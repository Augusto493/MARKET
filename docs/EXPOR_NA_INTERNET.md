# Expor o sistema na internet (acesso de fora, hospedado no seu PC)

O projeto roda no seu computador (XAMPP/Laravel). Para outras pessoas acessarem pela internet, é preciso criar um **túnel**: um serviço que gera uma URL pública e encaminha o tráfego para o seu localhost.

---

## Pré-requiso: servidor acessível na sua rede local

O túnel vai conectar na porta que o seu servidor usa. Escolha uma das opções:

### Opção 1 – Usar o servidor do Laravel (recomendado para túnel)

No terminal, na pasta do projeto:

```powershell
cd C:\xampp\htdocs\market
php artisan serve --host=0.0.0.0 --port=8000
```

Deixe esse terminal aberto. O site fica em **http://127.0.0.1:8000** (e o túnel vai usar a porta **8000**).

### Opção 2 – Usar o Apache do XAMPP

1. Abra o XAMPP e inicie **Apache** e **MySQL**.
2. O site fica em **http://localhost/market/public** e o Apache usa a porta **80**.

---

## Método 1: ngrok (rápido e simples)

O ngrok gera uma URL pública (ex: `https://abc123.ngrok.io`) que redireciona para o seu PC.

### Instalação via linha de comando

**Opção A – Winget (Windows 10/11, recomendado):**

```powershell
winget install Ngrok.Ngrok --accept-source-agreements --accept-package-agreements
```

Feche e abra o terminal depois da instalação. Teste com: `ngrok version`.

**Opção B – Chocolatey (se já tiver instalado):**

```powershell
choco install ngrok -y
```

**Opção C – Scoop (se já tiver instalado):**

```powershell
scoop install ngrok
```

**Opção D – PowerShell (download direto, sem winget/choco/scoop):**

Execute no PowerShell (pode colar tudo de uma vez):

```powershell
$ngrokDir = "$env:LOCALAPPDATA\ngrok"
New-Item -ItemType Directory -Force -Path $ngrokDir | Out-Null
$zip = "$env:TEMP\ngrok.zip"
Invoke-WebRequest -Uri "https://bin.equinox.io/c/bnyj1mqvy4c/ngrok-v3-stable-windows-amd64.zip" -OutFile $zip -UseBasicParsing
Expand-Archive -Path $zip -DestinationPath $ngrokDir -Force
Remove-Item $zip -Force
$path = [Environment]::GetEnvironmentVariable("Path", "User")
if ($path -notlike "*$ngrokDir*") { [Environment]::SetEnvironmentVariable("Path", "$path;$ngrokDir", "User") }
Write-Host "ngrok instalado em $ngrokDir. Feche e abra o terminal e rode: ngrok http 8000"
```

Depois **feche e abra o PowerShell** e use: `ngrok http 8000`.

**Instalação manual (navegador):**  
1. Acesse https://ngrok.com/download e baixe o Windows.  
2. (Opcional) Crie conta em https://ngrok.com e use o authtoken para URLs estáveis.

### Comandos

**Se estiver usando `php artisan serve` na porta 8000:**

```powershell
ngrok http 8000
```

**Se estiver usando Apache (XAMPP) na porta 80:**

```powershell
ngrok http 80
```

No terminal o ngrok mostra algo como:

```
Forwarding   https://xxxx-xx-xx-xx-xx.ngrok-free.app -> http://localhost:8000
```

Essa URL **https://...** é a que você envia para outras pessoas acessarem.

### Ajuste no Laravel (links e redirecionamentos)

Para os links e redirecionamentos do Laravel usarem a URL pública, defina no `.env` a URL que o ngrok mostrou:

```env
APP_URL=https://xxxx-xx-xx-xx-xx.ngrok-free.app
```

Troque pela URL que apareceu no seu terminal. Depois reinicie o `php artisan serve` (ou o Apache) se precisar.

---

## Método 2: Cloudflare Tunnel (gratuito, sem abrir porta no roteador)

O Cloudflare Tunnel (cloudflared) cria um túnel seguro e você pode ter um subdomínio fixo.

### Instalação

1. Baixe o cloudflared para Windows:  
   https://github.com/cloudflare/cloudflared/releases  
2. Extraia e coloque o `cloudflared.exe` em uma pasta no PATH ou use o caminho completo no comando.

### Comando rápido (URL temporária)

**Com o Laravel na porta 8000:**

```powershell
cloudflared tunnel --url http://127.0.0.1:8000
```

**Com Apache na porta 80:**

```powershell
cloudflared tunnel --url http://127.0.0.1:80
```

O terminal vai mostrar uma URL do tipo `https://xxx-xxx-xxx.trycloudflare.com`. Use essa URL para acessar de fora.

No `.env`:

```env
APP_URL=https://xxx-xxx-xxx.trycloudflare.com
```

---

## Método 3: localtunnel (via npm)

Requer Node.js instalado.

### Instalação e uso (uma vez)

```powershell
npx localtunnel --port 8000
```

Se estiver usando Apache na porta 80:

```powershell
npx localtunnel --port 80
```

Será exibida uma URL como `https://xxx.loca.lt`. Use essa URL no navegador e, na primeira vez, clique em “Click to Continue” na página do localtunnel.

No `.env`:

```env
APP_URL=https://xxx.loca.lt
```

---

## Resumo dos comandos

| Situação                         | Comando principal                          |
|----------------------------------|-------------------------------------------|
| Laravel na porta 8000 + ngrok    | `ngrok http 8000`                         |
| Apache na porta 80 + ngrok       | `ngrok http 80`                           |
| Laravel 8000 + Cloudflare       | `cloudflared tunnel --url http://127.0.0.1:8000` |
| Laravel 8000 + localtunnel      | `npx localtunnel --port 8000`             |

---

## Segurança e uso real

- **Firewall:** em geral não é preciso abrir portas no roteador; o túnel sai do seu PC e recebe conexões pelo servidor do ngrok/Cloudflare/localtunnel.
- **HTTPS:** ngrok e Cloudflare já fornecem HTTPS na URL que eles geram.
- **Uso temporário:** ótimo para testes e demonstrações. Para uso contínuo, considere:
  - Conta gratuita no ngrok (URL mais estável),
  - Ou túnel nomeado no Cloudflare (subdomínio fixo).
- **Produção:** para uso sério e muitos acessos, o ideal é hospedar em um servidor (VPS ou hospedagem), não no seu PC.

---

## Checklist rápido

1. [ ] Apache + MySQL (XAMPP) **ou** `php artisan serve --host=0.0.0.0 --port=8000` rodando.  
2. [ ] ngrok **ou** cloudflared **ou** localtunnel instalado.  
3. [ ] Comando do túnel executado (ex: `ngrok http 8000`).  
4. [ ] `.env` com `APP_URL` igual à URL pública que o túnel mostrou.  
5. [ ] Testar a URL pública em outro dispositivo ou rede (celular com Wi‑Fi desligado, 4G).
