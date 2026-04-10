# Módulo Garçom — Contexto para Claude

## Visão Geral

Sistema de pedidos para espetaria. Garçom autentica por PIN (4 dígitos) e é
redirecionado para `waiter.tables`. Todo acesso ao módulo passa pelos middlewares
`auth`, `active` e `role:waiter`.

---

## Stack

- PHP 8.2 / Laravel 12
- Tailwind CSS v4
- Alpine.js (sem Livewire nas views do garçom)
- Laravel Reverb (broadcast de eventos)
- Pest v3 para testes

---

## Arquivos do Módulo

```
app/
  Http/Controllers/Waiter/
    TableController.php     — index, open
    OrderController.php     — show, addItem, removeItem, confirm
  Events/
    OrderConfirmed.php      — broadcast no canal 'orders' (evento 'order.confirmed')
  Models/
    Table.php               — status: free | occupied | waiting_payment
    Order.php               — status: open | closed
    OrderItem.php           — status: pending | printing | delivered
    OrderItemOption.php     — snapshots das opções no momento do pedido
    PrintJob.php            — fila de impressão para agente Node.js
    Product.php
    ProductOption.php       — type: toggle | select | extra | text
    ProductOptionChoice.php — price_add: acréscimo no preço

resources/views/waiter/
  tables.blade.php          — grade de mesas com status colorido
  order.blade.php           — cardápio + resumo + modal de opções
```

---

## Rotas

```
GET  /garcom/mesas                           waiter.tables          TableController@index
POST /garcom/mesas/{table}/abrir             waiter.tables.open     TableController@open
GET  /garcom/pedido/{order}                  waiter.orders.show     OrderController@show
POST /garcom/pedido/{order}/item             waiter.orders.item.add OrderController@addItem
DEL  /garcom/pedido/{order}/item/{item}      waiter.orders.item.remove OrderController@removeItem
POST /garcom/pedido/{order}/confirmar        waiter.orders.confirm  OrderController@confirm
```

---

## Schema Relevante

```
tables:              id, number, status (free/occupied/waiting_payment)
orders:              id, table_id, user_id, status (open/closed), total, opened_at, closed_at
order_items:         id, order_id, product_id, quantity, unit_price*, notes, status, print_sequence, printed_at
order_item_options:  id, order_item_id, option_id, choice_id, text_value, price_delta*
products:            id, category_id, name, price, active, order
product_options:     id, product_id, label, type (toggle/select/extra/text), required, order
product_option_choices: id, option_id, label, price_add, order
print_jobs:          id, order_item_id, status (pending/sent/failed), payload (JSON), attempts, error_message, sent_at

* unit_price e price_delta são snapshots — não mudam se o admin alterar preços depois
```

---

## Regras de Negócio

### Mesas
- `free` → garçom pode abrir → cria `Order` (status `open`) e muda mesa para `occupied`
- `occupied` → garçom acessa o pedido aberto
- `waiting_payment` → caixa está processando, garçom não interage

### Pedido
- Fica `open` até o caixa fechar (o garçom pode adicionar itens a qualquer momento)
- `authorizeOrder()` aborta 403 se `order.status != open` ou `table.status != occupied`

### Itens
- `pending` → visível no pedido, pode ser removido pelo garçom
- `printing` → enviado para impressão (não pode mais remover)
- `delivered` → entregue na mesa

### Confirmação (`confirm`)
- Pega apenas itens com `status = pending`
- Atribui `print_sequence` sequencial (continua do maior existente no pedido)
- Cria um `PrintJob` por item com payload JSON completo
- Atualiza itens para `status = printing`
- Dispara evento `OrderConfirmed` via Reverb no canal `orders`
- Garçom pode confirmar múltiplas vezes (a cada rodada de itens novos)

### Cálculo do Total
- `order.total` é recalculado do zero a cada `addItem` / `removeItem`
- Fórmula por item: `(unit_price + sum(options.price_delta)) × quantity`

---

## Payload do PrintJob

```json
{
  "sequence": 3,
  "table": 5,
  "waiter": "João",
  "order_id": 42,
  "timestamp": "2026-04-10T15:30:00-03:00",
  "item": {
    "id": 17,
    "product": "Espeto de Frango",
    "quantity": 2,
    "notes": "bem passado",
    "options": [
      { "label": "Ponto", "choice": "Ao ponto", "text_value": null },
      { "label": "Adicional", "choice": "Queijo extra", "text_value": null }
    ]
  }
}
```

---

## Evento OrderConfirmed

- **Classe:** `App\Events\OrderConfirmed`
- **Canal:** público `orders`
- **Nome do evento:** `order.confirmed`
- **Payload broadcast:** `order_id`, `table`, `waiter`, `print_jobs[]` (id + payload)
- Consumido pelo agente Node.js que escuta via Reverb

---

## Views — Decisões de Implementação

### tables.blade.php
- Grade 3 colunas, cards coloridos: verde (free), vermelho (occupied), amarelo (waiting_payment)
- Mesa livre → botão "Abrir" que faz POST em `waiter.tables.open`
- Mesa ocupada → link "Ver pedido" para `waiter.orders.show`

### order.blade.php
- **Alpine.js component:** `orderPage(categories)` recebe todos os produtos/opções como JSON via `@json($categoriesData)`
- **Tabs:** Cardápio | Pedido (badge com contagem de itens pendentes)
- **Modal (bottom-sheet):** abre ao clicar em produto, renderiza opções com `<template x-if>` por tipo
- **Submit do modal:** `addToOrder()` adiciona hidden inputs dinamicamente no `<form x-ref="addForm">` e chama `form.submit()` — POST normal, sem AJAX
- **Sticky footer:** botão "Confirmar N itens" só aparece se há itens pendentes
- **Remover item:** form com `@method('DELETE')` por item (apenas pending)

### Tipos de Opção no Modal
| Tipo | UI | Valor enviado |
|---|---|---|
| `toggle` | Checkbox | `options[i][option_id]` (sem choice_id) |
| `select` | Radio group | `options[i][option_id]` + `options[i][choice_id]` |
| `extra` | Checkboxes múltiplos | `options[i][option_id]` + `options[i][choice_id]` por escolha |
| `text` | Textarea | `options[i][option_id]` + `options[i][text_value]` |

---

## Estado Alpine — `orderPage()`

```js
{
  tab: 'menu' | 'order',
  showModal: bool,
  prod: ProductObject | null,  // produto selecionado
  qty: int,                    // quantidade
  notes: string,               // observação livre
  optVals: {},    // { [optionId]: value }  — toggle(bool), select(choiceId), text(string)
  extraVals: {},  // { ['optId_choiceId']: bool }  — para tipo 'extra'
}
```

---

## Pendências / Próximos Passos

- Controllers do caixa: `App\Http\Controllers\Cashier\OrderController` (index, show, close)
- Controllers admin: DashboardController, ProductController, CategoryController, UserController, AdminTableController
- Agente Node.js que consome `PrintJob` via polling ou Reverb
- Testes Pest para `TableController` e `OrderController`
- Tratamento de erro quando `OrderConfirmed` falha no broadcast
