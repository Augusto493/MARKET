# Plano de Testes - Layout HospedaBC + Motor de Busca e Reserva

## 5.1 Testes de Layout (manual)

| ID | Caso de teste | Passos | Resultado esperado |
|----|---------------|--------|--------------------|
| L1 | Hero exibido | Acessar `/marketplace/` | Título "Faça já a sua reserva!", subtítulo "Melhores hospedagens em Balneário Camboriú" e formulário visíveis |
| L2 | Seção diferenciais | Rolagem até "ALUGAR COM A GENTE É DIFERENTE" | 3 colunas com ícones/textos (Apartamentos completos, Estamos sempre presentes, Reserve com confiança) |
| L3 | Footer WhatsApp | Clicar no botão "Atendimento WhatsApp" | Abre WhatsApp com número correto |
| L4 | Header responsivo | Testar mobile/tablet | Nav e busca adaptados |
| L5 | Consistência entre páginas | Navegar por index, busca, imóvel, reserva | Header e footer iguais em todas |

## 5.2 Testes de Busca

| ID | Caso de teste | Passos | Resultado esperado |
|----|---------------|--------|--------------------|
| B1 | Busca sem filtros | GET `/marketplace/` | Lista imóveis publicados |
| B2 | Busca com cidade | `?cidade=Centro` | Só imóveis em Centro |
| B3 | Busca com datas | `?checkin=X&checkout=Y` | Exclui imóveis ocupados nesse período |
| B4 | Busca com hóspedes | `?hospedes=6` | Só imóveis com capacidade >= 6 |
| B5 | Ordenação preço | `?ordem=preco_asc` | Lista ordenada por preço crescente |
| B6 | Filtro preço | `?preco_min=300&preco_max=1000` | Imóveis dentro da faixa |

## 5.3 Testes de Cálculo de Preço

| ID | Caso de teste | Passos | Resultado esperado |
|----|---------------|--------|--------------------|
| P1 | Calcular preço | POST `/marketplace/calcular-preco` com property_id, checkin, checkout, guests_count | JSON com grand_total, nights, cleaning_fee |
| P2 | Datas inválidas | checkout <= checkin | Erro 422 |
| P3 | Imóvel inexistente | property_id inválido | Erro 404 |

## 5.4 Testes de Reserva

| ID | Caso de teste | Passos | Resultado esperado |
|----|---------------|--------|--------------------|
| R1 | Reservar com sucesso | Preencher formulário na página do imóvel e enviar | Redirect para `/marketplace/reserva/{id}` com mensagem de sucesso |
| R2 | Campos obrigatórios | Enviar sem nome/email | Erro de validação |
| R3 | Ver reserva | Acessar `/marketplace/reserva/{id}` | Exibe dados da reserva e código |
| R4 | Localizar por código | Acessar `/marketplace/localizar-reserva`, digitar código, enviar | Exibe reserva se existir |
| R5 | Localizar código inexistente | Digitar código inválido em localizar-reserva | Mensagem "Reserva não encontrada" |

## 5.5 Testes de Integração Stays (se aplicável)

| ID | Caso | Passos | Esperado |
|----|------|--------|----------|
| S1 | Preço via Stays | Imóvel com stays_property_id, calcular preço | Usa StaysPricingService |
| S2 | Reserva na Stays | Reservar imóvel conectado | status atualizado, stays_reservation_id preenchido |

## 5.6 Execução dos testes

- **Manual:** rodar casos L1–L5, B1–B6, P1–P3, R1–R5 em navegador (Chrome/Firefox)
- **Automatizado (opcional):** PHPUnit para `PropertyController::calculatePrice`, validações de `ReservationController::store`, e testes de integração para busca
- **Checklist final:** marcar todos os itens acima como OK após execução
