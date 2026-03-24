# Docker Logs Helper
# Usage: .\docker-logs.ps1 [-Follow] [-Errors] [-Session]

param(
    [ValidateSet('apache','socket','all')]
    [string]$Mode = 'all',
    [int]$Lines = 200
)

switch ($Mode) {
    'apache' {
        Write-Host "Apache recent logs (last $Lines lines):" -ForegroundColor Cyan
        if (Test-Path .\logs\php-error.log) { Get-Content .\logs\php-error.log -Tail $Lines } else { docker logs --tail $Lines idae-legacy }
    }

    'socket' {
        Write-Host "Socket recent logs (last $Lines lines):" -ForegroundColor Cyan
        if (Test-Path .\logs\socket.log) {
            Get-Content .\logs\socket.log -Tail $Lines
        } else {
            docker logs --tail $Lines idae-socket
        }
    }

    'all' {
        Write-Host "Combined recent logs:" -ForegroundColor Cyan
        ./docker-logs.ps1 apache -Lines $Lines
        Write-Host '--- SOCKET ---' -ForegroundColor Cyan
        ./docker-logs.ps1 socket -Lines $Lines
    }
}

