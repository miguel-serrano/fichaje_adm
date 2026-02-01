# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Fichajes App** — a Laravel 12 employee time-tracking system (clock-in/out) with Vue 3 + Inertia.js frontend. Spanish-localized, Europe/Madrid timezone. Runs in Docker via Laravel Sail.

## Common Commands

All commands use the Makefile and run through Laravel Sail (Docker):

```bash
make up                # Start containers (Laravel, MySQL 8.4, Elasticsearch 8.15)
make down              # Stop and remove containers
make test              # Run PHPUnit tests
make test-filter filter=ClassName  # Run a specific test class
make pint              # Format PHP code with Laravel Pint
make migrate           # Run database migrations
make fresh             # Reset database with migrations + seeds
make npm-dev           # Start Vite dev server
make npm-build         # Production frontend build
make shell             # Access container shell
```

Alternative: `composer dev` runs Laravel server, queue worker, log tail, and Vite concurrently.

## Architecture

The app follows **Domain-Driven Design (DDD) with hexagonal architecture**. All business logic lives in `app/DDD/`.

### Module Structure

Each domain module in `app/DDD/Backoffice/` follows this layout:

```
ModuleName/
├── Domain/
│   ├── Entity/          # Domain entities
│   ├── ValueObject/     # Immutable value objects
│   ├── Event/           # Domain events
│   ├── Exception/       # Domain exceptions
│   ├── Interface/       # Repository contracts
│   ├── Service/         # Domain services
│   └── Voter/           # Authorization voters
├── Application/
│   ├── Command*/        # Write operations (command + handler)
│   ├── Query*/          # Read operations (query + handler)
│   ├── Subscriber/      # Domain event listeners
│   └── Request/         # Validation objects
└── Infrastructure/
    ├── Http/Controller/  # Controllers (Inertia + API)
    ├── Http/Request/     # Form requests
    ├── Http/Resource/    # Response DTOs
    ├── Repository/       # Eloquent repository implementations
    ├── Mapper/           # Entity ↔ DTO mapping
    └── Service/          # Infrastructure services
```

### Domain Modules

- **ClockIn** — Core time tracking (clock-in/out, breaks, worked hours reports, late arrival detection, geofence violations)
- **Employee** — Employee management with contracts, weekly schedules (planifications), workplace assignments
- **Workplace** — Location management with geographic coordinates for geofencing
- **Notification** — Event-triggered user notifications
- **Dashboard** — Analytics and reporting
- **Authorization** — RBAC (roles and permissions)

### Shared Infrastructure (`app/DDD/Shared/`)

- **Event Bus** — Command bus, query bus, and domain event bus (CQRS pattern)
- **Domain Events** — Outbox pattern with MySQL persistence + optional Elasticsearch sync
- **Elasticsearch** — Event search and statistics (disabled in tests via `ELASTICSEARCH_ENABLED`)
- **GeoIP Provider** — IP-based geolocation (controlled by `GEOIP_ENABLED`)

### Frontend

- Vue 3 + Inertia.js pages in `resources/js/Pages/`
- Layout: `resources/js/Layouts/BackofficeLayout.vue`
- Reusable components in `resources/js/Components/`
- Tailwind CSS v4 for styling
- Route helpers via Ziggy

### Routing

- `routes/web.php` — Public routes (welcome, auth)
- `routes/backoffice.php` — Authenticated backoffice routes (employees, workplaces, clock-ins, notifications, events)
- `routes/api.php` + `routes/events.php` — Sanctum-protected API (health check, event search/sync)

## Testing

Tests mirror the DDD structure under `tests/Feature/DDD/Backoffice/` and `tests/Unit/DDD/`. PHPUnit config uses a separate `testing` database with array cache/session, sync queue, and Elasticsearch disabled.

## Docker Services

| Service         | Internal Port | Forwarded Port |
|-----------------|--------------|----------------|
| Laravel (Sail)  | 80           | 8080           |
| Vite            | 5173         | 5174           |
| MySQL 8.4       | 3306         | 3307           |
| Elasticsearch   | 9200         | 9201           |
