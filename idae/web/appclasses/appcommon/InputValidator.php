<?php
declare(strict_types=1);

/**
 * InputValidator.php — Input validation and sanitization helpers
 *
 * Provides common validation methods for user input with secure defaults.
 * All methods return sanitized values or throw InvalidArgumentException on invalid input.
 *
 * @package AppCommon
 * Date: 2026-03-27
 */

namespace AppCommon;

class InputValidator
{
    /**
     * Validate and sanitize an integer.
     *
     * @param mixed $value Value to validate
     * @param int $min Minimum value (optional)
     * @param int $max Maximum value (optional)
     * @return int Sanitized integer
     * @throws \InvalidArgumentException if value cannot be converted to int or out of range
     */
    public static function int($value, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): int
    {
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException('Value must be numeric');
        }

        $int = (int)$value;

        if ($int < $min || $int > $max) {
            throw new \InvalidArgumentException("Value must be between $min and $max");
        }

        return $int;
    }

    /**
     * Validate and sanitize a string.
     *
     * @param mixed $value Value to validate
     * @param int $minLength Minimum length (optional)
     * @param int $maxLength Maximum length (optional)
     * @param bool $allowEmpty Allow empty string
     * @return string Sanitized string
     * @throws \InvalidArgumentException if value is not a string or length constraints not met
     */
    public static function string(
        $value,
        int $minLength = 0,
        int $maxLength = PHP_INT_MAX,
        bool $allowEmpty = true
    ): string {
        if (!is_string($value)) {
            $value = (string)$value;
        }

        $trimmed = trim($value);

        if (!$allowEmpty && $trimmed === '') {
            throw new \InvalidArgumentException('Value cannot be empty');
        }

        $len = mb_strlen($trimmed, 'UTF-8');
        if ($len < $minLength || $len > $maxLength) {
            throw new \InvalidArgumentException("String length must be between $minLength and $maxLength");
        }

        return $trimmed;
    }

    /**
     * Validate and sanitize an email address.
     *
     * @param mixed $value Value to validate
     * @param bool $allowEmpty Allow empty value
     * @return string Sanitized email
     * @throws \InvalidArgumentException if email format is invalid
     */
    public static function email($value, bool $allowEmpty = true): string
    {
        if (!is_string($value)) {
            $value = (string)$value;
        }

        $email = trim($value);

        if ($email === '') {
            if (!$allowEmpty) {
                throw new \InvalidArgumentException('Email cannot be empty');
            }
            return '';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Validate and sanitize a URL.
     *
     * @param mixed $value Value to validate
     * @param array<string> $allowedProtocols Allowed protocols (default: http, https)
     * @param bool $allowEmpty Allow empty value
     * @return string Sanitized URL
     * @throws \InvalidArgumentException if URL format is invalid
     */
    public static function url(
        $value,
        array $allowedProtocols = ['http', 'https'],
        bool $allowEmpty = true
    ): string {
        if (!is_string($value)) {
            $value = (string)$value;
        }

        $url = trim($value);

        if ($url === '') {
            if (!$allowEmpty) {
                throw new \InvalidArgumentException('URL cannot be empty');
            }
            return '';
        }

        // Use filter_var with basic URL validation
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid URL format');
        }

        // Check protocol manually
        $parsed = parse_url($url);
        if (isset($parsed['scheme']) && !in_array(strtolower($parsed['scheme']), $allowedProtocols)) {
            throw new \InvalidArgumentException('Protocol not allowed');
        }

        return filter_var($url, FILTER_SANITIZE_URL);
    }

    /**
     * Validate a boolean value.
     *
     * @param mixed $value Value to validate
     * @param bool $strict Strict mode (only accept true booleans)
     * @return bool Boolean value
     * @throws \InvalidArgumentException if value cannot be converted to bool in strict mode
     */
    public static function bool($value, bool $strict = false): bool
    {
        if ($strict) {
            if (!is_bool($value)) {
                throw new \InvalidArgumentException('Value must be a boolean');
            }
            return $value;
        }

        // Lenient mode: accept various truthy/falsy values
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (bool)$value;
        }

        if (is_string($value)) {
            $lower = strtolower(trim($value));
            if (in_array($lower, ['1', 'true', 'yes', 'on'], true)) {
                return true;
            }
            if (in_array($lower, ['0', 'false', 'no', 'off', ''], true)) {
                return false;
            }
        }

        throw new \InvalidArgumentException('Value cannot be converted to boolean');
    }

    /**
     * Validate a date string.
     *
     * @param mixed $value Value to validate
     * @param string $format Expected format (default: Y-m-d)
     * @param bool $allowEmpty Allow empty value
     * @return string Date in specified format
     * @throws \InvalidArgumentException if date format is invalid
     */
    public static function date($value, string $format = 'Y-m-d', bool $allowEmpty = true): string
    {
        if (!is_string($value)) {
            $value = (string)$value;
        }

        $date = trim($value);

        if ($date === '') {
            if (!$allowEmpty) {
                throw new \InvalidArgumentException('Date cannot be empty');
            }
            return '';
        }

        $dateTime = \DateTime::createFromFormat($format, $date);

        if (!$dateTime || $dateTime->format($format) !== $date) {
            throw new \InvalidArgumentException("Invalid date format. Expected: $format");
        }

        return $dateTime->format($format);
    }

    /**
     * Validate an alphanumeric string.
     *
     * @param mixed $value Value to validate
     * @param int $minLength Minimum length
     * @param int $maxLength Maximum length
     * @param bool $allowUnderscore Allow underscores
     * @return string Sanitized alphanumeric string
     * @throws \InvalidArgumentException if value contains invalid characters
     */
    public static function alphanumeric(
        $value,
        int $minLength = 1,
        int $maxLength = 255,
        bool $allowUnderscore = false
    ): string {
        $string = self::string($value, $minLength, $maxLength, false);

        $pattern = $allowUnderscore ? '/^[a-zA-Z0-9_]+$/' : '/^[a-zA-Z0-9]+$/';

        if (!preg_match($pattern, $string)) {
            throw new \InvalidArgumentException('Value must be alphanumeric' . ($allowUnderscore ? ' with underscores allowed' : ''));
        }

        return $string;
    }

    /**
     * Validate an array with optional element validation.
     *
     * @param mixed $value Value to validate
     * @param callable|null $elementValidator Function to validate each element (optional)
     * @param int $minCount Minimum number of elements
     * @param int $maxCount Maximum number of elements
     * @return array Sanitized array
     * @throws \InvalidArgumentException if value is not an array or element validation fails
     */
    public static function array(
        $value,
        ?callable $elementValidator = null,
        int $minCount = 0,
        int $maxCount = PHP_INT_MAX
    ): array {
        if (!is_array($value)) {
            throw new \InvalidArgumentException('Value must be an array');
        }

        $count = count($value);
        if ($count < $minCount || $count > $maxCount) {
            throw new \InvalidArgumentException("Array must have between $minCount and $maxCount elements");
        }

        if ($elementValidator !== null) {
            foreach ($value as $key => $element) {
                $value[$key] = $elementValidator($element);
            }
        }

        return $value;
    }

    /**
     * Validate a value against a whitelist.
     *
     * @param mixed $value Value to validate
     * @param array<int|string, mixed> $allowedValues List of allowed values
     * @param bool $strict Use strict comparison (===)
     * @return mixed The validated value
     * @throws \InvalidArgumentException if value is not in whitelist
     */
    public static function whitelist($value, array $allowedValues, bool $strict = false)
    {
        $found = false;

        foreach ($allowedValues as $allowed) {
            if ($strict) {
                if ($value === $allowed) {
                    $found = true;
                    break;
                }
            } else {
                if ($value == $allowed) {
                    $found = true;
                    break;
                }
            }
        }

        if (!$found) {
            $allowedStr = implode(', ', array_map(fn($v) => is_string($v) ? "\"$v\"" : (string)$v, $allowedValues));
            throw new \InvalidArgumentException("Value must be one of: $allowedStr");
        }

        return $value;
    }

    /**
     * Sanitize HTML by stripping all tags.
     *
     * @param string $value HTML to sanitize
     * @param bool $allowEmpty Allow empty value
     * @return string Sanitized text
     */
    public static function stripHtml(string $value, bool $allowEmpty = true): string
    {
        $trimmed = trim(strip_tags($value));

        if ($trimmed === '' && !$allowEmpty) {
            throw new \InvalidArgumentException('Value cannot be empty after stripping HTML');
        }

        return $trimmed;
    }

    /**
     * Validate a filename (no path traversal).
     *
     * @param mixed $value Filename to validate
     * @return string Sanitized filename
     * @throws \InvalidArgumentException if filename contains path traversal or invalid characters
     */
    public static function filename($value): string
    {
        $filename = self::string($value, 1, 255, false);

        // Check for path traversal
        if (strpos($filename, '..') !== false || strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
            throw new \InvalidArgumentException('Invalid filename: path traversal not allowed');
        }

        // Check for null byte
        if (strpos($filename, "\0") !== false) {
            throw new \InvalidArgumentException('Invalid filename: null byte not allowed');
        }

        return basename($filename);
    }
}
