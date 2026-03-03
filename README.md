# HospedaBC Marketplace

Sistema completo de marketplace para imóveis da HospedaBC, integrado com a API da Stays.net.

## 🎯 Objetivo

Marketplace público que:
- Conecta na API da Stays.net (multi-contas / multi-proprietários / multi-imóveis)
- Importa e replica imóveis no banco local
- Exibe imóveis com filtros, detalhes, calendário e preços
- Aplica camada de precificação com comissão/markup configurável
- Permite reservas através do marketplace
- Painel admin completo para gerenciamento

## 🛠️ Stack Tecnológica

- **PHP**: 8.1+
- **Framework**: Laravel 12
- **Banco de Dados**: MySQL/MariaDB
- **Frontend**: Blade + Tailwind CSS
- **Autenticação**: Laravel Breeze (Blade)
- **Permissões**: Spatie Laravel Permission
- **Ambiente Local**: XAMPP
- **Produção**: Hostinger (shared hosting)

## 📋 Funcionalidades

### ✅ Implementado

- [x] Estrutura completa do banco de dados (migrations e models)
- [x] Sistema de adapters para Stays.net (Mock + HTTP)
- [x] Service layer para sincronização
- [x] Comando artisan `stays:sync` para sincronização
- [x] Sistema de precificação com regras de markup
- [x] Models e relações completas
- [x] Rotas configuradas (marketplace + admin)
- [x] Documentação completa

### 🚧 Em Desenvolvimento

- [ ] Controllers do admin (CRUD completo)
- [ ] Views do marketplace público
- [ ] Views do painel admin
- [ ] Integração completa de reservas
- [ ] Sistema de webhooks (se suportado pela Stays)

## 📁 Estrutura do Projeto

```
market/
├── app/
│   ├── Console/Commands/
│   │   └── StaysSyncCommand.php      # Comando de sincronização
│   ├── Http/Controllers/
│   │   ├── Admin/                     # Controllers do admin
│   │   └── Marketplace/              # Controllers do marketplace
│   ├── Models/                       # Models Eloquent
│   ├── Services/
│   │   ├── Stays/                    # Adapters e service da Stays
│   │   └── PricingService.php        # Motor de precificação
│   └── Providers/
│       └── StaysServiceProvider.php   # Service Provider
├── config/
│   └── stays.php                     # Configuração da integração Stays
├── database/migrations/              # Migrations do banco
├── docs/                             # Documentação
│   ├── INSTALL_XAMPP.md
│   ├── DEPLOY_HOSTINGER.md
│   ├── STAYS_ADAPTER.md
│   └── PRICE_RULES.md
└── routes/
    └── web.php                        # Rotas da aplicação
```

## 🚀 Instalação Rápida

### Local (XAMPP)

1. Clone o projeto para `C:\xampp\htdocs\market`
2. Instale dependências: `composer install`
3. Configure `.env`: `copy .env.example .env`
4. Gere a chave: `php artisan key:generate`
5. Configure o banco no `.env`
6. Execute migrations: `php artisan migrate`
7. Crie um usuário admin (veja docs/INSTALL_XAMPP.md)

### Produção (Hostinger)

Veja instruções completas em `docs/DEPLOY_HOSTINGER.md`

## 📖 Documentação

- **[INSTALL_XAMPP.md](docs/INSTALL_XAMPP.md)**: Instalação no ambiente local
- **[DEPLOY_HOSTINGER.md](docs/DEPLOY_HOSTINGER.md)**: Deploy em shared hosting
- **[STAYS_ADAPTER.md](docs/STAYS_ADAPTER.md)**: Como implementar endpoints reais da Stays
- **[PRICE_RULES.md](docs/PRICE_RULES.md)**: Sistema de regras de precificação

## 🔧 Uso Básico

### Sincronizar Imóveis

```bash
# Sincronizar todos os owners ativos
php artisan stays:sync

# Sincronizar owner específico
php artisan stays:sync 1

# Sincronização completa (força atualização)
php artisan stays:sync --full

# Sincronizar próximos 365 dias
php artisan stays:sync --days=365
```

### Configurar Owner

1. Acesse `/admin/owners`
2. Crie um novo owner
3. Configure credenciais da Stays.net
4. Teste conexão: botão "Testar Conexão"
5. Importe imóveis: botão "Importar Imóveis"

### Configurar Regras de Preço

1. Acesse `/admin/markup-rules`
2. Crie regras globais, por owner ou por propriedade
3. Configure tipo (percentual ou fixo) e condições

## 🗄️ Banco de Dados

### Tabelas Principais

- `owners`: Proprietários/clientes Stays
- `properties`: Imóveis sincronizados
- `property_photos`: Fotos dos imóveis
- `property_amenities`: Comodidades
- `property_calendar_cache`: Cache de disponibilidade
- `property_rate_cache`: Cache de preços
- `markup_rules`: Regras de precificação
- `reservations`: Reservas do marketplace
- `sync_logs`: Logs de sincronização
- `integration_logs`: Logs de integração

## 🔐 Permissões

Roles disponíveis:
- `superadmin`: Acesso total
- `admin`: Gerenciamento geral
- `operador`: Operações básicas
- `owner_view`: Apenas visualização

## 🔌 Integração Stays.net

O sistema usa um adapter pattern que permite:
- **Mock**: Para desenvolvimento sem API real
- **HTTP**: Para produção com API real

Para implementar os endpoints reais, veja `docs/STAYS_ADAPTER.md`.

## 💰 Sistema de Precificação

O sistema aplica markup sobre os preços base da Stays com:
- Regras globais, por owner ou por propriedade
- Markup percentual ou fixo
- Regras por período, dia da semana, quantidade de noites

Veja exemplos em `docs/PRICE_RULES.md`.

## 📝 Próximos Passos

1. Implementar controllers do admin com CRUD completo
2. Criar views do marketplace (busca, filtros, detalhes)
3. Criar views do painel admin
4. Implementar checkout completo
5. Adicionar testes automatizados
6. Implementar webhooks (se suportado)

## 🤝 Contribuindo

Este é um projeto privado da HospedaBC. Para dúvidas ou sugestões, entre em contato com a equipe de desenvolvimento.

## 📄 Licença

Proprietário - HospedaBC

---

**Desenvolvido com ❤️ para HospedaBC**
