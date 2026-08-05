/*
 * WebMCP tool bridge: exposes schema-driven entity read/write as navigator.modelContext tools.
 * Date: 2026-07-19
 * Modified: 2026-08-05 — scope the advertised tables to the agent's actual rights
 *                        (services/json_droit_table.php); server-side gate lives in
 *                        ClassAction::droit_ok(). This is the cosmetic half: it keeps the
 *                        model from proposing calls it cannot make.
 */

(function () {
	'use strict';

	if (!navigator.modelContext || typeof navigator.modelContext.addTool !== 'function') {
		return;
	}

	function ucfirst(str) {
		return str.charAt(0).toUpperCase() + str.slice(1);
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
			var res = typeof raw === 'string' ? JSON.parse(raw) : raw;
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
			description: 'Read a list of records for a given Idae schema table (produit, agent, etc.)',
			inputSchema: {
				type: 'object',
				properties: {
					table: { type: 'string', enum: readCodes, description: 'codeAppscheme of the table to query' },
					vars: { type: 'object', description: 'Mongo-style filter fields, keyed by <field><Table> name' },
					search: { type: 'string', description: 'Free-text search across the table display fields' },
					sortBy: { type: 'string', description: 'Field name to sort by' },
					sortDir: { type: 'string', enum: ['asc', 'desc'], description: 'Sort direction' },
					page: { type: 'number', description: '1-based page number' },
					nbRows: { type: 'number', description: 'Rows per page (default 1000)' }
				},
				required: ['table']
			},
			handler: async function (args) {
				var payload = {
					table: args.table,
					vars: args.vars || {},
					sortBy: args.sortBy,
					sortDir: args.sortDir === 'desc' ? -1 : 1,
					page: args.page || 1,
					nbRows: args.nbRows || 1000
				};
				if (args.search) payload.search = args.search;

				var raw = await get_data('json_data', payload);
				var data;
				try {
					data = JSON.parse(raw);
				} catch (e) {
					data = raw;
				}
				return { content: [{ type: 'text', text: JSON.stringify(data) }] };
			}
		});

		if (writeCodes.length > 0) navigator.modelContext.addTool({
			name: 'idae-mutate-entity',
			description: 'Create, update or delete a record on a given Idae schema table',
			inputSchema: {
				type: 'object',
				properties: {
					action: { type: 'string', enum: ['create', 'update', 'delete'] },
					table: { type: 'string', enum: writeCodes, description: 'codeAppscheme of the table to mutate' },
					table_value: { type: 'string', description: 'Record id (required for update/delete)' },
					vars: { type: 'object', description: 'Field values keyed by <field><Table> name (required for create/update)' }
				},
				required: ['action', 'table']
			},
			// Authorization is enforced server-side by ClassAction::droit_ok(); the check
			// below only turns a silent server-side no-op into a readable answer.
			handler: async function (args) {
				var codeByAction = { create: 'C', update: 'U', delete: 'D' };
				var allowed = droits[codeByAction[args.action]] || [];
				if (allowed.indexOf(args.table) === -1) {
					return {
						isError: true,
						content: [{ type: 'text', text: 'Not authorized: ' + args.action + ' on table ' + args.table }]
					};
				}

				var params = new URLSearchParams();
				params.append('F_action', 'app_' + args.action);
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

				var response = await fetch('mdl/app/actions.php', {
					method: 'POST',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
					body: params.toString()
				});
				var text = await response.text();
				return { content: [{ type: 'text', text: text }] };
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
