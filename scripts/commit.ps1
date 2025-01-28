# Forcer l'encodage en UTF-8
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
$PSDefaultParameterValues['*:Encoding'] = 'utf8'

# Lire le fichier JSON des types de commit
$commitTypes = Get-Content -Raw -Path "$PSScriptRoot\commit-types.json" -Encoding UTF8 | ConvertFrom-Json

# Afficher le menu des types de commit
Write-Host "`nChoisissez le type de commit :" -ForegroundColor Cyan
for ($i = 0; $i -lt $commitTypes.types.Count; $i++) {
    $type = $commitTypes.types[$i]
    Write-Host "$($i + 1). $($type.emoji) [$($type.code.ToUpper())] $($type.description)"
}

# Demander à l'utilisateur de choisir un type
$choice = Read-Host "`nEntrez le numéro de votre choix (1-$($commitTypes.types.Count))"
$choice = [int]$choice - 1

if ($choice -ge 0 -and $choice -lt $commitTypes.types.Count) {
    $selectedType = $commitTypes.types[$choice]
    
    # Demander le message du commit
    $message = Read-Host "Entrez votre message de commit"
    
    # Construire le message de commit complet
    $fullMessage = "$($selectedType.emoji) [$($selectedType.code.ToUpper())] $message"
    
    # Afficher un aperçu
    Write-Host "`nAperçu du commit :" -ForegroundColor Yellow
    Write-Host $fullMessage
    
    # Demander confirmation
    $confirm = Read-Host "`nVoulez-vous procéder au commit ? (O/N)"
    if ($confirm -eq "O" -or $confirm -eq "o") {
        # Exécuter la commande git commit
        git add .
        git commit -m $fullMessage
        Write-Host "`nCommit effectué avec succès !" -ForegroundColor Green
    }
    else {
        Write-Host "`nCommit annulé." -ForegroundColor Red
    }
}
else {
    Write-Host "Choix invalide." -ForegroundColor Red
} 
