<?php
declare(strict_types=1);
/**
 * json_action.php — JSON-speaking wrapper around the app_create/app_update/app_delete
 * handlers of ClassAction.
 *
 * mdl/app/actions.php answers with the HTML+JavaScript blob produced by postAction.php,
 * which the legacy SPA eval()s. Non-browser callers (the WebMCP bridge) need a machine
 * answer with an explicit success flag instead, so they go through here.
 *
 * POST: action=create|update|delete, table, table_value, vars[...]
 * Response: {"ok":bool,"action":…,"table":…,"table_value":…,"error":…}
 *
 * Date: 2026-08-05
 */

include_once($_SERVER['CONF_INC']);
require_once(__DIR__ . '/../appclasses/appcommon/CsrfGuard.php');

header('Content-Type: application/json');

/**
 * @param string $error machine-readable reason, empty when ok
 */
function json_action_out(bool $ok, string $error = '', array $extra = []): void
{
	echo json_encode(['ok' => $ok, 'error' => $error] + $extra);
}

\AppCommon\CsrfGuard::validateOrDie();

$action      = $_POST['action'] ?? '';
$table       = $_POST['table'] ?? '';
$table_value = $_POST['table_value'] ?? '';
$vars        = empty($_POST['vars']) ? [] : (array)$_POST['vars'];

$codes = ['create' => 'C', 'update' => 'U', 'delete' => 'D'];
if (!isset($codes[$action])) {
	json_action_out(false, 'unknown_action: ' . $action);

	return;
}
if (empty($table)) {
	json_action_out(false, 'missing_table');

	return;
}
// Arguments the underlying handlers silently no-op on when absent — reject up front
// so the caller gets a reason instead of a success-looking empty answer.
if ($action !== 'create' && empty($table_value)) {
	json_action_out(false, 'missing_table_value for action ' . $action);

	return;
}
if ($action !== 'delete' && empty($vars)) {
	json_action_out(false, 'missing_vars for action ' . $action);

	return;
}
if (!droit_table_enforce($codes[$action], $table)) {
	json_action_out(false, 'not_authorized: ' . $action . ' on ' . $table);

	return;
}

array_walk_recursive($_POST, 'CleanStr');

$ARGS = ['table' => $table, 'vars' => $vars];
if (!empty($table_value)) {
	$ARGS['table_value'] = $table_value;
	$ARGS['vars']['id' . $table] = $table_value;
}

// The SPA still expects the socket notifications the handlers emit — keep going
// through ClassAction rather than touching the collections directly.
$ACT    = new Action();
$method = 'app_' . $action;

try {
	$RES = $ACT->$method($ARGS);
} catch (\Throwable $e) {
	error_log('[json_action] ' . $method . ' on ' . $table . ' failed: ' . $e->getMessage());
	json_action_out(false, 'handler_error: ' . $e->getMessage());

	return;
}

if ($RES === false || $RES === null) {
	json_action_out(false, 'handler_refused');

	return;
}

json_action_out(true, '', [
	'action'      => $action,
	'table'       => $table,
	'table_value' => $RES['table_value'] ?? $table_value,
]);
