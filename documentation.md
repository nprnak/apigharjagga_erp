# User Dashboard UI Enhancement — Documentation

This document describes the UI/UX improvements made to the **User Dashboard** (`/dashboard`) in the API GharJagga MIS project.

---

## Overview

The user dashboard is built with **Filament v5** (Laravel admin panel framework) and serves as the primary workspace for registered users to manage KYC verification, property listings, and account activity.

The redesign focuses on:

- A clearer visual hierarchy and modern dark theme
- Interactive widgets with hover states, animations, and live data
- **Chart.js** charts (via Filament's built-in `ChartWidget`)
- **Alpine.js** micro-interactions (included with Filament/Livewire)
- Responsive grid layout that adapts from mobile to desktop

---

## Technology Stack Used

| Layer | Library / Tool | Purpose |
|---|---|---|
| Dashboard framework | **Filament v5** | Panel layout, widgets, tables, navigation |
| Charts | **Chart.js** (via Filament `ChartWidget`) | Doughnut chart for listing status breakdown |
| Interactivity | **Alpine.js** | Hover tracking, scroll-reveal on KYC card |
| Styling | **Tailwind CSS** + custom CSS | Dark theme, glass effects, animations |
| Backend | **Laravel Eloquent** | Live stats, recent listings, sparkline data |

No additional npm packages were required — Chart.js and Alpine.js ship with Filament.

---

## Files Added

| File | Description |
|---|---|
| `app/Filament/User/Widgets/ListingsStatusChart.php` | Doughnut chart showing Approved / Pending / Rejected listing counts |
| `app/Filament/User/Widgets/QuickActionsWidget.php` | Widget controller for the quick-action grid |
| `app/Filament/User/Widgets/RecentListingsWidget.php` | Table widget showing the 5 most recent property submissions |
| `resources/views/filament/user/widgets/quick-actions-widget.blade.php` | Interactive 2×2 action card grid (KYC, Add Listing, Marketplace, Manage) |

---

## Files Modified

| File | Changes |
|---|---|
| `app/Filament/User/Pages/Dashboard.php` | Registered new widgets; responsive 1/2/3-column grid |
| `app/Providers/Filament/UserPanelProvider.php` | Registered new widget classes |
| `app/Filament/User/Widgets/UserStatsWidget.php` | Added sparkline mini-charts on each stat card; 7-day submission trend |
| `resources/views/filament/custom-theme.blade.php` | Animated mesh background, widget entrance animations, stat hover effects, custom scrollbar |
| `resources/views/filament/user/widgets/kyc-status-widget.blade.php` | Animated progress bar, step counter, hover states on verification steps, scroll-reveal |

---

## Dashboard Layout (Top to Bottom)

### 1. KYC Status Widget (full width)

- Welcome card with user avatar initials, live KYC badge (Verified / Under Review / Needs Amendment / Unverified)
- Contextual message based on verification state
- Primary CTA buttons: **Complete KYC** / **Check Status** / **Add Property Listing**
- Admin rejection banner (when applicable)
- **Animated 4-step progress tracker** with fill bar showing completion percentage

### 2. Stats Overview (full width)

Four stat cards with **sparkline charts**:

| Stat | Data Source |
|---|---|
| KYC Identity Status | User's `kyc_verifications.status` |
| Active Listed Properties | Properties with `approval_status = approved` |
| Pending Review | Properties with `approval_status = pending` |
| Total Submissions | All user properties + 7-day submission trend chart |

### 3. Listing Status Chart + Quick Actions (side-by-side on desktop)

**ListingsStatusChart** — interactive doughnut chart (Chart.js):

- Segments: Approved (green), Pending (amber), Rejected (red)
- Hover offset animation, legend at bottom
- 68% cutout for modern donut appearance

**QuickActionsWidget** — 2×2 grid of action cards:

| Action | Link | State |
|---|---|---|
| KYC Verification | `/dashboard/kyc-verification-page` | Color changes when verified |
| Add Listing | `/dashboard/my-properties/create` | Locked until KYC approved |
| Browse Marketplace | `/properties` (new tab) | Always available |
| Manage Listings | `/dashboard/my-properties` | Always available |

Each card has icon scale-on-hover, gradient overlay, and arrow slide animation.

### 4. Recent Listings Table (full width)

Table widget showing up to 5 recent submissions with columns:

- Reference Code
- Property Type (badge)
- Location (municipality + district)
- Area
- Approval Status (colored badge with icon)
- Submitted (relative time, e.g. "2 hours ago")

Empty state prompts user to complete KYC first.

---

## Custom Theme Enhancements

Applied globally to the user Filament panel via `resources/views/filament/custom-theme.blade.php`:

- **Animated mesh gradient** background (subtle blue/indigo/emerald radial gradients)
- **Staggered widget fade-in** animation on page load
- **Stat card hover lift** with enhanced chart opacity
- **Chart canvas scale** on hover
- **Custom dark scrollbar** styling
- **Interactive card** utility class (`.interactive-card`) for lift + shadow on hover

---

## Responsive Behavior

| Breakpoint | Grid Columns | Layout |
|---|---|---|
| Mobile (`default`) | 1 column | All widgets stacked vertically |
| Tablet (`md`) | 2 columns | Chart and Quick Actions side-by-side |
| Desktop (`xl`) | 3 columns | Quick Actions spans 2 cols beside chart |

---

## How to View

1. Start the dev server: `composer dev` (or `php artisan serve` + `npm run dev`)
2. Log in as a regular user (not admin)
3. Navigate to **`/dashboard`**

---

## Future Enhancement Ideas

- Wire the legacy Inertia/Vue dashboard (`resources/js/pages/Dashboard.vue`) to a separate route if dual UI is needed
- Add real-time notifications via Filament broadcasts when KYC/listing status changes
- Property-type breakdown bar chart alongside the doughnut chart
- Export recent listings to PDF from the table widget

---

## Bug Fixes (August 18, 2026)

### Missing `dashboard` route name

Laravel auth controllers (`AuthenticatedSessionController`, `RegisteredUserController`, etc.) redirect to `route('dashboard')`, but Filament only registered `filament.user.pages.dashboard`. This caused **Route [dashboard] not defined** errors after login.

**Fix:** Added `/user/dashboard` named route in `routes/web.php` that redirects to the Filament user panel, mapping legacy `?tab=` params:

| Query param | Redirect target |
|---|---|
| `?tab=kyc` | `/dashboard/kyc-verification-page` |
| `?tab=listings` | `/dashboard/my-properties` |
| (default) | `/dashboard` |

### Recent Listings widget crash

The `RecentListingsWidget` used Filament's `TableWidget`, which caused `newQueryWithoutRelationships() on null` errors in some Livewire render cycles.

**Fix:** Replaced with a lightweight Blade-based widget (`recent-listings-widget.blade.php`) that loads properties directly without Filament table filters.

### KYC widget Alpine.js error

The KYC status widget used `x-intersect` (Alpine Intersect plugin), which is not loaded in Filament by default, causing silent JS failures.

**Fix:** Replaced with CSS `@keyframes widget-fade-in` animation.

### Stats widget SQL

The 7-day submission trend used `GROUP BY DATE(created_at)` which can fail under strict MySQL modes.

**Fix:** Switched to per-day `whereDate()` counts in a loop.

---

## Bug Fix: Broken/oversized icons and layout (August 18, 2026)

**Symptom:** On the user dashboard, icons (shield-check, chevron, status dots) rendered as huge, unstyled black shapes, the avatar/name stacked incorrectly, and grid layouts collapsed to a single column.

**Root cause:** Neither Filament panel (`AdminPanelProvider` / `UserPanelProvider`) had a custom Vite theme registered. Filament panels ship with a **pre-compiled, purged Tailwind CSS bundle** that only contains the utility classes Filament's own core package views use. Our custom Blade widgets (`resources/views/filament/**`) and custom `app/Filament/**` classes use many additional Tailwind utility classes (e.g. `h-12 w-12`, `rounded-2xl`, `grid-cols-2`, `bg-gradient-to-br`) that were **never compiled into any stylesheet**, so the browser rendered raw, unstyled HTML/SVG — giant unconstrained icons, collapsed flex layouts, default browser circle/box sizing.

**Fix:**

1. Created custom theme entry files that import Tailwind + Filament's base theme and scan our custom views for classes to compile:
   - `resources/css/filament/admin/theme.css`
   - `resources/css/filament/user/theme.css`
   ```css
   @import 'tailwindcss';
   @import '../../../../vendor/filament/filament/resources/css/theme.css';

   @source '../../../../app/Filament/**/*';
   @source '../../../../resources/views/filament/**/*';
   ```
2. Registered both entries in `vite.config.ts`'s `input` array.
3. Registered `->viteTheme('resources/css/filament/admin/theme.css')` and `->viteTheme('resources/css/filament/user/theme.css')` in the respective panel providers so Filament loads our compiled CSS instead of its default bundle.
4. Rebuilt assets with `npm run build` (also works automatically via `npm run dev` hot reload).

**Why this fixes it:** Now every Tailwind utility class used anywhere in `app/Filament/**` and `resources/views/filament/**` is scanned and compiled into the panel's stylesheet, so all custom widget styling (avatar sizing, icon sizing, grid layouts, gradients, badges) renders correctly.

**Verification:** The Filament login page (`/dashboard/login`, `/admin/login`) was confirmed to render correctly with the new theme active (dark background, correctly sized input fields and button). Users should hard-refresh (or restart `npm run dev`) to pick up the new compiled CSS.

---

## Feature: Dynamic Property Detail Page & Backend-Connected Inquiries (August 20, 2026)

### What changed

Previously, clicking a property card in `MarketplaceIndex.vue` just toggled an inline overlay within the same component (no real URL, no deep-linking), and the "Send Direct Inquiry" form was purely cosmetic — it never reached the backend. The Home page's featured carousel (`PropertyCarousel` / `PropertyCard`) wasn't clickable at all.

1. **New dynamic property detail page** — `resources/js/pages/Marketplace/PropertyDetail.vue`, served at `GET /properties/{listing}` via `MarketplaceController@show`. All data (price, specs, address, amenities, legal verification status, photos) comes from the database — nothing is hardcoded. Legally-sensitive identifiers (ownership certificate no., building permit no.) and the owner's personal contact info are intentionally excluded from the public payload.
2. **Both entry points now link to the same page:**
   - `MarketplaceIndex.vue` grid cards navigate via `router.visit('/properties/{id}')` instead of toggling local state. The old ~370-line inline detail overlay was removed.
   - `PropertyCard.vue` (used by the Home page's `PropertyCarousel`) is now wrapped in an Inertia `<Link>` to the same route.
3. **Real inquiries table** — new `property_inquiries` migration/model (`App\Models\PropertyInquiry`), submitted via `POST /inquiries` (`PropertyInquiryController@store`). The detail page's inquiry form posts here with axios and shows a live success/error state.
4. **Admin "Inquiries & Leads" section** — new `App\Filament\Resources\InquiryResource` (List/View/Edit) shows every submitted inquiry with the buyer's name/phone/email/message, which property/listing it relates to, and quick actions to mark a lead as *Contacted* or *Closed*.

### Files added

- `database/migrations/2026_08_20_070000_create_property_inquiries_table.php`
- `app/Models/PropertyInquiry.php`
- `app/Http/Controllers/PropertyInquiryController.php`
- `app/Filament/Resources/InquiryResource.php` (+ `Pages/ListInquiries.php`, `Pages/ViewInquiry.php`, `Pages/EditInquiry.php`)
- `resources/js/pages/Marketplace/PropertyDetail.vue`

### Files modified

- `routes/web.php` — added `properties.show` (`GET /properties/{listing}`) and `inquiries.store` (`POST /inquiries`).
- `app/Http/Controllers/MarketplaceController.php` — extracted a shared `visibleListingsQuery()` (so the carousel, search grid, and detail page all apply the same "only show approved/listed" visibility rule — a raw ID can't be guessed to view a hidden listing), added `show()` and `transformListingDetail()`.
- `app/Models/Property.php` — added `inquiries()` relation.
- `app/Providers/Filament/AdminPanelProvider.php` — registered the "Inquiries & Leads" navigation group.
- `resources/js/pages/Marketplace/MarketplaceIndex.vue` — removed the inline detail overlay and its dedicated state/handlers; cards now navigate to the real detail page.
- `resources/js/components/home/PropertyCard.vue` — wrapped in an Inertia `<Link>` to the detail page.
- `resources/js/types/marketplace.ts` — added `ListingDetail` type with the extra fields the detail page needs.

### Verification

- `php artisan migrate` ran the new table successfully; `php artisan route:list` shows `properties.show` and `inquiries.store` registered (and the auto-discovered `admin/inquiries` Filament routes).
- `npm run build` compiled `PropertyDetail.vue` and the updated `MarketplaceIndex`/`MarketplaceLanding` bundles with no errors.
- Hit `GET /properties/{id}` directly against the running dev server: a pending/draft listing correctly redirects back to `/properties` (visibility rule working), while an approved listing returns the `Marketplace/PropertyDetail` Inertia payload fully populated from the database.
- Submitted `POST /inquiries` with a valid CSRF/session cookie flow and received `201 Created` with the new `inquiry_id`.

---

## Enhancement: Admin Inquiry View Page Now Shows Full Property Details (August 20, 2026)

### What changed

The individual inquiry "View" page (`/admin/inquiries/{id}`) previously fell back to Filament's default behavior of rendering the *form* schema in a disabled state, because `InquiryResource` never defined an `infolist()`. That form only had a bare, un-dehydrated `property.property_code` field, so opening a specific inquiry did **not** show the property's type, area, or address — and there was no way to mark a lead as contacted without going back to the table row action.

1. **New `InquiryResource::infolist()`** — the View page now renders a proper read-only layout with four sections:
   - **Buyer / Tenant Details** — name, phone (copyable), email, received date.
   - **Property Inquired About** — a computed "Property" label (e.g. *"House in Bhaktapur"*), the property code (badge), a computed **full address** (tole/ward/municipality/district/province), property type, area, covered area, and the listing's purpose (sale/rent/etc.).
   - **Inquiry Message** — the buyer's message.
   - **Follow-up** — current status badge and any internal admin note.
2. **"Mark Contacted" / "Close" actions on the View and Edit pages** — previously these actions only existed as row actions on the table. They're now shared static helpers (`InquiryResource::markContactedAction()` / `markClosedAction()`) reused by the table, `ViewInquiry`, and `EditInquiry` pages, so an admin can update a lead's status directly from the detail page's header, not just from the list.
3. **Consistent detail also added to the Edit form** — the edit screen now shows the same computed property name/type/address (as read-only entries) above the editable Status/Internal Note fields, so admins editing a lead don't lose context on which property it's about.
4. **Eager loading** — `InquiryResource::getEloquentQuery()` now eager-loads `property.address` and `listing` for every inquiry query (table, view, edit), avoiding N+1 queries when rendering these new fields.

### Files modified

- `app/Filament/Resources/InquiryResource.php` — added `infolist()`, `getEloquentQuery()`, `propertyName()`/`propertyAddress()` helpers, and public `markContactedAction()`/`markClosedAction()` helpers reused by the table.
- `app/Filament/Resources/InquiryResource/Pages/ViewInquiry.php` — added the Mark Contacted / Close header actions.
- `app/Filament/Resources/InquiryResource/Pages/EditInquiry.php` — added the same header actions for consistency.

### Verification

- `php -l` on all three modified files reported no syntax errors.
- `php artisan optimize:clear` ran cleanly (config/route/view/Filament caches rebuilt without error).
- Verified via a logged-in admin browser session that `/admin/inquiries/{id}` renders the property code, computed property name, full address, buyer details, and the Mark Contacted/Close header actions, and that clicking "Mark Contacted" updates the status badge and shows a success notification.

---

*Last updated: August 20, 2026*
