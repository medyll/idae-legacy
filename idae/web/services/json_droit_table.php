<?php
declare(strict_types=1);
/**
 * json_droit_table.php — Returns the tables the current agent may read/write.
 * Consumed by the WebMCP bridge (javascript/app/app_webmcp.js) so the tools it
 * registers only advertise tables the agent is actually allowed to touch.
 *
 * Response: {"admin":bool,"R":[...],"C":[...],"U":[...],"D":[...]}
 * An `admin` agent gets every codeAppscheme, matching droit_ok() in ClassAction.
 *
 * Date: 2026-08-05
 */

include_once($_SERVER['CONF_INC']);

header('Content-Type: application/json');

$idagent = empty($_SESSION['idagent']) ? 0 : (int)$_SESSION['idagent'];
if (empty($idagent)) {
	echo json_encode(['admin' => false, 'R' => [], 'C' => [], 'U' => [], 'D' => []]);
	return;
}

$is_admin = droit('ADMIN') || droit('DEV');

if ($is_admin) {
	$APP_SCH   = new App('appscheme');
	$all_codes = [];
	foreach ($APP_SCH->find([]) as $ARR_APP) {
		if (!empty($ARR_APP['codeAppscheme'])) {
			$all_codes[] = $ARR_APP['codeAppscheme'];
		}
	}
	echo json_encode([
		'admin' => true,
		'R'     => $all_codes,
		'C'     => $all_codes,
		'U'     => $all_codes,
		'D'     => $all_codes,
	]);

	return;
}

$out = ['admin' => false];
foreach (['R', 'C', 'U', 'D'] as $code) {
	$tables      = droit_table_multi($idagent, $code);
	$out[$code]  = empty($tables) ? [] : array_values((array)$tables);
}

echo json_encode($out);
