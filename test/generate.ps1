# Windows PowerShell Script
$ErrorActionPreference = "Stop"

# Go to current location
$CURRENT_FOLDER=(Get-Location)
Set-Location (Split-Path $MyInvocation.Line -Parent)

# Remove the old generated PDF files
Remove-Item *.pdf

# Go to the examples folder
Set-Location ../examples

Get-ChildItem ./ -File | ForEach-Object {
    $PHP_SCRIPT="$_"
    $PDF_FILE="../test/$_" -replace ".php",".pdf"
    Write-Output " - $PHP_SCRIPT > $PDF_FILE"
    php $PHP_SCRIPT > $PDF_FILE
}

# Go to the test folder to see the result
Set-Location ../test
Get-ChildItem

# Restore the current location
Set-Location $CURRENT_FOLDER
