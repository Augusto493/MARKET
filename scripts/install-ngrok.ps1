# Instala o ngrok via PowerShell (download direto)
# Uso: .\install-ngrok.ps1
# Ou: powershell -ExecutionPolicy Bypass -File .\scripts\install-ngrok.ps1

$ErrorActionPreference = "Stop"
$ngrokDir = "$env:LOCALAPPDATA\ngrok"
$zip = "$env:TEMP\ngrok-windows.zip"

Write-Host "Instalando ngrok em $ngrokDir ..." -ForegroundColor Cyan
New-Item -ItemType Directory -Force -Path $ngrokDir | Out-Null

# URL estável do ngrok para Windows (amd64)
$url = "https://bin.equinox.io/c/bnyj1mqvy4c/ngrok-v3-stable-windows-amd64.zip"
try {
    Invoke-WebRequest -Uri $url -OutFile $zip -UseBasicParsing
} catch {
    Write-Host "Falha no download. Tente instalar com: winget install Ngrok.Ngrok" -ForegroundColor Yellow
    exit 1
}

Expand-Archive -Path $zip -DestinationPath $ngrokDir -Force
Remove-Item $zip -Force -ErrorAction SilentlyContinue

$currentPath = [Environment]::GetEnvironmentVariable("Path", "User")
if ($currentPath -notlike "*$ngrokDir*") {
    [Environment]::SetEnvironmentVariable("Path", "$currentPath;$ngrokDir", "User")
    Write-Host "Pasta do ngrok adicionada ao PATH do usuario." -ForegroundColor Green
}

Write-Host ""
Write-Host "ngrok instalado com sucesso em: $ngrokDir" -ForegroundColor Green
Write-Host "Feche e abra o terminal (PowerShell) e depois rode:" -ForegroundColor Yellow
Write-Host "  ngrok http 8000" -ForegroundColor White
Write-Host ""
