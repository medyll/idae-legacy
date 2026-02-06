#!/usr/bin/env pwsh
# Script pour démarrer le serveur socket avec forever
# Usage: .\start-socket.ps1

Set-Location $PSScriptRoot

Write-Host "=== Démarrage du serveur socket.io ===" -ForegroundColor Green

# Vérifier si forever est installé
if (!(Test-Path ".\node_modules\.bin\forever.cmd")) {
    Write-Host "Forever n'est pas installé. Installation en cours..." -ForegroundColor Yellow
    npm install
}

# Créer le répertoire logs s'il n'existe pas
if (!(Test-Path ".\logs")) {
    New-Item -ItemType Directory -Path ".\logs" | Out-Null
}

# Créer le répertoire .forever dans le profil utilisateur si nécessaire
if (!(Test-Path "$env:USERPROFILE\.forever\logs")) {
    New-Item -ItemType Directory -Force -Path "$env:USERPROFILE\.forever\logs" | Out-Null
}

# Démarrer le serveur avec forever
npm run start

Write-Host ""
Write-Host "Serveur démarré en arrière-plan." -ForegroundColor Green
Write-Host "Pour voir l'état: npm run status" -ForegroundColor Cyan
Write-Host "Pour voir les logs: npm run logs" -ForegroundColor Cyan
Write-Host "Pour arrêter: npm run stop" -ForegroundColor Cyan
