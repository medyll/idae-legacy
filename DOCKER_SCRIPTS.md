# Docker Scripts - Quick Reference

Scripts PowerShell pour gérer l'environnement Docker idae-legacy.

## 📋 Scripts disponibles

### `docker-restart.ps1` - Redémarrage

Redémarre Apache ou le conteneur selon les besoins.

**Usage :**
```powershell
.\docker-restart.ps1               # Apache graceful (defaut, recommandé)
.\docker-restart.ps1 apache        # Idem
.\docker-restart.ps1 container     # Restart container complet
.\docker-restart.ps1 full          # Down + Up (restart complet)
```

**Quand l'utiliser :**
- ✅ **Apache graceful** : après modification de code PHP, MongoDB lent
- ✅ **Container** : si Apache ne répond plus, après changement d'environnement
- ✅ **Full** : problèmes majeurs, changement de configuration Docker

**Temps :**
- Apache: ~2s
- Container: ~5-10s
- Full: ~15-20s

---

### `docker-health.ps1` - Diagnostic

Vérifie l'état complet du système Docker.

**Usage :**
```powershell
.\docker-health.ps1
```

**Ce qui est vérifié :**
1. ✅ Statut du conteneur (running/stopped/restarting)
2. ✅ Health check (healthy/unhealthy)
3. ✅ Connexion MongoDB
4. ✅ Réponse HTTP
5. ✅ Erreurs récentes dans les logs

**Output exemple :**
```
🏥 Docker Health Check
=====================

1️⃣ Container Status:
NAMES         STATUS                   PORTS
idae-legacy   Up 5 minutes (healthy)   8080/tcp, 0.0.0.0:8080->80/tcp

2️⃣ Health Status:
  healthy

3️⃣ MongoDB Connection:
  ✅ MongoDB OK

4️⃣ HTTP Response:
  ✅ HTTP 200 OK

5️⃣ Recent Errors:
  ✅ No errors
```

---

### `docker-logs.ps1` - Logs

Affiche les logs du conteneur avec filtres.

**Usage :**
```powershell
.\docker-logs.ps1                  # 50 dernières lignes
.\docker-logs.ps1 -Lines 100       # 100 dernières lignes
.\docker-logs.ps1 -Follow          # Suivi en temps réel (Ctrl+C pour arrêter)
.\docker-logs.ps1 -Errors          # Erreurs uniquement
.\docker-logs.ps1 -Session         # Logs de session (reindex, retry, etc.)
```

**Exemples :**
```powershell
# Vérifier les erreurs PHP
.\docker-logs.ps1 -Errors

# Debug session
.\docker-logs.ps1 -Session

# Suivre les logs en live
.\docker-logs.ps1 -Follow
```

---

## 🚀 Workflows courants

### Après modification de code PHP
```powershell
.\docker-restart.ps1 apache        # Restart Apache uniquement
```

### Apache ne répond plus (hang)
```powershell
.\docker-health.ps1                # Diagnostic
.\docker-restart.ps1 apache        # Tentative restart Apache
.\docker-logs.ps1 -Errors          # Vérifier les erreurs

# Si ça ne fonctionne pas :
.\docker-restart.ps1 container     # Restart conteneur
```

### Boucle de redirection / Session error
```powershell
.\docker-logs.ps1 -Session         # Vérifier les logs session
.\docker-restart.ps1 apache        # Restart Apache

# Si ça persiste :
$env:DEBUG_SESSION=1               # Activer debug session
.\docker-restart.ps1 container     # Restart avec debug
.\docker-logs.ps1 -Follow          # Suivre les logs
```

### MongoDB inaccessible
```powershell
.\docker-health.ps1                # Vérifier connexion MongoDB

# Test manuel MongoDB
docker exec idae-legacy php /var/www/html/idae/web/check_mongo.php

# Si MongoDB externe non démarré :
# - Démarrer MongoDB localement
# - Vérifier host.docker.internal
```

### Container ne démarre pas
```powershell
docker ps -a                       # Vérifier statut
.\docker-logs.ps1 -Errors          # Vérifier erreurs

.\docker-restart.ps1 full          # Restart complet

# Si ça échoue encore :
docker compose down -v             # Supprime volumes
docker compose up -d               # Rebuild
```

---

## 🔧 Commandes manuelles (alternatives)

Si les scripts PowerShell ne fonctionnent pas :

### Restart Apache
```bash
docker exec idae-legacy apachectl graceful
docker exec idae-legacy apachectl restart
```

### Restart Container
```bash
docker compose restart
docker compose stop && docker compose start
```

### Logs
```bash
docker logs idae-legacy                    # All logs
docker logs -f idae-legacy                 # Follow
docker logs --tail 50 idae-legacy          # Last 50 lines
docker logs idae-legacy | grep error       # Errors only
```

### Health
```bash
docker ps                                  # Status
docker inspect idae-legacy --format='{{.State.Health.Status}}'
curl http://localhost:8080/idae/web/check_mongo.php
```

---

## 📚 Documentation

- [DOCKER_TROUBLESHOOTING.md](DOCKER_TROUBLESHOOTING.md) - Guide complet de dépannage
- [.github/copilot-instructions.md](.github/copilot-instructions.md) - Architecture système
- [MIGRATION.md](MIGRATION.md) - Migration MongoDB

---

## ⚠️ Notes importantes

1. **Toujours privilégier `apache` restart** avant `container` restart :
   - Plus rapide (~2s vs ~10s)
   - Pas d'interruption des autres services
   - Pas de perte de cache

2. **`full` restart en dernier recours** :
   - Supprime les connexions actives
   - Peut perdre des données temporaires
   - Plus long (~20s)

3. **Vérifier les logs après chaque restart** :
   ```powershell
   .\docker-restart.ps1 apache
   .\docker-logs.ps1 -Errors       # Vérifier pas d'erreurs
   ```

4. **Activer DEBUG_SESSION si problème de session** :
   ```powershell
   $env:DEBUG_SESSION=1
   docker compose restart
   .\docker-logs.ps1 -Follow
   ```

---

**Créé :** 2026-02-06  
**Auteur :** Agent (migration MongoDB / Docker stability fixes)
