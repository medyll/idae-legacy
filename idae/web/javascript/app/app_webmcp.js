/*
 * WebMCP tool bridge: exposes schema-driven entity read/write as navigator.modelContext tools.
 * Date: 2026-07-19
 * Modified: 2026-08-05 — scope the advertised tables to the agent's actual rights
 *                        (services/json_droit_table.php); server-side gate lives in
 *                        droit_table_enforce(). This is the cosmetic half: it keeps the
 *                        model from proposing calls it cannot make.
 * Modified: 2026-08-05 — MCP error responses, bounded page size, mutate goes through
 *                        services/json_action.php (JSON) instead of the HTML+JS blob
 *                        mdl/app/actions.php returns.
 */

(function () {
	'use strict';

	if (!navigator.modelContext || typeof navigator.modelContext.addTool !== 'function') {
		return;
	}

	// A model that asks for "all the products" would otherwise pull the legacy 1000-row
	// default into its context window and drown in it. Page instead, and say so.
	var ROWS_DEFAULT = 50;
	var ROWS_MAX     = 200;

	function ucfirst(str) {
		return str.charAt(0).toUpperCase() + str.slice(1);
	}

	function mcpText(text) {
		return { content: [{ type: 'text', text: text }] };
	}

	function mcpError(text) {
		return { isError: true, content: [{ type: 'text', text: text }] };
	}

	function parseJson(raw) {
		return typeof raw === 'string' ? JSON.parse(raw) : raw;
	}

	// window.APP.APPSCHEMES is populated once by app_bootstrap.js's schemeLoad(), asynchronously,
	// after the whole require_trame queue drains — no dedicated ready-event exists, so poll for it.
	function waitForSchemes(timeoutMs) {
		var start = Date.now();
		return new RSVP.Promise(function (resolve, reject) {
			(function check() {
				var codes = window.APP && window.APP.APPSCHEMES ? Object.keys(window.APP.APPSCHEMES) : [];
				if (codes.length > 0) {
					resolve(codes);
				} else if (Date.now() - start > timeoutMs) {
					reject(new Error('APPSCHEMES not populated within timeout'));
				} else {
					setTimeout(check, 200);
				}
			})();
		});
	}

	function loadDroits() {
		return get_data('json_droit_table', {}).then(function (raw) {
			var res = parseJson(raw);
			return {
				R: res.R || [],
				C: res.C || [],
				U: res.U || [],
				D: res.D || []
			};
		});
	}

	// Tables the agent may read, and — separately — those it may write.
	// A table absent from every write list must not appear in the mutate tool's enum.
	function intersect(schemeCodes, allowed) {
		return schemeCodes.filter(function (code) {
			return allowed.indexOf(code) !== -1;
		});
	}

	function registerTools(readCodes, writeCodes, droits) {

		// An empty enum would advertise a tool no call can satisfy — skip it instead.
		if (readCodes.length > 0) navigator.modelContext.addTool({
			name: 'idae-query-entity',
			description: 'Read a page of records from an Idae schema table (produit, agent, etc.). '
				+ 'Returns {count, page, nbRows, truncated, rows}: count is the total matching the '
				+ 'filter, rows only the requested page. Raise page to walk the rest.',
			annotations: {
				readOnlyHint: true
			},
			inputSchema: {
				type: 'object',
				properties: {
					table: { type: 'string', enum: readCodes, description: 'codeAppscheme of the table to query' },
					vars: { type: 'object', description: 'Mongo-style filter fields, keyed by <field><Table> name' },
					search: { type: 'string', description: 'Free-text search across the table display fields' },
					sortBy: { type: 'string', description: 'Field name to sort by' },
					sortDir: { type: 'string', enum: ['asc', 'desc'], description: 'Sort direction' },
					page: { type: 'number', minimum: 1, description: '1-based page number' },
					nbRows: {
						type: 'number',
						minimum: 1,
						maximum: ROWS_MAX,
						description: 'Rows per page (default ' + ROWS_DEFAULT + ', max ' + ROWS_MAX + ')'
					}
				},
				required: ['table']
			},
			handler: async function (args) {
				var nbRows = Math.min(Math.max(parseInt(args.nbRows, 10) || ROWS_DEFAULT, 1), ROWS_MAX);
				var page   = Math.max(parseInt(args.page, 10) || 1, 1);
				var payload = {
					table: args.table,
					// piece=query wraps the rows in {count, maxcount, rs} — the row page alone
					// gives the model no way to tell a complete answer from a truncated one.
					piece: 'query',
					vars: args.vars || {},
					page: page,
					nbRows: nbRows
				};
				if (args.sortBy) {
					payload.sortBy  = args.sortBy;
					payload.sortDir = args.sortDir === 'desc' ? -1 : 1;
				}
				if (args.search) payload.search = args.search;

				var res;
				try {
					res = parseJson(await get_data('json_data', payload));
				} catch (e) {
					return mcpError('Query failed on table ' + args.table + ': ' + e.message);
				}
				if (!res || typeof res !== 'object') {
					return mcpError('Query returned no parseable payload for table ' + args.table);
				}

				var rows  = res.rs || [];
				// res.count is the page size; res.total is the full match count.
				var total = typeof res.total === 'number' ? res.total : rows.length;
				return mcpText(JSON.stringify({
					count: total,
					page: page,
					nbRows: nbRows,
					truncated: total > page * nbRows,
					rows: rows
				}));
			}
		});

		if (writeCodes.length > 0) navigator.modelContext.addTool({
			name: 'idae-mutate-entity',
			description: 'Create, update or delete a record on an Idae schema table. '
				+ 'delete is not reversible from here: the record moves to the trash collection '
				+ 'and disappears from the application.',
			annotations: {
				readOnlyHint: false,
				destructiveHint: true,
				idempotentHint: false
			},
			inputSchema: {
				type: 'object',
				properties: {
					action: { type: 'string', enum: ['create', 'update', 'delete'] },
					table: { type: 'string', enum: writeCodes, description: 'codeAppscheme of the table to mutate' },
					table_value: { type: 'string', description: 'Record id — required for update and delete' },
					vars: { type: 'object', description: 'Field values keyed by <field><Table> name — required for create and update' }
				},
				required: ['action', 'table']
			},
			// Authorization is enforced server-side by droit_table_enforce(); the checks
			// below only turn silent no-ops into readable answers.
			handler: async function (args) {
				var codeByAction = { create: 'C', update: 'U', delete: 'D' };
				var allowed = droits[codeByAction[args.action]] || [];
				if (allowed.indexOf(args.table) === -1) {
					return mcpError('Not authorized: ' + args.action + ' on table ' + args.table);
				}
				// Conditional requirements JSON Schema cannot express in one `required` list.
				if (args.action !== 'create' && !args.table_value) {
					return mcpError('table_value is required for action ' + args.action);
				}
				if (args.action !== 'delete' && (!args.vars || Object.keys(args.vars).length === 0)) {
					return mcpError('vars is required for action ' + args.action);
				}

				var params = new URLSearchParams();
				params.append('action', args.action);
				params.append('table', args.table);
				if (args.table_value) {
					params.append('table_value', args.table_value);
					params.append('vars[id' + ucfirst(args.table) + ']', args.table_value);
				}
				var vars = args.vars || {};
				Object.keys(vars).forEach(function (key) {
					params.append('vars[' + key + ']', vars[key]);
				});
				params.append('_csrf', (window.APP && window.APP.CSRF_TOKEN) || '');

				var res;
				try {
					var response = await fetch('services/json_action.php', {
						method: 'POST',
						headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
						body: params.toString()
					});
					var text = await response.text();
					if (!response.ok) {
						return mcpError('HTTP ' + response.status + ' from json_action.php: ' + text.slice(0, 200));
					}
					res = JSON.parse(text);
				} catch (e) {
					return mcpError(args.action + ' failed on table ' + args.table + ': ' + e.message);
				}

				if (!res.ok) {
					return mcpError(args.action + ' refused on table ' + args.table + ': ' + (res.error || 'unknown reason'));
				}
				return mcpText(JSON.stringify(res));
			}
		});
	}

	waitForSchemes(20000).then(function (schemeCodes) {
		return loadDroits().then(function (droits) {
			var readCodes  = intersect(schemeCodes, droits.R);
			var writeCodes = intersect(schemeCodes, droits.C.concat(droits.U, droits.D));
			if (readCodes.length === 0 && writeCodes.length === 0) {
				console.warn('[app_webmcp] no table permitted for this agent — no tool registered');
				return;
			}
			registerTools(readCodes, writeCodes, droits);
		});
	}).catch(function (e) {
		console.error('[app_webmcp] tool registration skipped:', e.message);
	});
})();
