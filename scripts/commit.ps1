# Forcer l'encodage UTF-8 pour PowerShell
$OutputEncoding = [System.Text.Encoding]::UTF8
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
[Console]::InputEncoding = [System.Text.Encoding]::UTF8

# Charger les types de commit depuis le JSON
$commitTypesPath = Join-Path $PSScriptRoot "commit-types.json"
# Lire le fichier JSON avec l'encodage UTF-8
$commitTypesContent = Get-Content -Path $commitTypesPath -Raw -Encoding UTF8
$commitTypes = $commitTypesContent | ConvertFrom-Json

# Fonction pour afficher du texte en couleur
function Write-ColoredOutput {
    param(
        [string]$Message,
        [string]$Color = "White"
    )
    Write-Host $Message -ForegroundColor $Color
}

# Afficher l'en-tête
Clear-Host
Write-ColoredOutput "=== Assistant de commit avec Gitmoji ===" "Cyan"
Write-ColoredOutput "Tapez 'q' pour quitter" "Yellow"
Write-Host ""

# Afficher les types de commit disponibles
Write-ColoredOutput "Types de commit disponibles :" "Cyan"
for ($i = 0; $i -lt $commitTypes.types.Count; $i++) {
    $number = $i + 1
    $type = $commitTypes.types[$i]
    Write-Host "$number. $($type.emoji) [$($type.code)] $($type.description)"
}
Write-Host ""

# Demander le type de commit
$maxAttempts = 3
$attempt = 0
$selectedType = $null

while ($attempt -lt $maxAttempts -and -not $selectedType) {
    $input = Read-Host "Numéro du type de commit (1-$($commitTypes.types.Count))"
    
    if ($input -eq 'q') {
        Write-ColoredOutput "`n❌ Assistant de commit annulé." "Yellow"
        exit 0
    }
    
    if ($input -match '^\d+$' -and [int]$input -ge 1 -and [int]$input -le $commitTypes.types.Count) {
        $selectedType = $commitTypes.types[[int]$input - 1]
    } else {
        $attempt++
        if ($attempt -lt $maxAttempts) {
            Write-ColoredOutput "Entrée invalide. Veuillez entrer un nombre entre 1 et $($commitTypes.types.Count) (ou 'q' pour quitter). Tentative $attempt/$maxAttempts" "Red"
        }
    }
}

if (-not $selectedType) {
    Write-ColoredOutput "`n❌ Nombre maximum de tentatives atteint." "Red"
    exit 1
}

# Demander le message du commit
$attempt = 0
$commitMessage = ""

while ($attempt -lt $maxAttempts -and -not $commitMessage) {
    $input = Read-Host "`nMessage du commit (ou 'q' pour quitter)"
    
    if ($input -eq 'q') {
        Write-ColoredOutput "`n❌ Assistant de commit annulé." "Yellow"
        exit 0
    }
    
    if ($input.Trim()) {
        $commitMessage = $input.Trim()
    } else {
        $attempt++
        if ($attempt -lt $maxAttempts) {
            Write-ColoredOutput "Le message ne peut pas être vide. Tentative $attempt/$maxAttempts" "Red"
        }
    }
}

if (-not $commitMessage) {
    Write-ColoredOutput "`n❌ Nombre maximum de tentatives atteint." "Red"
    exit 1
}

# Construire le message complet
$fullMessage = "$($selectedType.emoji) [$($selectedType.code)] $commitMessage"

# Afficher l'aperçu
Write-ColoredOutput "`nAperçu du commit :" "Yellow"
Write-Host $fullMessage

# Demander confirmation
$attempt = 0
$confirmed = $false

while ($attempt -lt $maxAttempts -and -not $confirmed) {
    $input = Read-Host "`nProcéder au commit ? (O/N)"
    
    if ($input -eq 'q') {
        Write-ColoredOutput "`n❌ Assistant de commit annulé." "Yellow"
        exit 0
    }
    
    if ($input -eq 'O' -or $input -eq 'o') {
        $confirmed = $true
    } elseif ($input -eq 'N' -or $input -eq 'n') {
        Write-ColoredOutput "`n❌ Commit annulé." "Red"
        exit 0
    } else {
        $attempt++
        if ($attempt -lt $maxAttempts) {
            Write-ColoredOutput "Veuillez répondre par O (Oui) ou N (Non). Tentative $attempt/$maxAttempts" "Red"
        }
    }
}

if (-not $confirmed) {
    Write-ColoredOutput "`n❌ Nombre maximum de tentatives atteint." "Red"
    exit 1
}

# Vérifier s'il y a des changements à commiter
$status = git status --porcelain
if (-not $status) {
    Write-ColoredOutput "`n❌ Aucun changement à commiter." "Red"
    exit 1
}

# Exécuter le commit
git add .
if ($LASTEXITCODE -ne 0) {
    Write-ColoredOutput "`n❌ Erreur lors de l'ajout des fichiers." "Red"
    exit 1
}

# Utiliser l'encodage UTF-8 pour le message de commit
$commitCommand = "git commit -m `"$fullMessage`""
$encodedCommand = [System.Text.Encoding]::UTF8.GetBytes($commitCommand)
$encodedCommandString = [System.Text.Encoding]::UTF8.GetString($encodedCommand)
Invoke-Expression $encodedCommandString

if ($LASTEXITCODE -ne 0) {
    Write-ColoredOutput "`n❌ Erreur lors du commit." "Red"
    exit 1
}

Write-ColoredOutput "`n✅ Commit effectué avec succès !" "Green" 