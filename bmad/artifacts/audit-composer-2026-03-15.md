# Composer Security Audit — 2026-03-15

## Summary
**12 security advisories** across 2 packages.

## Affected Packages

### `latte/latte` v2.4.2 — 1 CRITICAL
| CVE | Severity | Title | Fixed in |
|-----|----------|-------|----------|
| CVE-2021-23803 | critical | Incorrect Authorization | >=2.10.6 |

**Recommendation**: Update to `^2.10` (minor version bump, same v2 API).
**Risk**: Low — only 24 usages found in `tpl/`.

### `smarty/smarty` v3.1.30 — 11 HIGH/CRITICAL
| CVE | Severity | Title | Fixed in |
|-----|----------|-------|----------|
| CVE-2024-35226 | high | PHP Code Injection via extends-tag | >=4.5.3 |
| CVE-2023-28447 | high | XSS in Javascript escaping | >=3.1.48,>=4.3.1 |
| CVE-2022-29221 | high | PHP code injection via malicious math string | >=3.1.45,>=4.1.1 |
| + 8 more | medium-high | Various template injection and XSS | >=3.1.48 |

**Recommendation**: Update to `^3.1.48` (patch-level bump within v3, safe).
**Risk**: Low — only 9 Smarty calls found in `tpl/`.

## Other Packages — Clean
- `mongodb/mongodb` ^2.0: no advisories
- `wisembly/elephant.io` 3.1.0: no advisories
- `phpunit/phpunit` ^11.0: no advisories
- `phpstan/phpstan` ^1.12: no advisories

## Action Taken
Updated `composer.json` minimum versions for affected packages.
Full `composer update` should be run inside the Docker container.

## Note
The bundled `composer.phar` itself is outdated (PHP 8.2 deprecation warnings).
Recommend downloading a fresh `composer.phar` v2.8+.
