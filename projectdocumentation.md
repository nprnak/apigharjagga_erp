# API GharJagga MIS Project Documentation

## 1. Project Overview

API GharJagga MIS is a Laravel-based property and client management system. It supports property listings, a public marketplace, KYC verification, client registration, agreements, complaints, valuation requests, inquiries, and administrative approval workflows.

The application uses two frontend approaches:

- **Laravel and Filament** for authenticated admin and user dashboards.
- **Vue 3, Inertia, and Vite** for public pages and interactive application screens.

## 2. Technology Stack

| Technology | Purpose |
|---|---|
| Laravel 13 | Backend framework, routing, middleware, validation, and application services |
| PHP 8.3+ | Server-side programming language |
| Filament 5 | Admin and user dashboard panels |
| Vue 3 | Frontend components and pages |
| Inertia.js | Connects Laravel controllers to Vue pages |
| Vite | Frontend asset development and production builds |
| Tailwind CSS | Utility-first styling |
| Eloquent ORM | Database models and relationships |
| Dompdf | PDF generation for agreements, complaints, registrations, and listings |
| PHPUnit | Automated testing |
| Larastan/PHPStan | Static analysis |

## 3. Main File Structure

```text
APIGharJaggaMIS/
├── app/
│   ├── Filament/          # Filament resources, user pages, and dashboard widgets
│   ├── Http/
│   │   ├── Controllers/   # Request handlers and business workflow entry points
│   │   ├── Middleware/    # Authentication, authorization, and request middleware
│   │   └── Requests/      # Form request validation classes
│   ├── Models/            # Eloquent models for application data
│   └── Providers/         # Laravel and Filament service providers
├── bootstrap/             # Laravel application bootstrap configuration
├── config/                # Framework and package configuration
├── database/
│   ├── factories/         # Test and seed data factories
│   ├── migrations/        # Database table and schema changes
│   └── seeders/           # Initial and sample database data
├── public/                # Public entry point and published assets
├── resources/
│   ├── css/               # Application and Filament theme styles
│   ├── js/                # Vue, Inertia, TypeScript, and frontend modules
│   └── views/              # Blade layouts, Filament views, and PDF templates
├── routes/                # Web, authentication, and console routes
├── storage/               # Logs, cache, generated files, and uploaded files
├── tests/                 # Feature and unit tests
├── artisan                 # Laravel command-line entry point
├── composer.json           # PHP dependencies and Composer scripts
├── package.json            # JavaScript dependencies and npm scripts
├── phpunit.xml             # PHPUnit test configuration
├── phpstan.neon            # PHPStan/Larastan configuration
├── pint.json               # PHP code style configuration
├── tsconfig.json           # TypeScript configuration
└── vite.config.ts          # Vite entry points and asset build configuration
```

## 4. Important Backend Directories and Files

### `app/Models/`

Contains the Eloquent models used to read and write application data. Important models include:

- `User.php` - User accounts, roles, and authenticated ownership.
- `Property.php` - Property records and their approval state.
- `PropertyListing.php` - Marketplace listing information.
- `PropertyInquiry.php` - Inquiries submitted for marketplace properties.
- `KycVerification.php` - User identity and KYC verification details.
- `Client.php` and related client models - Client registration, organizations, requirements, documents, and service requests.
- `Agreement.php`, `AgreementParty.php`, and `AgreementWitness.php` - Agreement records and participants.
- `Complaint.php` and `ComplaintEvidence.php` - Complaint records and supporting evidence.
- `Address.php`, `PropertyPhoto.php`, and `ClientDocument.php` - Supporting property, client, and address data.

Models define database relationships, fillable fields, casts, and domain-specific query behavior.

### `app/Http/Controllers/`

Controllers receive HTTP requests, validate or delegate input, load models, return views/Inertia pages, and redirect after form submissions.

| Controller | Responsibility |
|---|---|
| `MarketplaceController.php` | Public marketplace landing page, property search, and property detail pages |
| `PropertyController.php` | Authenticated property submission |
| `PropertyListingController.php` | Property listing forms, submission, and listing PDF downloads |
| `PropertyInquiryController.php` | Saves inquiries submitted from the marketplace |
| `KycController.php` | Stores KYC verification information |
| `AdminApprovalController.php` | Approves or rejects administrative submissions |
| `ClientRegistrationController.php` | Client registration and generated PDFs |
| `AgreementController.php` | Agreement creation and PDF downloads |
| `ComplaintController.php` | Complaint submission and complaint PDFs |
| `ValuationRequestController.php` | Valuation request forms and submissions |
| `ProfileController.php` | Authenticated profile editing and deletion |

### `app/Filament/`

Contains the Filament dashboard implementation:

- `Resources/` - Filament resource definitions, forms, tables, and CRUD pages.
- `User/` - User-facing Filament pages and dashboard configuration.
- `Widgets/` - Dashboard statistics, listing status, quick actions, and recent activity widgets.
- `Providers/` - Panel configuration, navigation, authentication, and theme registration.

### Where the Filament designs are located

The Filament interface is split between PHP configuration/classes, Blade markup, and CSS themes:

| Location | What it controls |
|---|---|
| `app/Providers/Filament/AdminPanelProvider.php` | Admin panel ID, URL, authentication, navigation, resources, and admin theme |
| `app/Providers/Filament/UserPanelProvider.php` | User panel ID, URL, authentication, navigation, pages, widgets, and user theme |
| `app/Filament/Resources/` | Admin CRUD designs for clients, inquiries, KYC verification, properties, and users |
| `app/Filament/User/Pages/` | User-facing dashboard pages and page-level behavior |
| `app/Filament/User/Resources/` | User-facing resource pages and forms |
| `app/Filament/User/Widgets/` | Dashboard cards, charts, quick actions, and recent listings |
| `resources/views/filament/` | Blade templates that render custom Filament dashboard layouts and widgets |
| `resources/views/filament/custom-theme.blade.php` | Shared custom Filament visual styling and dashboard effects |
| `resources/views/filament/user/` | User dashboard widget markup and user-panel-specific views |
| `resources/css/filament/admin/theme.css` | Tailwind theme entry point for the admin panel |
| `resources/css/filament/user/theme.css` | Tailwind theme entry point for the user panel |

To change the **structure or behavior** of a Filament screen, start in the matching PHP class under `app/Filament/`. To change the **HTML layout or displayed content**, edit the matching Blade file under `resources/views/filament/`. To change **colors, spacing, typography, animations, or responsive styling**, edit the relevant theme CSS file under `resources/css/filament/` or shared styles in `resources/views/filament/custom-theme.blade.php`.

### `database/migrations/`

Migrations create and update the database schema. The schema covers users and roles, clients, properties, listings, photos, documents, KYC, inspections, verification, valuations, agreements, complaints, service workflows, handovers, audit logs, and property inquiries.

Run migrations with:

```bash
php artisan migrate
```

## 5. Routing and Request Flow

### `routes/web.php`

Defines the application's browser routes, including:

- `/` - Marketplace landing page.
- `/properties` - Public property marketplace.
- `/properties/{listing}` - Property detail page.
- `/inquiries` - Marketplace inquiry submission.
- `/property-listing` - Property listing form and submission.
- `/kyc` - Authenticated KYC submission.
- `/user/dashboard` - Named authenticated dashboard redirect used by auth flows.
- `/agreement` - Agreement form, submission, and PDF download.
- `/client-registration` - Client registration form, submission, and PDF download.
- `/complaint` - Complaint form, submission, and PDF download.
- `/annex-c` - Valuation request form and submission.

### `routes/auth.php`

Contains login, registration, logout, password reset, and email verification routes supplied by Laravel Breeze.

### `bootstrap/app.php`

Creates the Laravel application and registers:

- Web, console, and health routes.
- Inertia request handling middleware.
- Preloaded asset link headers.
- The `admin` authorization middleware alias.
- JSON exception behavior for API and JSON requests.

## 6. Frontend Structure

### `resources/js/`

- `app.ts` - Main Vue/Inertia application bootstrap.
- `pages/` - Inertia page components such as marketplace, dashboard, forms, and detail pages.
- `components/` - Reusable Vue UI components.
- `Layouts/` - Shared page layouts and navigation.
- `actions/` and `routes/` - Generated or shared frontend route helpers.
- `types/` - TypeScript types used by the frontend.
- `lib/` - Shared frontend utilities.

### `resources/views/`

- `app.blade.php` - Main Blade shell used to mount the frontend application.
- `filament/` - Filament panel layouts, widgets, and custom dashboard views.
- `pdf/` - Blade templates rendered into PDF documents.

### `resources/css/`

Contains application styling and custom Filament themes. Filament theme files include the Tailwind sources used by custom dashboard resources and widgets.

## 7. Configuration and Asset Files

- `composer.json` - PHP packages, Laravel scripts, testing, linting, and development commands.
- `package.json` - Vue, Inertia, Tailwind, Vite, ESLint, Prettier, and TypeScript packages/scripts.
- `vite.config.ts` - Vite configuration and frontend/Filament asset entry points.
- `config/` - Environment-aware Laravel configuration for the application, database, mail, filesystems, queues, sessions, and packages.
- `.env` - Local environment values. This file should remain private and is not committed.

## 8. Common Development Commands

Install the project and build assets:

```bash
composer install
npm install
php artisan migrate
npm run build
```

Run the application during development:

```bash
composer dev
```

This starts the Laravel server, queue listener, and Vite development server.

Run focused checks:

```bash
php artisan test
composer lint:check
composer types:check
npm run lint:check
npm run types:check
```

## 9. Typical Feature Workflow

1. A user opens a browser route defined in `routes/web.php`.
2. Laravel sends the request to a controller or Filament page.
3. Form requests validate incoming data where applicable.
4. Controllers use Eloquent models to load or persist records.
5. The response is returned as a Blade view, PDF, redirect, or Inertia/Vue page.
6. Admin or user dashboard widgets display current model data through Filament.
7. Database changes are introduced through a new migration in `database/migrations/`.

## 10. Development Notes

- Keep database changes in migrations rather than editing the database manually.
- Keep validation in form request classes or the owning controller workflow.
- Use Eloquent relationships instead of duplicating database queries in views.
- Add new PDF layouts under `resources/views/pdf/` and connect them through the relevant controller.
- Add frontend pages under `resources/js/pages/` and reusable UI under `resources/js/components/`.
- Run the relevant tests and static checks after changing backend workflows or shared frontend code.
