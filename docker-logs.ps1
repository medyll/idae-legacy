# Docker Logs Helper
# Usage: .\docker-logs.ps1 [-Follow] [-Errors] [-Session]

param(
    [switch]$Follow,
    [switch]$Errors,
    [switch]$Session,
    [int]$Lines = 50
)

if ($Follow) {
    Write-Host "📋 Following logs (Ctrl+C to stop)..." -ForegroundColor Cyan
    docker logs -f idae-legacy
} elseif ($Errors) {
    Write-Host "❌ Error logs (last $Lines lines):" -ForegroundColor Red
    docker logs --tail $Lines idae-legacy 2>&1 | Select-String -Pattern "error|Error|ERROR|fail|Fail|FAIL|fatal|Fatal|FATAL"
} elseif ($Session) {
    Write-Host "🔐 Session logs (last $Lines lines):" -ForegroundColor Yellow
    docker logs --tail $Lines idae-legacy 2>&1 | Select-String -Pattern "Session|session|reindex|retry"
} else {
    Write-Host "📋 Recent logs (last $Lines lines):" -ForegroundColor Cyan
    docker logs --tail $Lines idae-legacy
}
