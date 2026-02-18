# HR SaaS

A multi-tenant HR management system built with Laravel 12, Inertia.js v2, and Svelte 5.

## Features

- **Multi-Tenancy**: Subdomain-based tenant isolation using [stancl/tenancy](https://tenancyforlaravel.com/)
- **Authentication**: Headless authentication via [Laravel Fortify](https://laravel.com/docs/fortify)
  - Login, registration, password reset
  - Two-factor authentication (2FA/TOTP)
  - Email verification
- **Employee Management**: Create, view, and manage employee records
- **User Settings**: Profile management, password changes, appearance preferences
- **Role-Based Access Control**: Permission management via [spatie/laravel-permission](https://spatie.be/docs/laravel-permission)
- **Admin Panel**: Tenant management for super admins

## Tech Stack

### Backend
- **PHP 8.5**
- **Laravel 12** - The PHP framework for web artisans
- **PostgreSQL** - Database
- **Redis** - Cache, queue, and sessions
- **Laravel Fortify** - Headless authentication backend
- **Laravel Wayfinder** - Type-safe route generation

### Frontend
- **Inertia.js v2** - SPA without the complexity
- **Svelte 5** - Reactive UI framework
- **Tailwind CSS v4** - Utility-first CSS
- **TanStack Table** - Headless UI for tables
- **bits-ui** - Unstyled accessible UI components
- **Lucide Svelte** - Icon library

### Development
- **Laravel Boost** - MCP server for Laravel development
- **Pest 4** - Testing framework
- **Laravel Pint** - Code formatter
- **Laravel Sail** - Docker development environment

## Requirements

- PHP >= 8.5
- Composer
- Node.js >= 20
- PostgreSQL
- Redis

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd hr-saas
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Update your `.env` file with your database and Redis credentials.

5. Add the central domain to your hosts file:
```bash
# /etc/hosts on Linux/Mac or C:\Windows\System32\drivers\etc\hosts on Windows
127.0.0.1 hr-saas.test
```

6. Run migrations:
```bash
php artisan migrate
```

7. Build assets:
```bash
npm run build
```

## Development

Start the development server:
```bash
composer run dev
```

This will start:
- PHP server at `http://hr-saas.test:8000`
- Queue worker
- Log viewer (Pail)
- Vite dev server

### Running Tests

Run all tests:
```bash
php artisan test
```

Run tests with filtering:
```bash
php artisan test --filter=testName
```

### Code Formatting

Format code with Pint:
```bash
vendor/bin/pint
```

Format frontend code:
```bash
npm run format
```

## Default Credentials

After running the seeders (`php artisan db:seed`), the following users are available:

### Super Admin (Central Domain)
| URL | Email | Password |
|-----|-------|----------|
| `http://hr-saas.test:8000` | `admin@hr-saas.test` | `password` |

### Demo Tenants

**Acme Corporation** (Pro Plan)
| URL | Email | Password | Role |
|-----|-------|----------|------|
| `http://acme.hr-saas.test:8000` | `john@acme.test` | `password` | Admin |
| `http://acme.hr-saas.test:8000` | `jane@acme.test` | `password` | Manager |
| `http://acme.hr-saas.test:8000` | `bob@acme.test` | `password` | Employee |

**Startup XYZ** (Free Plan)
| URL | Email | Password | Role |
|-----|-------|----------|------|
| `http://startup.hr-saas.test:8000` | `alice@startup.test` | `password` | Admin |
| `http://startup.hr-saas.test:8000` | `charlie@startup.test` | `password` | Employee |

> **Note:** Add the tenant subdomains to your hosts file:
> ```bash
> 127.0.0.1 acme.hr-saas.test startup.hr-saas.test
> ```

## Environment Variables

| Variable | Description |
|----------|-------------|
| `APP_NAME` | Application name |
| `APP_ENV` | Environment (local, production) |
| `APP_URL` | Application URL |
| `DB_CONNECTION` | Database connection (pgsql) |
| `DB_HOST` | Database host |
| `DB_PORT` | Database port |
| `DB_DATABASE` | Database name |
| `DB_USERNAME` | Database username |
| `DB_PASSWORD` | Database password |
| `TENANCY_CENTRAL_DOMAINS` | Central domain(s) for tenancy |
| `SESSION_DOMAIN` | Session cookie domain |
| `REDIS_HOST` | Redis host |
| `REDIS_PORT` | Redis port |

## Project Structure

```
├── app/
│   ├── Actions/          # Action classes
│   ├── Console/          # Artisan commands
│   ├── Exceptions/       # Exception handlers
│   ├── Http/             # Controllers, middleware, requests
│   ├── Models/           # Eloquent models
│   └── Services/         # Business logic services
├── database/
│   ├── factories/        # Model factories
│   ├── migrations/       # Database migrations
│   └── seeders/          # Database seeders
├── resources/
│   ├── css/              # Stylesheets
│   └── js/
│       ├── actions/      # Wayfinder-generated controller actions
│       ├── components/   # Svelte components
│       ├── layouts/      # Inertia layouts
│       ├── pages/        # Inertia pages
│       └── routes/       # Wayfinder-generated named routes
├── routes/               # Route definitions
├── tests/                # Pest tests
└── bootstrap/            # Framework bootstrap files
```

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).
