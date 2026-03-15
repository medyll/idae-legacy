<?php
declare(strict_types=1);
/**
 * CsrfGuard — Session-based CSRF token generation and validation.
 *
 * Usage:
 *   CsrfGuard::init();              // call once per session (e.g. at login)
 *   $token = CsrfGuard::token();    // embed in forms / expose to JS
 *   CsrfGuard::validate($input);    // throws on mismatch
 *   CsrfGuard::validateOrDie();     // reads from $_POST['_csrf'] or X-CSRF-Token header
 *
 * Date: 2026-03-15
 */

namespace AppCommon;

class CsrfGuard
{
    private const SESSION_KEY = '_csrf_token';
    private const POST_FIELD  = '_csrf';
    private const HEADER_NAME = 'HTTP_X_CSRF_TOKEN';

    /**
     * Ensure a token exists in the session. Call during login or bootstrap.
     */
    public static function init(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
    }

    /**
     * Return the current CSRF token (or empty string if no session).
     */
    public static function token(): string
    {
        return $_SESSION[self::SESSION_KEY] ?? '';
    }

    /**
     * Validate a given token against the session token.
     *
     * @return bool true if valid
     */
    public static function validate(string $input): bool
    {
        $expected = self::token();
        if ($expected === '' || $input === '') {
            return false;
        }
        return hash_equals($expected, $input);
    }

    /**
     * Read the token from $_POST['_csrf'] or X-CSRF-Token header and validate.
     * Logs a warning and returns false on failure (does NOT exit — caller decides).
     *
     * @return bool true if the request carries a valid CSRF token
     */
    public static function check(): bool
    {
        $input = $_POST[self::POST_FIELD]
            ?? $_SERVER[self::HEADER_NAME]
            ?? '';

        if (!self::validate($input)) {
            error_log('[CsrfGuard] CSRF token mismatch for ' . ($_POST['F_action'] ?? 'unknown'));
            return false;
        }
        return true;
    }

    /**
     * Regenerate the token (e.g. after login to prevent session fixation).
     */
    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
    }
}
