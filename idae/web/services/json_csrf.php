<?php
declare(strict_types=1);
/**
 * json_csrf.php — Returns the current CSRF token for the authenticated session.
 * Called by the frontend SPA during bootstrap to populate window.APP.CSRF_TOKEN.
 *
 * Date: 2026-03-15
 */
include_once($_SERVER['CONF_INC']);
require_once(__DIR__ . '/../appclasses/appcommon/CsrfGuard.php');

header('Content-Type: application/json');
echo json_encode(['token' => \AppCommon\CsrfGuard::token()]);
