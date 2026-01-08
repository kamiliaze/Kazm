#!/usr/bin/env powershell

# Script pour renommer les images automatiquement

$imagePath = "C:\Users\LENOVO\mini_app!\public\images\products"
Write-Host "📁 Dossier: $imagePath" -ForegroundColor Cyan

# Récupérer tous les fichiers
$files = Get-ChildItem $imagePath -File -Include *.jpg, *.jpeg, *.png

Write-Host "`n📋 Fichiers détectés: $($files.Count)" -ForegroundColor Yellow

# Mapping intelligent basé sur les noms de fichiers
$renaming = @{
    # Gels douche
    "*Fleur*Oranger*" = "gel-oud.jpg"
    "*Fruit*Rouge*" = "gel-monoi.jpg"
    "*Oud Jasmine*" = "gel-oud-jasmin.jpg"
    "*Oud*" = "gel-oud.jpg"
    "*Amber*" = "gel-amber.jpg"
    "*Monoi*" = "gel-monoi.jpg"
    
    # Crèmes
    "*creme*Oud*" = "creme-oud.jpg"
    "*creme*Jasmine*" = "creme-oud-jasmin.jpg"
    
    # Déodorants (pots) - déjà faits
    
    # Parfums
    "*Crazy*Candy*" = "parfum-oud-jasmin.jpg"
}

Write-Host "`n" 
Write-Host "=============== RENOMMAGE DES IMAGES ===============" -ForegroundColor Green

$count = 0
foreach ($file in $files) {
    $oldName = $file.Name
    
    if ($oldName -like ".gitkeep" -or $oldName -like "*jpeg" -or $file.Length -lt 1000) {
        continue
    }
    
    foreach ($pattern in $renaming.Keys) {
        if ($oldName -like $pattern) {
            $newName = $renaming[$pattern]
            $oldPath = $file.FullName
            $newPath = Join-Path $imagePath $newName
            
            if ($oldPath -ne $newPath) {
                Rename-Item -Path $oldPath -NewName $newName -Force -ErrorAction SilentlyContinue
                Write-Host "✅ $oldName → $newName" -ForegroundColor Green
                $count++
            }
            break
        }
    }
}

Write-Host "`n✨ $count fichiers renommés!" -ForegroundColor Cyan
Write-Host "`nFichiers finaux:" -ForegroundColor Yellow
Get-ChildItem $imagePath -File -Include *.jpg, *.jpeg, *.png | 
    Where-Object { $_.Length -gt 1000 } | 
    Select-Object Name | 
    Sort-Object Name | 
    Format-Table -AutoSize

