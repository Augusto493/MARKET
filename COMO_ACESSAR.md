# HospedaBC Marketplace - Como acessar no XAMPP

## URLs

- **Marketplace (site público):**  
  http://localhost/market/public  
  ou, se configurou virtual host:  
  http://marketplace.local  

- **Login / Admin:**  
  http://localhost/market/public/login  

## Credenciais do admin (criadas pelo seed)

- **Email:** admin@hospedabc.com.br  
- **Senha:** admin123  

Após login você é redirecionado para o **Dashboard** do admin.

## O que já está pronto

1. **Marketplace:** lista de imóveis (3 imóveis de demonstração já importados e publicados).
2. **Admin:** Dashboard, Owners, Imóveis, Reservas, Regras de preço.
3. **Sincronização:** no admin, em Owners, use "Importar" para rodar o sync (modo mock).
4. **Reservas:** na página do imóvel no marketplace é possível enviar uma solicitação de reserva.

## Rodar pelo XAMPP

1. Inicie o **Apache** e o **MySQL** no painel do XAMPP.
2. Acesse: **http://localhost/market/public**
3. Para entrar no painel: **http://localhost/market/public/login** → admin@hospedabc.com.br / admin123

## Se quiser usar o servidor embutido do PHP (alternativa)

```bash
cd C:\xampp\htdocs\market
php artisan serve
```

Depois acesse: **http://127.0.0.1:8000**
