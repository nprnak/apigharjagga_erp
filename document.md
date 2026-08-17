# Api Ghar Jagga MIS — Form Work Documentation

This document records the work done to align the web application with official annex forms:

1. **ANNEX-A** — Property Listing Application Form (`AGJ-FRM-001`)
2. **ANNEX-B (PO-PB)** — House and Land Sale/Purchase Agreement (`AGJ-FRM-002`)

Source PDFs:

- `1. ANNEX-A-Property Listing Application Form.pdf`
- `2. ANNEX-B (PO-PB) Agreement.pdf`

---

## How the work was approached

The same method was used for both annexes:

1. **Read the official PDF** and extract every section, field, checkbox, and option.
2. **Compare against the existing codebase** (Vue form, Laravel request validation, controller, models, migrations, PDF blade).
3. **Fill the gap** — either add missing UI fields that the backend already supported, or build a new vertical (form + API + storage + PDF) where nothing existed.
4. **Keep the same UI pattern** as the existing Property Listing wizard: Tailwind, emerald accents, step tracker, `FormField` component, success screen, and PDF download.

Stack: Laravel (Inertia + Vue 3), MySQL, DomPDF.

---

## Part 1 — ANNEX-A: Property Listing Application Form

### What was found

The **backend already matched Annex A**. These already accepted (and could persist) the annex fields:

- `app/Http/Requests/StorePropertyListingRequest.php`
- `app/Http/Controllers/PropertyListingController.php`
- tables: `clients`, `addresses`, `properties`, `property_listings`

The **frontend did not**. `resources/js/pages/PropertyListing/PropertyListingForm.vue` was a shortened wizard. Many annex fields were never shown, so users could not fill them even though the API would have saved them.

Office-use-only fields (Application No., Listing ID, Assigned Officer, Inspection/Valuation flags, Legal Verification, Listing Status, Seal) were **intentionally left off the public form**. They belong to staff workflow after submission.

### Gap analysis (Annex A vs form)

| Annex section | Status before this work |
|---|---|
| 1. Applicant Details | Partial — missing Father's Name, Grandfather's Name, Telephone No., Occupation |
| 2. Property Owner Details | Present (Self / Family / Representative / Company) |
| 3. Property Details | Partial — only 4 property types; no Land Information, Road fields, or Building Details |
| 3. Address of Property | Present (Province, District, Municipality, Ward, Tole, GPS) |
| 4. Purpose of Listing | Partial — missing Investment and Other |
| 5. Expected Price | Partial — missing Negotiable and Minimum Acceptable Price |
| 6. Documents Submitted | Entire section missing |
| 7. Property Features | Partial — only 5 of 13 checkboxes |
| 8. Owner's Declaration | Present |
| 9. Signatures | Partial — missing Date |
| 10. Office Use Only | Correctly omitted from public form |

### What was changed

**Only the Vue form was updated.** No new backend files or migrations were required.

File: `resources/js/pages/PropertyListing/PropertyListingForm.vue`

#### Step 1 — Applicant Details

Added:

- Father's Name (`father_name`)
- Grandfather's Name (`grandfather_name`)
- Telephone No. (`telephone_no`, 7–10 digits)
- Occupation (`occupation`)

Already present: Full Name (EN/NP), Citizenship No., Date of Birth, Mobile, Email, Permanent Address, Current Address.

#### Step 2 — Property Details

Property type expanded from 4 to 8 annex options:

- Land, House, Apartment, Commercial Building
- Office Space, Industrial Property, Agricultural Land
- Other (shows a specify input when selected)

New **Land Information** block:

- Kitta No., Area, Map Sheet No.
- Ownership Type (private / joint / other)
- Road Access (yes / no), Road Width, Facing Direction

New **Building Details** block (if applicable):

- Year of Construction, No. of Floors, Covered Area
- Structure Type (RCC / Load Bearing / Steel / Other)
- Roof Type, Parking, Water Supply, Electricity, Internet, Drainage

Location Data (Province, District, Municipality, Ward, Tole, GPS) was already there.

#### Step 3 — Listing Details (Purpose, Price, Documents, Features)

Purpose expanded to: Sale, Rent, Lease, Exchange, **Investment**, **Other** (+ specify).

Price expanded with:

- Expected Selling Price (existing)
- **Minimum Acceptable Price**
- Rental Amount (existing)
- **Negotiable** (Yes / No)

New **Documents Submitted** checklist:

- Citizenship Copy
- Land Ownership Certificate (Lalpurja)
- Tax Clearance
- Blueprint
- Building Completion Certificate
- Valuation Report
- Power of Attorney
- Utility Bills
- Photographs
- Other (free text)

Property Features expanded to the full annex list:

- Corner Plot, Blacktopped Road, Drinking Water, Electricity, Sewer, Internet
- School Nearby, Hospital Nearby, Market Nearby, Public Transport, Bank Nearby, Temple, Park
- Other (free text)

#### Step 4 — Declaration & Signature

Added **Date** (`applicant_date`) next to the electronic signature (typed full name).

### Field mapping (frontend → backend)

New form keys reuse the names already defined in `StorePropertyListingRequest`, so submit works without a controller change:

| Vue field | Stored in |
|---|---|
| `father_name`, `grandfather_name`, `telephone_no`, `occupation` | `clients` |
| `kitta_no`, `area`, `map_sheet_no`, `ownership_type`, road / building fields | `properties` |
| `property_type_other`, `purpose_other` | validated; used when type/purpose is `other` |
| `negotiable`, `minimum_acceptable_price` | `property_listings` |
| `submitted_documents`, `other_documents` | accepted by request (`submitted_documents` array) |
| `property_features`, `other_features` | accepted by request |
| `applicant_date` | accepted by request |

### Annex A user flow

```
GET /property-listing
        │
        ▼
Vue wizard (4 steps)
  1 Applicant → 2 Property → 3 Details → 4 Review
        │
        ▼
POST /property-listing
  StorePropertyListingRequest validates
  PropertyListingController@store (one DB transaction):
    1. Address (permanent)
    2. Address (current / property location + GPS)
    3. Client (applicant)
    4. Property
    5. PropertyListing (application_no like AGJ-YYYYMMDD-0001)
        │
        ▼
Success screen + PDF download
GET /property-listing/{id}/pdf
  → pdf/property_listing.blade.php (DomPDF)
```

---

## Part 2 — ANNEX-B (PO-PB): House and Land Sale/Purchase Agreement

### What was found

The database already had agreement tables from the original schema:

- `agreements` — sale_purchase vs listing_brokerage
- `agreement_parties` — seller / buyer / property_owner / company
- `agreement_witnesses`
- `agreement_expense_terms` (for listing/brokerage, not this annex)

There was **no** Vue form, controller, request class, routes, models, or PDF for filling Annex B.

Some annex fields also had **no columns**:

- House Description
- Four boundaries (East / West / North / South) — चार किल्ला
- Purchase price in words

Those are snapshots *at agreement time*, so they were added on `agreements`, not on `properties`.

Legal clauses that are standard text (Transfer of Ownership, Taxes, Breach, Dispute Resolution, Governing Law, Final Provisions) are shown in the UI and printed on the PDF. They are not extra input fields, except Place and Date of Agreement.

### What was created

#### 1. Migration

`database/migrations/2026_08_16_150000_add_property_snapshot_to_agreements_table.php`

Columns added to `agreements`:

- `house_description` (text, nullable)
- `boundary_east`, `boundary_west`, `boundary_north`, `boundary_south` (string 150, nullable)
- `total_price_words` (string 255, nullable)

This migration was run (`php artisan migrate`).

#### 2. Models

| File | Role |
|---|---|
| `app/Models/Agreement.php` | Agreement record; relations to property, parties, witnesses |
| `app/Models/AgreementParty.php` | Seller or buyer row; relation to Client |
| `app/Models/AgreementWitness.php` | Witness name + citizenship |

`Property` also gained `agreements()` (`HasMany`).

#### 3. Validation

`app/Http/Requests/StoreAgreementRequest.php`

Required (among others):

- Seller and Buyer: full name, citizenship no., permanent address, contact no.
- Property: type (land/house), district, municipality, ward, kitta no., area
- Boundaries: **required if property type is land**
- Total price + amount in words
- Seller and Buyer declaration checkboxes (`accepted`)
- Place and Date of Agreement

Optional: father/mother names, house description, advance/balance payment, final payment date, witnesses.

#### 4. Controller

`app/Http/Controllers/AgreementController.php`

- `index` — Inertia page `Agreement/AgreementForm`
- `store` — one DB transaction (see flow below)
- `downloadPdf` — DomPDF from `pdf.agreement`

On store, seller and buyer clients are **found or created by citizenship number** (`firstOrCreate`) so the same person is not duplicated if they already exist.

#### 5. Routes

In `routes/web.php`:

```
GET  /agreement            agreement.form     AgreementController@index
POST /agreement            agreement.store    AgreementController@store
GET  /agreement/{id}/pdf   agreement.pdf      AgreementController@downloadPdf
```

Reference number format: `AGJ-AGR-00001` (padded `agreement_id`).

#### 6. Vue form (same design as Annex A)

`resources/js/pages/Agreement/AgreementForm.vue`

Shared UI: min-h-screen slate background, emerald step tracker, `FormField`, error banner, fade-slide transitions, success card, PDF download button.

5 wizard steps:

| Step | Title | Content |
|---|---|---|
| 1 | Parties | First Party (Seller) and Second Party (Buyer): name, father/mother, citizenship, contact, permanent address |
| 2 | Property | Type (Land / House), District, Municipality, Ward, Kitta No., Area, House Description, Boundaries (चार किल्ला) |
| 3 | Price | Total price, amount in words, advance, balance, final payment date; read-only Transfer of Ownership clause |
| 4 | Declare | Seller declaration + checkbox + date; Buyer declaration + checkbox + date |
| 5 | Finalize | Clauses 7–10 (read-only), Witness 1 & 2, Place, Date of Agreement |

#### 7. PDF

`resources/views/pdf/agreement.blade.php`

Same visual language as `pdf/property_listing.blade.php` (Noto Devanagari, DejaVu, annex header). Covers:

- Parties
- Property + boundaries
- Price and payment terms
- Clauses 4–11
- Seller / Buyer signatures
- Two witnesses
- Place and date

Page break before clauses 7–11 so the printed document stays readable.

### Annex B data flow

```
GET /agreement
        │
        ▼
Vue wizard (5 steps)
  1 Parties → 2 Property → 3 Price → 4 Declare → 5 Finalize
        │
        ▼
POST /agreement  (JSON + CSRF)
  StoreAgreementRequest validates
  AgreementController@store (DB::transaction):

    1. Address          ← seller permanent address
    2. Client (seller)  ← firstOrCreate by citizenship_no, type = owner
    3. Address          ← buyer permanent address
    4. Client (buyer)   ← firstOrCreate by citizenship_no, type = buyer
    5. Address          ← property district / municipality / ward
    6. Property         ← kitta, area, type, owner = seller
    7. Agreement        ← sale_purchase, prices, boundaries, place, date
    8. AgreementParty   ← seller
    9. AgreementParty   ← buyer
   10. AgreementWitness ← witness 1 and 2 if names provided

        │
        ▼
JSON 201: { success, agreement_id, agreement_no }
        │
        ▼
Success screen
GET /agreement/{id}/pdf  →  pdf/agreement.blade.php
```

### Annex B field mapping

| Form field | Table / column |
|---|---|
| `seller_*` | `clients` (owner) + `addresses` |
| `buyer_*` | `clients` (buyer) + `addresses` |
| `district`, `municipality`, `ward_no` | `addresses` (property) |
| `property_type`, `kitta_no`, `area` | `properties` |
| `house_description`, `boundary_*`, `total_price_words` | `agreements` (new columns) |
| `total_price`, `advance_payment`, `balance_payment`, `final_payment_date`, `place`, `agreement_date` | `agreements` |
| seller / buyer party rows | `agreement_parties` |
| `witness1_*`, `witness2_*` | `agreement_witnesses` |

---

## Shared design system

Both public forms follow the same pattern so users see one product, not two:

| Element | Implementation |
|---|---|
| Page chrome | `min-h-screen bg-slate-50`, Instrument Sans via Vite |
| Header chip | Document code + version (`AGJ-FRM-001` / `AGJ-FRM-002`) |
| Progress | Circular steps, emerald fill, click-back to completed steps |
| Fields | Shared `resources/js/components/FormField.vue` |
| Inputs | Rounded-xl, emerald focus ring, red error state |
| Options | Radio pills / cards, checkbox tiles |
| Success | Gradient emerald card, mono reference number, PDF button |
| PDF | A4, bilingual headings, dotted value lines, signature boxes |

Client-side step validation runs before Continue. Server-side 422 errors map back onto the same fields and the global error banner.

---

## Files touched in this session

### Annex A (updated)

- `resources/js/pages/PropertyListing/PropertyListingForm.vue`

### Annex B (new)

- `database/migrations/2026_08_16_150000_add_property_snapshot_to_agreements_table.php`
- `app/Models/Agreement.php`
- `app/Models/AgreementParty.php`
- `app/Models/AgreementWitness.php`
- `app/Http/Requests/StoreAgreementRequest.php`
- `app/Http/Controllers/AgreementController.php`
- `resources/js/pages/Agreement/AgreementForm.vue`
- `resources/views/pdf/agreement.blade.php`

### Annex B (updated)

- `app/Models/Property.php` — `agreements()` relation
- `routes/web.php` — three agreement routes

---

## How to use

| Form | URL |
|---|---|
| Property Listing (Annex A) | `/property-listing` |
| Sale/Purchase Agreement (Annex B) | `/agreement` |

After submit, use the success-screen **Download PDF Document** button, or:

- `/property-listing/{listing_id}/pdf`
- `/agreement/{agreement_id}/pdf`

---

## Checks run

| Check | Result |
|---|---|
| `php artisan migrate` | Snapshot columns added to `agreements` |
| `php artisan route:list --path=agreement` | All three routes registered |
| `php -l` on new PHP files | No syntax errors |
| `vue-tsc --noEmit` | No new errors from these forms (existing unrelated warning in `resources/js/app.ts`) |
| `npm run build` | Succeeded; `AgreementForm` and `PropertyListingForm` both emitted |

---

## Out of scope (not built here)

- **ANNEX-B (PO-RA)** listing/brokerage agreement (uses `commission_*`, expense terms). Schema exists; no form yet.
- Staff “Office Use Only” screens for Annex A section 10.
- Persisting scanned signatures (forms use typed name + date).
- File uploads for Annex A “Documents Submitted” — checklists are stored as selected types, not uploaded files.
- Deduplicating the same physical property if it was already listed via Annex A before an Annex B sale.

---

## Annex A vs Annex B at a glance

| | Annex A | Annex B (PO-PB) |
|---|---|---|
| Purpose | List a property for sale/rent/etc. | Bind seller and buyer to a sale |
| Document code | AGJ-FRM-001 | AGJ-FRM-002 |
| Wizard steps | 4 | 5 |
| Primary records | Client, Property, PropertyListing | Two Clients, Property, Agreement, Parties, Witnesses |
| Price | Expected / minimum / rent | Binding total + advance + balance |
| Public URL | `/property-listing` | `/agreement` |
| Backend before this work | Complete | Tables only |
| UI before this work | Incomplete | None |
