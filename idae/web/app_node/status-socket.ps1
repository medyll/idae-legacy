#!/usr/bin/env pwsh
# Script pour voir l'état du serveur socket
# Usage: .\status-socket.ps1

Set-Location $PSScriptRoot

Write-Host "=== État du serveur socket.io ===" -ForegroundColor Cyan

npm run status

Write-Host ""
Write-Host "Pour voir les logs en temps réel: npm run logs" -ForegroundColor Cyan
