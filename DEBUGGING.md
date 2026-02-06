# Debugging Best Practices - Docker Environment

## ⚠️ CRITICAL - What NOT to do

### ❌ NEVER use `die()` or `exit()` in main flow
```php
// ❌ BAD - Causes incomplete HTTP response → browser hangs
if (empty($_SESSION['reindex'])) {
    die('died');  // Browser gets incomplete response, waits forever
}

// ✅ GOOD - Proper error page
if (empty($_SESSION['reindex'])) {
    http_response_code(500);
    echo '<!DOCTYPE html><html><body><h1>Session Error</h1></body></html>';
    exit;
}
```

**Why it's bad in Docker:**
- Browser receives incomplete response
- Keeps connection open indefinitely
- Apache worker stuck waiting
- New requests create more stuck workers
- Eventually: memory exhaustion, zombie processes, container crash

---

## ✅ Safe Debugging Techniques

### 1. Use error_log() instead of die()
```php
// ✅ Debug without breaking the page
error_log('[DEBUG] Session status: ' . session_status());
error_log('[DEBUG] $_SESSION contents: ' . print_r($_SESSION, true));
error_log('[DEBUG] reindex value: ' . ($_SESSION['reindex'] ?? 'NOT SET'));

// View logs:
// docker logs idae-legacy
// Or: .\docker-logs.ps1
```

### 2. Use environment-based debug output
```php
// ✅ Only show debug in PREPROD/LAN
if (defined('ENVIRONEMENT') && ENVIRONEMENT !== 'PROD') {
    echo "<!-- DEBUG: session_status=" . session_status() . " -->";
    echo "<!-- DEBUG: reindex=" . ($_SESSION['reindex'] ?? 'NOT SET') . " -->";
}
```

### 3. Use temporary debug endpoint
```php
// Create: debug_session.php
<?php
header('Content-Type: text/plain');
include_once($_SERVER['CONF_INC']);

echo "Session Status: " . session_status() . "\n";
echo "Session ID: " . session_id() . "\n";
echo "Session Data:\n";
print_r($_SESSION);
echo "\nEnvironment: " . (defined('ENVIRONEMENT') ? ENVIRONEMENT : 'NOT SET') . "\n";
exit;
?>

// Access: curl http://localhost:8080/idae/web/debug_session.php
```

### 4. Use X-Debug headers (production-safe)
```php
// ✅ Debug info in HTTP headers (invisible to users)
if (!headers_sent()) {
    header('X-Debug-Session-Status: ' . session_status());
    header('X-Debug-Session-ID: ' . session_id());
    header('X-Debug-Reindex: ' . ($_SESSION['reindex'] ?? 'NOT SET'));
}

// View headers:
// curl -I http://localhost:8080/idae/web/index.php
```

---

## 🔍 Debugging Workflows

### Debug Session Issues
```powershell
# 1. Enable session debug
$env:DEBUG_SESSION=1
docker compose restart

# 2. Follow logs in real-time
.\docker-logs.ps1 -Follow

# 3. Access page and watch logs
# Ctrl+C to stop following

# 4. Disable debug when done
Remove-Item Env:\DEBUG_SESSION
docker compose restart
```

### Debug MongoDB Connection
```powershell
# 1. Test MongoDB directly
docker exec idae-legacy php /var/www/html/idae/web/check_mongo.php

# 2. Check MongoDB logs (if running externally)
# Or check MongoDB status

# 3. Test connection timeout
# Edit ClassSession.php → increase connectTimeoutMS
```

### Debug Redirect Loops
```powershell
# 1. Check for retry parameter
curl -I http://localhost:8080/idae/web/index.php

# If you see: Location: reindex.php?retry=1
# → Session not initializing

# 2. Check session logs
.\docker-logs.ps1 -Session

# 3. Verify session write
docker exec idae-legacy php -r "
include '/var/www/html/idae/web/conf.inc.php';
echo 'Session status: ' . session_status() . PHP_EOL;
\$_SESSION['test'] = 'works';
echo 'Test value: ' . \$_SESSION['test'] . PHP_EOL;
"
```

### Debug Apache Hangs
```powershell
# 1. Check for zombie processes
docker exec idae-legacy ps aux | Select-String "defunct|Z"

# 2. Check Apache worker count
docker exec idae-legacy apachectl status

# 3. Check memory usage
docker exec idae-legacy ps aux --sort=-%mem | Select-Object -First 10

# 4. If zombies found → emergency restart
.\docker-emergency.ps1
```

---

## 📊 Performance Debugging

### Measure Page Load Time
```powershell
# Quick test
Measure-Command { 
    Invoke-WebRequest -Uri http://localhost:8080/idae/web/index.php -UseBasicParsing 
} | Select-Object TotalSeconds

# Expected: < 1 second for index.php
# If > 3 seconds: check MongoDB connection, session timeout
```

### Check MongoDB Query Performance
```php
// Add to debug endpoint
$start = microtime(true);
$app = new App('appscheme');
$result = $app->findOne(['codeAppscheme' => 'appscheme']);
$time = microtime(true) - $start;

echo "MongoDB query time: " . round($time * 1000, 2) . "ms\n";
// Expected: < 50ms for simple queries in Docker
```

---

## 🛠️ Recovery Procedures

### If Page Loads Infinitely
1. **Check browser network tab**: empty response? Status code?
2. **Check for `die()` in code**: search for `die(` in modified files
3. **Emergency restart**: `.\docker-emergency.ps1`
4. **Remove debug code**, restart Apache: `.\docker-restart.ps1 apache`

### If Docker Restart Hangs
1. **Kill from separate terminal**: `docker kill idae-legacy`
2. **If kill fails**: `wsl --shutdown` (Windows) or restart Docker Desktop
3. **Clean restart**: `.\docker-emergency.ps1`

### If Container Becomes Unhealthy
1. **Check logs**: `.\docker-logs.ps1 -Errors`
2. **Test MongoDB**: `docker exec idae-legacy php check_mongo.php`
3. **Restart**: `.\docker-restart.ps1 container`
4. **If still failing**: `.\docker-emergency.ps1`

---

## 📚 Debugging Tools Reference

### Built-in Scripts
```powershell
.\docker-emergency.ps1        # 🚨 Force reset (30s)
.\docker-restart.ps1 apache   # 🔄 Restart Apache (2s)
.\docker-health.ps1           # 🏥 Full diagnostic
.\docker-logs.ps1 -Errors     # 📋 Show errors only
.\docker-logs.ps1 -Session    # 📋 Show session logs
.\docker-logs.ps1 -Follow     # 📋 Live tail
```

### Manual Commands
```powershell
# Container status
docker ps -a
docker inspect idae-legacy --format='{{.State.Health.Status}}'

# Logs
docker logs idae-legacy
docker logs -f idae-legacy     # Follow
docker logs --tail 50 idae-legacy

# Apache
docker exec idae-legacy apachectl graceful
docker exec idae-legacy apachectl status

# Process inspection
docker exec idae-legacy ps aux
docker exec idae-legacy top -bn1

# File inspection
docker exec idae-legacy cat /var/www/html/idae/web/index.php
docker exec idae-legacy tail /var/log/apache2/error.log
```

---

## 🎯 Quick Checklist Before Debugging

- [ ] Remove any `die()` or `exit()` from code (except after proper HTML output)
- [ ] Check Git diff for recent changes: `git diff`
- [ ] Verify MongoDB is running (if external)
- [ ] Check container health: `docker ps`
- [ ] Check recent logs: `.\docker-logs.ps1 -Errors`
- [ ] If stuck: Use `.\docker-emergency.ps1` first, debug after

---

**Remember:**
1. **Docker isolation** makes debugging harder - use logs extensively
2. **Browser caching** can hide issues - hard refresh (Ctrl+F5)
3. **Session persistence** in MongoDB can cause old data - clear session collection if needed
4. **Volume mounts** make code changes instant - but Apache needs reload for some changes

---

**Created:** 2026-02-06  
**Context:** Emergency debugging session after `die()` caused infinite load + container crash
