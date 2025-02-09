# Obtenir le chemin du dossier WindowsApps
$windowsAppsPath = Join-Path $env:LOCALAPPDATA "Microsoft\WindowsApps"

# Obtenir le chemin absolu du dossier scripts
$scriptsPath = $PSScriptRoot
$commitPath = Join-Path $scriptsPath "commit.ps1"

# Créer le contenu du fichier gitmoji.cmd avec le chemin absolu
$gitmojiContent = @"
@echo off
PowerShell -NoProfile -ExecutionPolicy Bypass -File "$commitPath"
"@

# Chemin complet du fichier gitmoji.cmd dans WindowsApps
$gitmojiPath = Join-Path $windowsAppsPath "gitmoji.cmd"

try {
    # Créer le fichier gitmoji.cmd dans WindowsApps
    Set-Content -Path $gitmojiPath -Value $gitmojiContent -Force -Encoding UTF8
    Write-Host "✅ Installation réussie! Vous pouvez maintenant utiliser la commande 'gitmoji' depuis n'importe où dans votre projet." -ForegroundColor Green
} catch {
    Write-Host "❌ Erreur lors de l'installation: $_" -ForegroundColor Red
    Write-Host "Vous pouvez toujours utiliser '.\gitmoji' depuis la racine du projet." -ForegroundColor Yellow
} 