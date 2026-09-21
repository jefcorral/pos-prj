# POS — Point of Sale

A production-ready Point-of-Sale web application for small to medium-sized businesses, built as a modular Laravel monolith.

## Stack

- **Backend:** Laravel 13, PHP 8.4+, Fortify auth, Sanctum, Spatie Laravel Permission
- **Frontend:** Vue 3 + TypeScript, Inertia.js, Vite, Tailwind CSS 4, shadcn-vue-style UI kit, Wayfinder typed routes
- **Data:** SQLite (local dev/tests), PostgreSQL (Docker), Redis (cache/queues)
- **Testing:** Pest (feature tests for checkout, inventory, shifts, authorization)

## Features

- Multi-branch schema (`company_id` / `branch_id` on all business records)
- Products with SKU, barcode, variants, pricing, tax, low-stock thresholds, images
- Inventory ledger: every stock change is an auditable `inventory_movements` row
- Fast cashier POS: barcode scanning, search, cart, per-item & cart discounts, taxes, hold/resume, split payments (cash/card/GCash/Maya/bank), automatic change
- Sales lifecycle: draft → held → completed → voided / refunded (partial & full)
- Cashier shifts: opening cash, cash in/out, expected vs actual cash, variance
- Receipts: generated per sale, printable (80mm-ready markup)
- Customers, suppliers, purchase orders & goods receiving (auto stock-in)
- Dashboard & reports: daily sales, by product/category/cashier/payment method, inventory valuation, low-stock
- Roles & permissions: `admin`, `manager`, `cashier` (Spatie)
- Audit log on all sensitive actions (sales, refunds, voids, stock/cash adjustments, user changes)

## Architecture

```
app/
  Actions/        # CreateSale, HoldSale, VoidSale, RefundSale, AdjustInventory,
                  # OpenCashierShift, CloseCashierShift, RecordCashMovement, ReceiveGoods
  Services/       # PricingService (server-side totals), InventoryService (ledger + locking)
  Support/        # AuditLogger
  Enums/          # SaleStatus, PaymentMethod, InventoryMovementType, CashMovementType
```

Checkout is atomic: sale + items + payments + inventory movements + audit log are committed inside a single `DB::transaction`, with `lockForUpdate` on stock rows. All financial math is computed server-side by `PricingService` — the frontend only sends item ids, quantities and discount intents.

## Local development

Requirements: PHP 8.4+, Composer, Node 22+.

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install
npm run build        # or `npm run dev` for HMR
php artisan storage:link
php artisan serve
```

Demo credentials (from `DatabaseSeeder`):

| Role    | Email            | Password |
| ------- | ---------------- | -------- |
| Admin   | admin@pos.test   | password |
| Manager | manager@pos.test | password |
| Cashier | cashier@pos.test | password |

## Docker (PostgreSQL + Redis + Nginx)

```bash
cp .env.example .env
# set DB_CONNECTION=pgsql, DB_DATABASE=pos, DB_USERNAME=pos, DB_PASSWORD=secret,
# SESSION_DRIVER=redis, CACHE_STORE=redis, QUEUE_CONNECTION=redis
docker compose up -d --build
docker compose exec app php artisan migrate --seed
```

App is served at http://localhost:8080.

## Testing

```bash
php artisan test          # Pest
./vendor/bin/pint --test  # code style
npm run build             # frontend typecheck/build
```

## Roadmap (prepared, not built)

The schema and action layer are designed for: offline POS + IndexedDB sync, multi-branch stock transfers, loyalty programs, e-commerce/accounting integrations, customer displays, and AI-powered reporting (via a safe predefined-query layer — never arbitrary SQL).
