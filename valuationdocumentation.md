# Property Valuation Module — Documentation

This document explains the complete property valuation flow in the APIGharJagga MIS project: every screen, route, controller step, database table, and the parts that are still unimplemented. It also describes how to finish the module (valuator assignment → report → approval → PDF).

---

## 1. Overview

The valuation module is built around the **Annex-C "Property Valuation Request Form"** (`AGJ-FRM-04 / ANNEX-C`). A property owner fills a 7-step bilingual (English / नेपाली) wizard, submits it, and the backend creates the client, the property, and a valuation request in one database transaction.

Key architectural facts:

- **No Eloquent models.** All writes use the raw query builder (`DB::table(...)`).
- **No Filament admin resource.** Submitted requests have no admin UI yet.
- **Front half only.** Intake (request + document checklist) is fully implemented. The back half (valuator assignment, field visit, valuation report, approval, PDF) has database schema but **no code**.

There is also a separate, unrelated **client-side "instant home estimate"** marketing widget (`HomeValuation.vue`) that computes a fake number in the browser and never touches the server. It is documented in section 9 to avoid confusion, but it is **not** part of the Annex-C valuation module.

---

## 2. Component map

| Layer | File | Purpose |
|-------|------|---------|
| Entry link | `resources/js/pages/Marketplace/PropertyDetail.vue` (~line 579) | "Request Official Valuation" button → `/annex-c` |
| Frontend form | `resources/js/pages/AnnexC.vue` | 7-step wizard, ~3600 lines |
| Routes | `routes/web.php:86-90` | `GET/POST /annex-c` |
| Controller | `app/Http/Controllers/ValuationRequestController.php` | `create()` + `store()` |
| Migration | `database/migrations/2026_08_14_102200_create_valuation_requests_table.php` | `valuation_requests` table |
| Migration | `database/migrations/2026_08_14_102300_create_valuation_request_documents_table.php` | `valuation_request_documents` table |
| Migration | `database/migrations/2026_08_14_102400_create_valuation_reports_table.php` | `valuation_reports` table (schema only, unused) |
| Wayfinder helper | `resources/js/actions/App/Http/Controllers/ValuationRequestController.ts` | Auto-generated JS route bindings |

Lookup data feeding the flow (seeders):

- `DocumentTypesSeeder.php` — must contain the `doc_name` values the controller maps to (see section 6).
- `ServiceTypesSeeder.php` — `'Valuation'` service type.
- `RolesSeeder.php` — `'Valuation Officer'` staff role.

---

## 3. End-to-end flow (happy path)

```
User on property detail page
      │  clicks "Request Official Valuation"
      ▼
GET /annex-c ──> ValuationRequestController@create ──> Inertia renders AnnexC.vue
      │
      ▼
User fills 7 steps, agrees to declaration
      │  nextOrSubmit() on last step
      ▼
axios POST /annex-c  (Accept: application/json, body = form object)
      │
      ▼
ValuationRequestController@store
      │  validate() ──> DB::transaction():
      │     1. insert permanent address
      │     2. insert current address
      │     3. upsert client (by citizenship_no)
      │     4. insert property address
      │     5. insert property (status = under_valuation)
      │     6. insert valuation_request (status = received)
      │     7. insert one valuation_request_documents row per doc type
      ▼
JSON 201 { success, message (Nepali), data:{ request_id, request_code, client_id, property_id } }
      │
      ▼
Frontend: alert() success ──> window.location.reload()
```

---

## 4. Frontend flow — `AnnexC.vue`

A single Inertia page holding a reactive `form` object (defined `AnnexC.vue:1480-1586`) and a 7-step wizard driven by `currentStep` / `maxStep`.

### Steps

1. **Applicant + addresses** — full name, father/mother name, citizenship no, mobile, email; permanent address; current address. A "same as permanent" checkbox triggers `copyPermanentAddress()` (`AnnexC.vue:1858`) which copies each permanent field into the current field.
2. **Property details** — property type, location (province/district/municipality/ward/tole), kitta no, area, map sheet no, ownership type, ownership certificate no.
3. **Building details** — year of construction, covered area, floors, structure type, building permit no, current condition, road access/width, facing direction.
4. **Purpose + valuation type** — `purpose_of_valuation`, `requested_valuation_type`, `remarks`.
5. **Documents checklist** — 7 Yes/No rows (see section 6).
6. **Site visit** — preferred visit date/time, site contact person name + mobile.
7. **Declaration + signature** — `declaration_agreed` (must be accepted), signature name, signature date.

### Option arrays (hard-coded in the Vue file)

- `propertyTypes` (`AnnexC.vue:1593`)
- `purposes` (`AnnexC.vue:1729`) — matches the DB enum: `bank_loan_mortgage`, `buying_selling`, `insurance`, `legal`, `investment_decision`, `other`.
- `valuationTypes` (`AnnexC.vue:1774`) — matches the DB enum: `market_value`, `forced_sale_value`, `government_value_reference`, `rental_value`.
- `documents` (`AnnexC.vue:1807`) — the 7 checklist keys.

### Submit — `nextOrSubmit()` (`AnnexC.vue:1891`)

- If not on the last step: advance `currentStep`, update `maxStep`, return.
- On the last step: if `declaration_agreed` is false, show a bilingual error and stop.
- Otherwise `POST /annex-c` with the whole `form` object and `Accept: application/json` (`AnnexC.vue:1942`).
- On success: `alert()` success message, then `window.location.reload()` (`AnnexC.vue:1966`) — the form is **not** re-populated; the returned `request_code` is only logged to the console, not shown to the user.
- On error: read `error.response.data.message` into `errorMessage`, else a generic bilingual fallback.

> Note: submission goes to the **web** route `/annex-c`, not an API route. It works because `axios` sends the CSRF cookie/header from the Laravel/Inertia setup.

---

## 5. Backend flow — `ValuationRequestController`

### `create()` (`ValuationRequestController.php:15`)

Returns `Inertia::render('AnnexC')`. No data passed in.

### `store()` (`ValuationRequestController.php:23`)

**Validation** (`lines 25-105`) — highlights:

- Required: `full_name`, `citizenship_no`, `mobile_no`, `property_type` (whitelist of 8), `province`, `district`, `municipality`, `ward_no`, `purpose_of_valuation` (whitelist of 6), `requested_valuation_type` (whitelist of 4), and `declaration_agreed` (`accepted`).
- `current_building_condition` restricted to `excellent|good|fair|poor`.
- `documents` is a nullable array; `email` must be a valid email; date fields validated as dates.

**Transaction** (`DB::transaction`, `line 107`) runs 7 steps in order. If any throws, the whole thing rolls back.

| Step | Table | Notes |
|------|-------|-------|
| 1 | `addresses` | permanent address → `$permanentAddressId` (`line 115`) |
| 2 | `addresses` | current address → `$currentAddressId` (`line 131`) |
| 3 | `clients` | **upsert by `citizenship_no`** (`line 147`). If found → update name/contact/addresses. If not → insert with `client_code = 'CL-' . strtoupper(Str::random(8))`, `client_type = 'owner'`, `nationality = 'Nepali'`, `mis_entry_status = 'pending'`, `is_active = 1` |
| 4 | `addresses` | property address → `$propertyAddressId` (`line 195`) — note: no `full_address_text` here |
| 5 | `properties` | `property_code = 'PROP-' . strtoupper(Str::random(8))`, `ownership_role = 'self'`, **`status = 'under_valuation'`** (`line 245`) |
| 6 | `valuation_requests` | `request_code = 'VAL-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6))` (`line 258`), `application_received_date = today`, **`status = 'received'`** (`line 291`) |
| 7 | `valuation_request_documents` | one row per document type — see section 6 |

**Fields never set by `store()`** (left for later back-half handling): `assigned_valuator_staff_id`, `field_visit_date`, and the entire `valuation_reports` table.

**Response** (`line 357`): JSON `201` with `success: true`, a Nepali success message, and `data = { request_id, request_code, client_id, property_id }`.

---

## 6. Document checklist mapping

Step 5's 7 checkboxes are booleans in `form.documents`. The controller maps each frontend key to a `document_types.doc_name` string (`ValuationRequestController.php:306-327`):

| Frontend key (`form.documents.*`) | `document_types.doc_name` looked up |
|-----------------------------------|-------------------------------------|
| `land_ownership_certificate` | `Land Ownership Certificate` |
| `citizenship_certificate` | `Citizenship Copy` |
| `land_revenue_receipt` | `Land Revenue Receipt` |
| `land_map_trace_map` | `Blueprint` |
| `building_approval_certificate` | `Building Approval Certificate` |
| `tax_clearance_certificate` | `Tax Clearance` |
| `other_documents` | `Other` |

For each mapping, the controller looks up the `document_types` row by `doc_name` and, **only if it exists**, inserts a `valuation_request_documents` row with `is_available = 1` if the checkbox was truthy else `0`.

> **Gotcha:** if a `doc_name` is missing from `document_types` (i.e. `DocumentTypesSeeder` was not run or uses different names), that row is silently skipped — no error, just no checklist entry. The frontend labels (e.g. `Land Map / Trace Map`, `Citizenship Certificate`) differ from the DB names the controller searches for (`Blueprint`, `Citizenship Copy`), so keep the controller map and the seeder in sync.

---

## 7. Database schema

### `valuation_requests` (migration `..._102200_...`)

- `request_id` PK, `request_code` string(30) unique
- `client_id` FK → `clients.client_id` (RESTRICT)
- `property_id` FK → `properties.property_id` (**CASCADE** on delete)
- `purpose_of_valuation` enum nullable (6 values)
- `requested_valuation_type` enum nullable (4 values)
- `preferred_visit_date`, `preferred_visit_time`, `site_contact_person_name`, `site_contact_mobile`
- `assigned_valuator_staff_id` nullable FK → `staff.staff_id`
- `field_visit_date` nullable
- `application_received_date` default `CURRENT_DATE`
- `status` enum default `received`: `received`, `site_visit_scheduled`, `in_progress`, `report_issued`, `cancelled`
- `remarks` text, `created_at`
- Index `idx_valuation_requests_property` on `property_id`

### `valuation_request_documents` (migration `..._102300_...`)

- `id` PK
- `request_id` FK → `valuation_requests.request_id` (**CASCADE**)
- `doc_type_id` FK → `document_types.doc_type_id`
- `is_available` boolean default false

### `valuation_reports` (migration `..._102400_...`) — schema only, **no code writes to it**

- `report_id` PK, `report_no` string(30) unique
- `request_id` FK → `valuation_requests` (RESTRICT), `property_id` FK → `properties` (RESTRICT)
- `valuation_type` enum NOT NULL (9 values incl. `mortgage_valuation`, `fair_value`, etc. — a wider set than the request form's 4)
- `valuated_amount` decimal(14,2) NOT NULL
- `rate_basis` text nullable
- `valuator_staff_id`, `approved_by_staff_id` nullable FKs → `staff.staff_id`
- `approval_status` enum default `draft`: `draft`, `pending_approval`, `approved`, `rejected`
- `digitally_signed` boolean, `report_file_ref` text, `issued_date`, `created_at`
- Index `idx_valuation_reports_property` on `property_id`

### Related

- `properties.status` enum includes `under_valuation` (migration `..._101200_...`), set in `store()` step 5 and mirrored in `PropertyResource.php:74`.
- The FKs reference a `staff` table (migration `..._100100_...`) but there is **no `App\Models\Staff` model**.

---

## 8. Status lifecycle

Request status (`valuation_requests.status`):

```
received ─> site_visit_scheduled ─> in_progress ─> report_issued
   └────────────────────────────────────────────> cancelled
```

Only `received` is ever written today (in `store()`). All later transitions require code that does not yet exist.

Report status (`valuation_reports.approval_status`), once the back half is built:

```
draft ─> pending_approval ─> approved
                   └───────> rejected
```

---

## 9. Separate feature — instant home estimate (NOT the Annex-C module)

- `resources/js/components/home/HomeValuation.vue` — `#home-valuation` section. Uses a `LOCATION_BASE_PRICES` map and `computeEstimate()` to produce an in-browser number. Explicitly disclaims it "is not an appraisal."
- Rendered by `MarketplaceLanding.vue`; linked from `HeroSearch.vue` (the "value" tab) and `AppFooter.vue`.
- It never calls the server and creates no `valuation_requests`. Treat it as a marketing widget only.

---

## 10. What is implemented vs missing

**Implemented (front half):**

- Public Annex-C form (all 7 steps).
- `create()` / `store()` with full validation and a 7-step transactional intake.
- Request + document-checklist persistence.

**Missing (back half):**

- No Eloquent models (`ValuationRequest`, `ValuationRequestDocument`, `ValuationReport`, `Staff`).
- No admin/staff UI (no Filament resource) to list or process requests.
- No valuator assignment, no field-visit scheduling (columns exist, unused).
- No `valuation_reports` writes — no report entry, approval workflow, or PDF.
- No user-facing confirmation with the `request_code` (only `alert()` + reload).

---

## 11. How to complete / extend the module

The following is a suggested build order to turn the intake-only module into a full workflow. Each step is independent enough to ship on its own.

### 11.1 Add Eloquent models (recommended first)

Create models so the rest of the work uses Eloquent instead of raw `DB::table()`:

- `app/Models/ValuationRequest.php` — PK `request_id`, `$incrementing`, relationships: `client()`, `property()`, `documents()` (hasMany `ValuationRequestDocument`), `report()` (hasOne `ValuationReport`), `assignedValuator()` (belongsTo `Staff`).
- `app/Models/ValuationRequestDocument.php` — PK `id`, `documentType()`, `request()`.
- `app/Models/ValuationReport.php` — PK `report_id`, `request()`, `property()`, `valuator()`, `approver()`.
- `app/Models/Staff.php` — PK `staff_id` (backs the existing `staff` table and all valuation FKs).

Set `protected $primaryKey`, `public $timestamps = false` where a table only has `created_at`, and `$fillable`/`$casts` to match the migrations.

### 11.2 Build an admin UI (Filament resource)

Create `app/Filament/Resources/ValuationRequestResource.php`:

- **Table**: `request_code`, client name, property code, `purpose_of_valuation`, `status`, `application_received_date`. Add a `status` filter.
- **View/Edit page**: show applicant, property, building, the document checklist (from `valuation_request_documents`), and site-visit fields.
- **Actions**:
  - *Assign valuator* — set `assigned_valuator_staff_id`, move `status` to `site_visit_scheduled`, set `field_visit_date`.
  - *Mark in progress* — `status = in_progress`.
  - *Issue report* — opens the report form (11.3), then `status = report_issued`.
  - *Cancel* — `status = cancelled`.

### 11.3 Valuation report + approval

- Form to create a `valuation_reports` row: pick `valuation_type` (the 9-value enum), enter `valuated_amount`, `rate_basis`, set `valuator_staff_id`. Generate `report_no` (e.g. `VALR-YYYYMMDD-XXXXXX`), start at `approval_status = draft`.
- Approval action: `pending_approval` → `approved` / `rejected`, recording `approved_by_staff_id` and `issued_date`; flip the parent request to `report_issued` on approval.

### 11.4 Report PDF

Mirror the existing PDF modules (`resources/views/pdf/complaint.blade.php`, `client_registration.blade.php`, and `kyc_verification.blade.php` — the last was just added):

- Create `resources/views/pdf/valuation_report.blade.php`.
- Add a controller method that loads the report + relations and renders with DomPDF (the package the other modules use), storing the file path in `valuation_reports.report_file_ref`.
- Add a route like `GET /valuation-report/{report}/pdf`.

### 11.5 User-facing confirmation

In `AnnexC.vue`'s success branch, instead of `alert()` + reload, show the returned `request_code` on a confirmation screen so the applicant can track their request.

### 11.6 Consistency fixes worth doing

- Reconcile the frontend document labels with the `document_types.doc_name` values the controller searches for (section 6 gotcha), or map by a stable `doc_type_id`/slug instead of a display name.
- Consider promoting the inline validation in `store()` to a `StoreValuationRequest` form request (matching the pattern in `app/Http/Requests/`).
- Align the request form's 4 valuation types with the report's 9-value enum, or document why they differ.

---

## 12. Quick reference

- **Open the form:** `GET /annex-c` (route name `annex-c.create`)
- **Submit:** `POST /annex-c` (route name `annex-c.store`), body = full form object, expects `application/json`
- **Success payload:** `{ success, message, data:{ request_id, request_code, client_id, property_id } }`, HTTP 201
- **Request code format:** `VAL-YYYYMMDD-XXXXXX`
- **Property code format:** `PROP-XXXXXXXX`; **Client code format:** `CL-XXXXXXXX`
- **Tables touched by `store()`:** `addresses` (×3), `clients`, `properties`, `valuation_requests`, `valuation_request_documents`
- **Tables defined but unused:** `valuation_reports`
