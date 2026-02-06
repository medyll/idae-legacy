# Docker Troubleshooting Guide

## ⚡ Quick Scripts (PowerShell)

**🚨 URGENCE - Container bloqué / Chargement infini :**
```powershell
.\docker-emergency.ps1              # Force kill + clean restart
```

**Redémarrer Apache rapidement :**
```powershell
.\docker-restart.ps1                # Apache graceful reload (recommended)
.\docker-restart.ps1 container      # Restart container
.\docker-restart.ps1 full           # Full restart (down + up)
```

**Vérifier l'état du système :**
```powershell
.\docker-health.ps1                 # Full diagnostic
```

**Consulter les logs :**
```powershell
.\docker-logs.ps1                   # Recent logs (50 lines)
.\docker-logs.ps1 -Follow           # Live logs (Ctrl+C to stop)
.\docker-logs.ps1 -Errors           # Error logs only
.\docker-logs.ps1 -Session          # Session-related logs
```

---

## Redirect Loop / Apache Crash

### Symptom
- Accessing `http://localhost:8080` crashes Apache
- Docker container becomes unresponsive
- Docker Desktop won't restart (Hyper-V error)
- Logs show repeated redirects between `index.php` ↔ `reindex.php`

### Root Cause
Session initialization failure creates infinite redirect loop:

1. `index.php` checks `$_SESSION['reindex']` → empty → redirect to `reindex.php`
2. `reindex.php` sets `$_SESSION['reindex']` → redirect to `index.php`
3. If `session_start()` fails (MongoDB timeout/unavailable), session write fails
4. Loop repeats indefinitely → Apache overload → Container crash

**Why in Docker but not native:**
- MongoDB connection slower through Docker network (`host.docker.internal`)
- Session storage in MongoDB via `ClassSession.php`
- Native dev has MongoDB on localhost (faster)

**⚠️ CRITICAL: Never add `die()` in production code!**  
Even for debugging - it causes incomplete HTTP responses → browser hangs → Apache zombies

### Protection Added (2026-02-06)

#### 1. Redirect Loop Counter
[index.php](idae/web/index.php#L13-L20):
```php
// Max 3 redirects - show error instead of infinite loop
if (isset($_GET['retry']) && (int)$_GET['retry'] >= 3) {
    die('Session Error: Cannot initialize session');
}
```

#### 2. Session Validation
[reindex.php](idae/web/reindex.php#L13-L18):
```php
// Verify session is active before redirecting
if (session_status() !== PHP_SESSION_ACTIVE) {
    die('Session Error: session_start() failed');
}
```

#### 3. MongoDB Connection Timeout
[ClassSession.php](idae/web/appclasses/ClassSession.php#L24-L32):
```php
$options = [
    'db' => 'admin',
    'connectTimeoutMS' => 5000,  // 5s timeout
    'socketTimeoutMS' => 5000
];
$this->conn = new MongoClient($mongo_url, $options);
```

#### 4. Docker Healthcheck
[docker-compose.yml](docker-compose.yml#L23-L28):
```yaml
healthcheck:
  test: ["CMD", "curl", "-f", "-m", "3", "http://localhost/idae/web/check_mongo.php"]
  interval: 30s
  timeout: 5s
  retries: 3
```

---

## Quick Fixes

### 0a. Chargement Infini / Docker Restart Bloqué (URGENCE)
```powershell
# Si docker compose restart bloque ou page charge infiniment :
.\docker-emergency.ps1              # Force kill + clean restart (30s)

# Ou manuellement :
docker kill idae-legacy            # Force stop
docker rm -f idae-legacy           # Remove container
docker compose down                # Clean up
docker compose up -d               # Fresh start
```

**Signes du problème :**
- Page blanche avec spinner infini
- `docker compose restart` ne répond pas
- Processus Apache zombie (`<defunct>`)
- Logs montrent boucles de redirections

**Causes fréquentes :**
- `die()` dans le code (même pour debug !)
- Boucle de redirection infinie
- MongoDB timeout/inaccessible
- Session corruption

**Temps de résolution :** ~30 secondes avec `docker-emergency.ps1`

### 0b. Restart Apache Only (Fastest)
```powershell
# Graceful restart (terminates requests cleanly)
docker exec idae-legacy apachectl graceful

# Or force restart
docker exec idae-legacy apachectl restart

# Check Apache status
docker exec idae-legacy apachectl status
```

**When to use:** After code changes, when Apache hangs, or after config edits.  
**Time:** ~2 seconds (no container restart needed)

### 1. Docker Desktop Won't Start (Hyper-V)
```powershell
# Stop WSL2/Hyper-V
wsl --shutdown

# Wait 30s, retry Docker Desktop

# If still failing, reboot Windows
# Or (admin): bcdedit /set hypervisorlaunchtype auto
```

### 2. Check Session Errors
```powershell
# Enable debug logging
docker compose down
$env:DEBUG_SESSION=1
docker compose up

# Check logs
docker logs idae-legacy
# Look for: [Session::__construct] MongoDB connection FAILED
```

### 3. Test MongoDB Connection
```bash
# From host
curl http://localhost:8080/idae/web/check_mongo.php

# Should return:
# [OK] MongoDB connection established
# [OK] findOne() works
```

### 4. Bypass Session (Emergency)
Temporarily disable session check in `index.php`:
```php
// Comment out line 13:
// if (empty($_SESSION['reindex'])) { ... }
```

**⚠️ Remove this after fixing MongoDB!**

---

## Debugging Workflow

### 1. Check Container Status
```powershell
docker ps -a
# STATUS: healthy/unhealthy/restarting
```

### 2. Check Apache Logs
```powershell
# Real-time logs
docker logs -f idae-legacy

# Or from host (mounted volume)
Get-Content ./logs/php_error.log -Tail 50
```

### 3. Check MongoDB Connection
```powershell
# From container
docker exec idae-legacy php /var/www/html/idae/web/check_mongo.php

# Expected output:
# [OK] MongoDB connection established
# [OK] findOne() works
```

### 4. Check Session Files (if using file storage)
```powershell
# From container
docker exec idae-legacy ls -la /var/www/html/idae/sessions/
```

---

## Prevention

### 1. Always Use Healthcheck
Ensure `docker-compose.yml` includes healthcheck:
```yaml
healthcheck:
  test: ["CMD", "curl", "-f", "-m", "3", "http://localhost/idae/web/check_mongo.php"]
  start_period: 40s  # Give MongoDB time to start
```

### 2. Set Resource Limits
Add to `docker-compose.yml`:
```yaml
deploy:
  resources:
    limits:
      cpus: '2'
      memory: 2048M
```

### 3. Use Restart Policy
Already added:
```yaml
restart: unless-stopped
```

### 4. Monitor Redirect Loops
Check for `?retry=` parameter in logs:
```bash
docker logs idae-legacy | grep "retry="
```

If seeing `retry=1`, `retry=2`, `retry=3` repeatedly → MongoDB issue

---

## Common Scenarios

### Scenario: Container Starts then Crashes
**Cause:** MongoDB not ready when Apache starts  
**Solution:** Increase `healthcheck.start_period` to 60s

### Scenario: Healthcheck Failing
**Cause:** `check_mongo.php` timeout  
**Solution:** Check MongoDB logs, verify `host.docker.internal` resolves

### Scenario: Session Empty Despite session_start()
**Cause:** MongoDB write timeout  
**Solution:** Check MongoDB performance, increase `socketTimeoutMS`

### Scenario: Works Native, Not Docker
**Cause:** Network latency to `host.docker.internal`  
**Solution:** Run MongoDB in Docker container (linked service)

---

## Migration Notes

This redirect loop protection was added during MongoDB driver migration (PHP 8.2).  
Original code relied on session always working - not safe in Docker.

See also:
- [MIGRATION.md](MIGRATION.md) - MongoDB driver migration
- [MONGOCOMPAT.md](MONGOCOMPAT.md) - Compatibility layer
- [.github/copilot-instructions.md](.github/copilot-instructions.md) - Architecture

---

**Last Updated:** 2026-02-06  
**Author:** Agent (automated fix)
