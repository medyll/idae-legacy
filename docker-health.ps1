# Docker Health Check
# Usage: .\docker-health.ps1

Write-Host "🏥 Docker Health Check" -ForegroundColor Cyan
Write-Host "=====================`n" -ForegroundColor Cyan

# 1. Container status
Write-Host "1️⃣ Container Status:" -ForegroundColor Yellow
$status = docker ps --filter "name=idae-legacy" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
Write-Host $status

# 2. Health status
Write-Host "`n2️⃣ Health Status:" -ForegroundColor Yellow
$health = docker inspect idae-legacy --format='{{.State.Health.Status}}' 2>&1
$healthColor = if ($health -eq 'healthy') { 'Green' } else { 'Red' }
Write-Host "  $health" -ForegroundColor $healthColor

# 3. MongoDB connection
Write-Host "`n3️⃣ MongoDB Connection:" -ForegroundColor Yellow
try {
    $mongoTest = docker exec idae-legacy php /var/www/html/idae/web/check_mongo.php 2>&1
    if ($mongoTest -match "\[OK\]") {
        Write-Host "  ✅ MongoDB OK" -ForegroundColor Green
    } else {
        Write-Host "  ❌ MongoDB issue" -ForegroundColor Red
        Write-Host $mongoTest
    }
} catch {
    Write-Host "  ❌ Cannot test MongoDB: $_" -ForegroundColor Red
}

# 4. HTTP response
Write-Host "`n4️⃣ HTTP Response:" -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri http://localhost:8080/idae/web/index.php -UseBasicParsing -TimeoutSec 5 -MaximumRedirection 0 -ErrorAction SilentlyContinue
    if ($response.StatusCode -eq 200) {
        Write-Host "  ✅ HTTP 200 OK" -ForegroundColor Green
    } else {
        Write-Host "  ⚠️ HTTP $($response.StatusCode)" -ForegroundColor Yellow
    }
} catch {
    # Check if it's a redirect
    if ($_.Exception.Response.StatusCode -eq 302) {
        $location = $_.Exception.Response.Headers['Location']
        Write-Host "  🔀 Redirect to: $location" -ForegroundColor Yellow
    } else {
        Write-Host "  ❌ HTTP error: $_" -ForegroundColor Red
    }
}

# 5. Recent errors
Write-Host "`n5️⃣ Recent Errors (last 20 lines):" -ForegroundColor Yellow
$errors = docker logs --tail 20 idae-legacy 2>&1 | Select-String -Pattern "error|Error|ERROR|fail|warning"
if ($errors) {
    $errors | ForEach-Object { Write-Host "  $_" -ForegroundColor Red }
} else {
    Write-Host "  ✅ No errors" -ForegroundColor Green
}

Write-Host "`n✨ Health check complete" -ForegroundColor Cyan
