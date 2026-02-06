# Docker Apache Restart Script
# Usage: .\docker-restart.ps1 [apache|container|full]

param(
    [ValidateSet('apache', 'container', 'full')]
    [string]$Mode = 'apache'
)

Write-Host "🔄 Restarting $Mode..." -ForegroundColor Cyan

switch ($Mode) {
    'apache' {
        Write-Host "Graceful Apache reload..." -ForegroundColor Yellow
        docker exec idae-legacy apachectl graceful
        Start-Sleep -Seconds 2
        $status = docker exec idae-legacy apachectl status 2>&1
        if ($LASTEXITCODE -eq 0) {
            Write-Host "✅ Apache restarted successfully" -ForegroundColor Green
        } else {
            Write-Host "⚠️ Apache restart completed (check logs if issues)" -ForegroundColor Yellow
        }
    }
    
    'container' {
        Write-Host "Restarting container..." -ForegroundColor Yellow
        docker compose restart
        Start-Sleep -Seconds 5
        $health = docker inspect idae-legacy --format='{{.State.Health.Status}}' 2>&1
        if ($health -eq 'healthy') {
            Write-Host "✅ Container healthy" -ForegroundColor Green
        } else {
            Write-Host "⏳ Container starting (health: $health)" -ForegroundColor Yellow
        }
    }
    
    'full' {
        Write-Host "Full restart (down + up)..." -ForegroundColor Yellow
        docker compose down
        Start-Sleep -Seconds 2
        docker compose up -d
        Start-Sleep -Seconds 10
        
        $health = docker inspect idae-legacy --format='{{.State.Health.Status}}' 2>&1
        Write-Host "Container health: $health" -ForegroundColor Cyan
        
        # Test HTTP
        try {
            $response = Invoke-WebRequest -Uri http://localhost:8080/idae/web/index.php -UseBasicParsing -TimeoutSec 5
            if ($response.StatusCode -eq 200) {
                Write-Host "✅ HTTP 200 OK" -ForegroundColor Green
            } else {
                Write-Host "⚠️ HTTP $($response.StatusCode)" -ForegroundColor Yellow
            }
        } catch {
            Write-Host "❌ HTTP test failed: $_" -ForegroundColor Red
        }
    }
}

# Show recent logs
Write-Host "`n📋 Recent logs:" -ForegroundColor Cyan
docker logs --tail 10 idae-legacy 2>&1 | Select-Object -Last 10
