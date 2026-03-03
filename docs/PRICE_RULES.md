# Sistema de Regras de Preço (Markup)

## Visão Geral

O sistema permite aplicar markup (comissão) sobre os preços base da Stays.net de forma flexível, com regras globais, por owner ou por propriedade.

## Tipos de Regras

### 1. Global
Aplica a todos os imóveis do sistema.

### 2. Por Owner
Aplica a todos os imóveis de um owner específico.

### 3. Por Property
Aplica apenas a um imóvel específico.

**Prioridade:** Property > Owner > Global

## Tipos de Markup

### Percentual
Adiciona uma porcentagem sobre o preço base.
- Exemplo: 10% → Se preço base é R$ 100, adiciona R$ 10

### Fixo
Adiciona um valor fixo.
- Exemplo: R$ 50 → Se preço base é R$ 100, adiciona R$ 50

## Regras Avançadas (Opcionais)

### Por Período
- `data_inicio`: Data inicial da regra
- `data_fim`: Data final da regra
- A regra só se aplica se o check-in estiver dentro deste período

### Por Dia da Semana
- `dias_semana`: Array com dias (0=domingo, 1=segunda, ..., 6=sábado)
- Exemplo: `[5, 6]` = apenas sexta e sábado

### Por Quantidade de Noites
- `min_noites`: Mínimo de noites para aplicar a regra
- `max_noites`: Máximo de noites para aplicar a regra
- Exemplo: `min_noites: 7` = apenas para estadias de 7+ noites

## Exemplos de Uso

### Exemplo 1: Comissão Global de 10%
```php
MarkupRule::create([
    'nome' => 'Comissão Global 10%',
    'tipo' => 'global',
    'markup_type' => 'percent',
    'markup_value' => 10,
    'ativo' => true,
    'prioridade' => 1,
]);
```

### Exemplo 2: Markup de 15% para Finais de Semana
```php
MarkupRule::create([
    'nome' => 'Finais de Semana +15%',
    'tipo' => 'global',
    'markup_type' => 'percent',
    'markup_value' => 15,
    'dias_semana' => [5, 6], // Sexta e Sábado
    'ativo' => true,
    'prioridade' => 2,
]);
```

### Exemplo 3: Markup Especial para Alta Temporada
```php
MarkupRule::create([
    'nome' => 'Alta Temporada +20%',
    'tipo' => 'global',
    'markup_type' => 'percent',
    'markup_value' => 20,
    'data_inicio' => '2026-12-20',
    'data_fim' => '2027-02-28',
    'ativo' => true,
    'prioridade' => 3,
]);
```

### Exemplo 4: Markup por Owner Específico
```php
MarkupRule::create([
    'nome' => 'Owner Premium +5%',
    'tipo' => 'owner',
    'owner_id' => 1,
    'markup_type' => 'percent',
    'markup_value' => 5,
    'ativo' => true,
    'prioridade' => 1,
]);
```

### Exemplo 5: Markup Fixo para Estadias Longas
```php
MarkupRule::create([
    'nome' => 'Estadias Longas +R$50',
    'tipo' => 'global',
    'markup_type' => 'fixed',
    'markup_value' => 50,
    'min_noites' => 7,
    'ativo' => true,
    'prioridade' => 1,
]);
```

## Cálculo do Preço Final

O `PricingService` calcula o preço final da seguinte forma:

1. Busca todas as regras ativas aplicáveis (considerando tipo, período, dias, noites)
2. Ordena por prioridade (maior primeiro) e tipo (property > owner > global)
3. Aplica cada regra sequencialmente sobre o preço base
4. Soma todos os markups
5. Preço final = Preço base + Soma dos markups

## Uso no Código

```php
use App\Services\PricingService;
use Carbon\Carbon;

$service = new PricingService();
$property = Property::find(1);
$checkin = Carbon::parse('2026-03-01');
$checkout = Carbon::parse('2026-03-05');
$basePrice = 250.00;

$result = $service->calculateFinalPrice($property, $checkin, $checkout, $basePrice);
// Retorna: ['base_price' => 250, 'markup_total' => 25, 'final_price' => 275, ...]

// Para período completo:
$periodResult = $service->calculatePeriodPrice($property, $checkin, $checkout);
// Retorna: ['base_total' => 1000, 'markup_total' => 100, 'final_total' => 1100, ...]
```

## Interface Admin

Acesse `/admin/markup-rules` para gerenciar as regras via interface web.
