param(
    [int]$Port = 8000
)

Write-Host "=== Iniciando sistema HospedaBC ===" -ForegroundColor Cyan

$ErrorActionPreference = "Stop"

# Caminho do projeto (pasta deste script)
$projectDir = Split-Path -Path $MyInvocation.MyCommand.Path -Parent
Set-Location $projectDir

function Test-PortInUse {
    param([int]$Port)
    try {
        return Test-NetConnection -ComputerName '127.0.0.1' -Port $Port -InformationLevel Quiet
    } catch {
        return $false
    }
}

function Start-LaravelServer {
    param([int]$Port)

    if (Test-PortInUse -Port $Port) {
        Write-Host "Laravel já está rodando na porta $Port." -ForegroundColor Yellow
        return
    }

    Write-Host "Iniciando Laravel na porta $Port..." -ForegroundColor Cyan
    Start-Process php -ArgumentList "artisan serve --host=0.0.0.0 --port=$Port" -WorkingDirectory $projectDir -WindowStyle Minimized | Out-Null

    # Aguarda alguns segundos até o servidor responder
    $tries = 0
    while (-not (Test-PortInUse -Port $Port) -and $tries -lt 10) {
        Start-Sleep -Seconds 1
        $tries++
    }

    if (Test-PortInUse -Port $Port) {
        Write-Host "Laravel rodando em http://127.0.0.1:$Port" -ForegroundColor Green
    } else {
        Write-Host "Não foi possível iniciar o Laravel na porta $Port." -ForegroundColor Red
        exit 1
    }
}

function Get-NgrokPath {
    $cmd = Get-Command ngrok -ErrorAction SilentlyContinue
    if ($cmd) {
        return $cmd.Source
    }

    $defaultWingetPath = Join-Path $env:LOCALAPPDATA "Microsoft\WinGet\Packages"
    if (Test-Path $defaultWingetPath) {
        $found = Get-ChildItem -Path $defaultWingetPath -Filter "ngrok*.exe" -Recurse -ErrorAction SilentlyContinue | Select-Object -First 1 -ExpandProperty FullName
        if ($found) { return $found }
    }

    return $null
}

function Start-NgrokTunnel {
    param(
        [string]$NgrokPath,
        [int]$Port
    )

    # Se já existe um túnel rodando, tenta reaproveitar
    try {
        $existing = Invoke-RestMethod -Uri "http://127.0.0.1:4040/api/tunnels" -TimeoutSec 2 -ErrorAction Stop
        if ($existing.tunnels -and $existing.tunnels.Count -gt 0) {
            $httpsTunnel = $existing.tunnels | Where-Object { $_.proto -eq "https" } | Select-Object -First 1
            if ($httpsTunnel) {
                return $httpsTunnel.public_url
            }
        }
    } catch {
        # Nenhum ngrok rodando ainda, segue adiante
    }

    Write-Host "Iniciando ngrok na porta $Port..." -ForegroundColor Cyan
    # Inicia o ngrok em segundo plano
    Start-Process $NgrokPath -ArgumentList "http $Port" -WorkingDirectory $projectDir -WindowStyle Minimized | Out-Null

    # Aguarda o painel local subir e o túnel ficar pronto
    $publicUrl = $null
    $maxTries = 20
    for ($i = 0; $i -lt $maxTries; $i++) {
        Start-Sleep -Seconds 2
        try {
            $resp = Invoke-RestMethod -Uri "http://127.0.0.1:4040/api/tunnels" -TimeoutSec 2 -ErrorAction Stop
            if ($resp.tunnels -and $resp.tunnels.Count -gt 0) {
                $httpsTunnel = $resp.tunnels | Where-Object { $_.proto -eq "https" } | Select-Object -First 1
                if ($httpsTunnel) {
                    $publicUrl = $httpsTunnel.public_url
                    break
                }
            }
        } catch {
            # ainda subindo
        }
    }

    if (-not $publicUrl) {
        Write-Host "ngrok não retornou um túnel público. Verifique o ngrok e tente novamente." -ForegroundColor Red
        exit 1
    }

    return $publicUrl
}

# 1) Inicia o Laravel
Start-LaravelServer -Port $Port

# 2) Localiza o ngrok
$ngrokPath = Get-NgrokPath
if (-not $ngrokPath) {
    Write-Host ""
    Write-Host "ngrok não encontrado." -ForegroundColor Red
    Write-Host "Instale com:" -ForegroundColor Yellow
    Write-Host "  winget install Ngrok.Ngrok --accept-source-agreements --accept-package-agreements" -ForegroundColor Yellow
    exit 1
}

# 3) Inicia túnel e obtém URL pública
$publicUrl = Start-NgrokTunnel -NgrokPath $ngrokPath -Port $Port

Write-Host ""
Write-Host "✅ Sistema online!" -ForegroundColor Green
Write-Host "URL pública:" -ForegroundColor Green
Write-Host "  $publicUrl" -ForegroundColor Cyan

try {
    Start-Process $publicUrl | Out-Null
} catch {
    # se não conseguir abrir o navegador, apenas ignora
}

