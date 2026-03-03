# Instalação no XAMPP

## Pré-requisitos

- XAMPP com PHP 8.1+ instalado
- MySQL/MariaDB rodando
- Composer instalado

## Passos

1. **Clone ou copie o projeto para `C:\xampp\htdocs\market`**

2. **Instale as dependências:**
   ```bash
   cd C:\xampp\htdocs\market
   composer install
   ```

3. **Configure o ambiente:**
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```

4. **Configure o banco de dados no `.env`:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=marketplace
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Crie o banco de dados:**
   - Abra phpMyAdmin (http://localhost/phpmyadmin)
   - Crie um banco chamado `marketplace`

6. **Execute as migrations:**
   ```bash
   php artisan migrate
   ```

7. **Instale o Breeze (se ainda não estiver):**
   ```bash
   php artisan breeze:install blade
   npm install && npm run build
   ```

8. **Crie um usuário admin:**
   ```bash
   php artisan tinker
   ```
   ```php
   $user = \App\Models\User::create([
       'name' => 'Admin',
       'email' => 'admin@hospedabc.com.br',
       'password' => bcrypt('senha123'),
   ]);
   $user->assignRole('superadmin');
   ```

9. **Acesse o sistema:**
   - Marketplace: http://localhost/market/public
   - Admin: http://localhost/market/public/admin/dashboard

## Configuração do Apache

Se necessário, configure um VirtualHost no Apache (`C:\xampp\apache\conf\extra\httpd-vhosts.conf`):

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/market/public"
    ServerName marketplace.local
    <Directory "C:/xampp/htdocs/market/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Adicione ao arquivo `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1 marketplace.local
```

## CRON (Sincronização Automática)

Para sincronização automática, configure uma tarefa agendada no Windows:

1. Abra o Agendador de Tarefas do Windows
2. Crie uma nova tarefa
3. Configure para executar a cada hora:
   ```
   C:\xampp\php\php.exe C:\xampp\htdocs\market\artisan stays:sync
   ```

Ou use o agendador do Laravel editando `app/Console/Kernel.php` e configurando:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('stays:sync')->hourly();
}
```

E execute manualmente quando necessário:
```bash
php artisan schedule:run
```
