# Docker Emergency Reset Script
# Usage: .\docker-emergency.ps1
# Use when: Container hangs, infinite loops, processes stuck

Write-Host "🚨 EMERGENCY DOCKER RESET" -ForegroundColor Red
Write-Host "========================`n" -ForegroundColor Red

# 1. Kill all docker processes
Write-Host "1️⃣ Killing container..." -ForegroundColor Yellow
docker kill idae-legacy 2>&1 | Out-Null
Start-Sleep -Seconds 1

# 2. Force remove
Write-Host "2️⃣ Removing container..." -ForegroundColor Yellow
docker rm -f idae-legacy 2>&1 | Out-Null
Start-Sleep -Seconds 1

# 3. Down everything
Write-Host "3️⃣ Docker compose down..." -ForegroundColor Yellow
docker compose down --remove-orphans 2>&1 | Out-Null
Start-Sleep -Seconds 2

# 4. Clean up if needed
Write-Host "4️⃣ Checking for zombie processes..." -ForegroundColor Yellow
$zombies = docker ps -a --filter "name=idae" --format "{{.ID}}"
if ($zombies) {
    Write-Host "   Found zombie containers, removing..." -ForegroundColor Yellow
    docker rm -f $zombies 2>&1 | Out-Null
}

# 5. Restart
Write-Host "5️⃣ Starting fresh container..." -ForegroundColor Yellow
docker compose up -d

# 6. Wait for health
Write-Host "6️⃣ Waiting for health check..." -ForegroundColor Yellow
$maxWait = 30
$waited = 0
$healthy = $false

while ($waited -lt $maxWait) {
    Start-Sleep -Seconds 2
    $waited += 2
    $health = docker inspect idae-legacy --format='{{.State.Health.Status}}' 2>&1
    
    if ($health -eq 'healthy') {
        $healthy = $true
        break
    }
    
    Write-Host "   ⏳ Waiting... ($waited/$maxWait s) - Status: $health" -ForegroundColor Gray
}

if ($healthy) {
    Write-Host "`n✅ Container is HEALTHY" -ForegroundColor Green
    
    # Test HTTP
    try {
        $response = Invoke-WebRequest -Uri http://localhost:8080/idae/web/index.php -UseBasicParsing -TimeoutSec 5
        $loadTime = (Measure-Command { 
            Invoke-WebRequest -Uri http://localhost:8080/idae/web/index.php -UseBasicParsing -TimeoutSec 5 
        }).TotalSeconds
        
        Write-Host "✅ HTTP 200 OK (Load time: $([math]::Round($loadTime, 2))s)" -ForegroundColor Green
    } catch {
        Write-Host "❌ HTTP test failed: $_" -ForegroundColor Red
    }
} else {
    Write-Host "`n❌ Container NOT healthy after ${maxWait}s" -ForegroundColor Red
    Write-Host "Check logs: docker logs idae-legacy" -ForegroundColor Yellow
}

Write-Host "`n✨ Emergency reset complete" -ForegroundColor Cyan
