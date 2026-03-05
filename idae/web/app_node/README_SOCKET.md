# Gestion du serveur Socket.io

## Installation

Installer les dépendances (dont forever):
```bash
npm install
```

## Commandes disponibles

### Avec npm (recommandé)
```bash
npm run start     # Démarrer le serveur en arrière-plan avec forever
npm run stop      # Arrêter le serveur
npm run restart   # Redémarrer le serveur
npm run status    # Voir l'état du serveur
npm run logs      # Voir les logs en temps réel
npm run dev       # Lancer en mode développement (premier plan)
```

### Avec les scripts PowerShell
```powershell
.\start-socket.ps1    # Démarrer le serveur
.\stop-socket.ps1     # Arrêter le serveur
.\status-socket.ps1   # Voir l'état
```

### Commandes forever directes
```bash
# Démarrer
npx forever start -a -l logs/socket.log -o logs/socket-out.log -e logs/socket-err.log --uid idae_socket idae_server.js

# Arrêter
npx forever stop idae_socket

# Lister tous les processus
npx forever list

# Arrêter tous les processus forever
npx forever stopall

# Voir les logs
npx forever logs idae_socket
```

## Logs

Les logs sont stockés dans le répertoire `logs/`:
- `socket.log` - Log combiné
- `socket-out.log` - Sortie standard
- `socket-err.log` - Erreurs

## Migration depuis ancien système

L'ancien fichier `start_socket.js` est obsolète. Utilisez maintenant les nouvelles commandes npm.

## Dépannage

### Arrêter tous les processus Node.js
```powershell
Stop-Process -Name node -Force
```

### Voir les processus Node.js en cours
```powershell
Get-Process -Name node
```

### Si forever ne fonctionne pas
Essayez de lancer directement:
```bash
npm run dev
```
Puis utilisez `Ctrl+C` pour arrêter.
