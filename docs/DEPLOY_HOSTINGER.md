# Deploy na Hostinger (Shared Hosting)

## Pré-requisitos

- Conta na Hostinger com acesso SSH (recomendado) ou File Manager
- PHP 8.1+ disponível
- MySQL/MariaDB disponível
- Composer disponível (via SSH)

## Deploy via Git (recomendado)

Se você já configurou o repositório no painel da Hostinger (GIT → repositório `git@github.com:Augusto493/MARKET.git`, branch `main`, diretório `public_html`):

1. **Push do código** (no seu PC):
   ```bash
   git add .
   git commit -m "sua mensagem"
   git push origin main
   ```

2. **No painel Hostinger**: clique em **Implantar** (Deploy).

3. **Após o primeiro deploy**, conecte por SSH e execute (ou use o arquivo `COMANDOS_APOS_DEPLOY.txt` na raiz do projeto):
   ```bash
   cd ~/domains/hospedavoce.online/public_html
   cp .env.example .env
   # Edite .env (APP_URL, DB_*) no File Manager ou: nano .env
   composer install --no-dev --optimize-autoloader
   php artisan key:generate
   php artisan migrate --force
   chmod -R 755 storage bootstrap/cache
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
   O **build do front (Vite)** já vem no repositório; não é necessário rodar `npm` no servidor.

O projeto inclui um **`.htaccess` na raiz** que redireciona todas as requisições para a pasta `public`, para funcionar quando a raiz do site é `public_html` (repositório clonado na raiz).

---

## Passos de Deploy (upload manual)

### 1. Upload dos Arquivos

**Via SSH:**
```bash
# No seu computador local
cd C:\xampp\htdocs\market
composer install --no-dev --optimize-autoloader
npm run build

# Compactar (exceto node_modules, vendor pode ser opcional)
# Upload via FTP/SFTP para public_html
```

**Via File Manager:**
- Faça upload de todos os arquivos para `public_html`
- Certifique-se de que a pasta `public` está acessível

### 2. Configuração do .env

Crie o arquivo `.env` na raiz do projeto (em `public_html`) com:

```env
APP_NAME="HospedaBC Marketplace"
APP_ENV=production
APP_KEY=base64:... (gere com: php artisan key:generate)
APP_DEBUG=false
APP_URL=https://hospedavoce.online

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=seu_banco_hostinger
DB_USERNAME=seu_usuario_hostinger
DB_PASSWORD=sua_senha

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Stays (produção)
STAYS_ADAPTER=http
# ... demais variáveis do .env.example
```

### 3. Permissões

Configure as permissões necessárias:

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### 4. Executar Migrations

Via SSH:
```bash
cd public_html
php artisan migrate --force
```

### 5. Configurar CRON

Na Hostinger, configure o CRON para executar:

**Opção 1: Agendador do Laravel (recomendado)**
```
* * * * * cd /home/usuario/public_html && php artisan schedule:run >> /dev/null 2>&1
```

**Opção 2: Comando direto**
```
0 * * * * cd /home/usuario/public_html && php artisan stays:sync >> /dev/null 2>&1
```

### 6. Configurar .htaccess

Certifique-se de que o `.htaccess` em `public` está configurado:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### 7. Otimizações

Execute os comandos de otimização:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. Verificar

- Acesse https://seudominio.com.br
- Teste o login admin
- Execute uma sincronização manual: `php artisan stays:sync`

## Troubleshooting

### Erro 500
- Verifique os logs em `storage/logs/laravel.log`
- Verifique permissões de arquivos
- Verifique se o `.env` está configurado corretamente

### CRON não funciona
- Verifique o caminho completo do PHP: `which php`
- Verifique se o caminho do projeto está correto
- Teste manualmente executando o comando

### Erro de memória
- Aumente `memory_limit` no `php.ini` ou `.htaccess`
- Considere usar `php artisan queue:work` para processar jobs em background
