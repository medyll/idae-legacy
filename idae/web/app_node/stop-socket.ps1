#!/usr/bin/env pwsh
# Script pour arrêter le serveur socket
# Usage: .\stop-socket.ps1

Set-Location $PSScriptRoot

Write-Host "=== Arrêt du serveur socket.io ===" -ForegroundColor Yellow

npm run stop

Write-Host ""
Write-Host "Serveur arrêté." -ForegroundColor Green
