#!/usr/bin/env bash
# =============================================================================
# backup_mongo.sh — Daily MongoDB backup with 7-day rolling retention
#
# Usage:
#   ./bin/backup_mongo.sh [--dry-run]
#
# Schedule (cron inside container or on host — runs daily at 02:00):
#   0 2 * * * /var/www/html/idae/web/bin/backup_mongo.sh >> /var/www/html/idae/web/mongo_backup/backup.log 2>&1
#
# Environment variables (read from shell or .env):
#   MONGO_HOST       — host to back up (default: host.docker.internal)
#   MONGO_HOST_PORT  — port            (default: 27017)
#   MDB_USER         — username        (optional, skipped if empty)
#   MDB_PASSWORD     — password        (optional, skipped if empty)
#   MDB_PREFIX       — db name prefix  (default: empty)
#   BACKUP_DIR       — output dir      (default: <script_dir>/../mongo_backup)
#   RETENTION_DAYS   — days to keep    (default: 7)
#
# Date: 2026-03-02
# =============================================================================

set -euo pipefail

# ---------------------------------------------------------------------------
# Config (env overrides with fallback defaults)
# ---------------------------------------------------------------------------
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKUP_DIR="${BACKUP_DIR:-$SCRIPT_DIR/../mongo_backup}"
RETENTION_DAYS="${RETENTION_DAYS:-7}"

MONGO_HOST="${MONGO_HOST:-host.docker.internal}"
MONGO_HOST_PORT="${MONGO_HOST_PORT:-27017}"
MDB_USER="${MDB_USER:-}"
MDB_PASSWORD="${MDB_PASSWORD:-}"
MDB_PREFIX="${MDB_PREFIX:-}"

DRY_RUN=0
[[ "${1:-}" == "--dry-run" ]] && DRY_RUN=1

# ---------------------------------------------------------------------------
# Logging helper
# ---------------------------------------------------------------------------
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $*"
}

# ---------------------------------------------------------------------------
# Safety guard — never back up the test sidecar
# ---------------------------------------------------------------------------
MONGO_ENV="${MONGO_ENV:-dev}"
if [[ "$MONGO_ENV" == "test" ]]; then
    log "ERROR: MONGO_ENV=test — refusing to run backup against the test sidecar."
    exit 1
fi

# ---------------------------------------------------------------------------
# Prepare output directory
# ---------------------------------------------------------------------------
TIMESTAMP="$(date '+%Y%m%d_%H%M%S')"
TARGET_DIR="$BACKUP_DIR/$TIMESTAMP"

log "=== Idae MongoDB Backup ==="
log "Host      : $MONGO_HOST:$MONGO_HOST_PORT"
log "Prefix    : '${MDB_PREFIX}'"
log "Target    : $TARGET_DIR"
log "Retention : ${RETENTION_DAYS} days"
[[ $DRY_RUN -eq 1 ]] && log "(DRY RUN — no files will be written)"

if [[ $DRY_RUN -eq 0 ]]; then
    mkdir -p "$TARGET_DIR"
fi

# ---------------------------------------------------------------------------
# Build mongodump arguments
# ---------------------------------------------------------------------------
DUMP_ARGS=(
    "--host=$MONGO_HOST"
    "--port=$MONGO_HOST_PORT"
    "--out=$TARGET_DIR"
)

if [[ -n "$MDB_USER" && -n "$MDB_PASSWORD" ]]; then
    DUMP_ARGS+=( "--username=$MDB_USER" "--password=$MDB_PASSWORD" "--authenticationDatabase=admin" )
fi

# Back up only prefixed databases if MDB_PREFIX is set; otherwise dump all
if [[ -n "$MDB_PREFIX" ]]; then
    for db_suffix in sitebase_app sitebase_sockets; do
        DUMP_ARGS+=( "--db=${MDB_PREFIX}${db_suffix}" )
    done
fi

# ---------------------------------------------------------------------------
# Run dump
# ---------------------------------------------------------------------------
if [[ $DRY_RUN -eq 1 ]]; then
    log "DRY RUN — would execute: mongodump ${DUMP_ARGS[*]}"
else
    if ! command -v mongodump &>/dev/null; then
        log "ERROR: mongodump not found in PATH. Install mongodb-database-tools."
        exit 2
    fi

    log "Starting mongodump..."
    if mongodump "${DUMP_ARGS[@]}"; then
        DUMP_SIZE="$(du -sh "$TARGET_DIR" 2>/dev/null | cut -f1 || echo 'unknown')"
        log "Dump complete. Size: $DUMP_SIZE"
    else
        log "ERROR: mongodump failed (exit $?)."
        rm -rf "$TARGET_DIR"
        exit 3
    fi
fi

# ---------------------------------------------------------------------------
# 7-day rolling retention — delete backups older than RETENTION_DAYS
# ---------------------------------------------------------------------------
log "Pruning backups older than ${RETENTION_DAYS} days..."

PRUNED=0
while IFS= read -r -d '' old_dir; do
    if [[ $DRY_RUN -eq 1 ]]; then
        log "DRY RUN — would delete: $old_dir"
    else
        rm -rf "$old_dir"
        log "Deleted: $old_dir"
    fi
    (( PRUNED++ )) || true
done < <(find "$BACKUP_DIR" -mindepth 1 -maxdepth 1 -type d -mtime "+${RETENTION_DAYS}" -print0 2>/dev/null)

log "Pruning done. Removed $PRUNED old backup(s)."
log "=== Backup finished successfully ==="
