#!/usr/bin/env bash
# =============================================================================
# pre-commit — PHPStan level-6 check on staged PHP files only
#
# Install: cp .git/hooks/pre-commit .git/hooks/pre-commit && chmod +x .git/hooks/pre-commit
# (git init hooks are per-repo; this file lives in idae/web/ for reference
#  and is symlinked/copied by the developer or by a composer post-install script)
#
# Date: 2026-03-02
# =============================================================================

set -euo pipefail

# Resolve paths relative to the repo root (works from any sub-directory)
REPO_ROOT="$(git rev-parse --show-toplevel)"
WEB_DIR="$REPO_ROOT/idae/web"
PHPSTAN="$WEB_DIR/vendor/bin/phpstan"

# Collect staged PHP files that actually exist (not deleted)
STAGED=$(git diff --cached --name-only --diff-filter=ACMR | grep '\.php$' || true)

if [[ -z "$STAGED" ]]; then
    exit 0   # no PHP files staged — nothing to check
fi

if [[ ! -x "$PHPSTAN" ]]; then
    echo "[pre-commit] PHPStan not found at $PHPSTAN"
    echo "             Run: cd idae/web && composer install"
    exit 1
fi

# Build absolute paths for staged files
PHP_FILES=()
while IFS= read -r file; do
    abs="$REPO_ROOT/$file"
    [[ -f "$abs" ]] && PHP_FILES+=("$abs")
done <<< "$STAGED"

if [[ ${#PHP_FILES[@]} -eq 0 ]]; then
    exit 0
fi

echo "[pre-commit] PHPStan level 6 — checking ${#PHP_FILES[@]} staged file(s)..."

cd "$WEB_DIR"
if "$PHPSTAN" analyse --configuration=phpstan.neon --no-progress "${PHP_FILES[@]}"; then
    echo "[pre-commit] PHPStan — OK"
    exit 0
else
    echo ""
    echo "[pre-commit] PHPStan found violations. Commit blocked."
    echo "             Fix the issues above, then re-stage and commit."
    echo "             To regenerate the baseline: vendor/bin/phpstan analyse --generate-baseline"
    exit 1
fi
