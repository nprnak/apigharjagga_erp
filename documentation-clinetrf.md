# Client Registration Form (Annex F) — How It Was Built

This file records the work done to implement **ANNEX–F / AGJ-FRM-07 Client Registration Form** (`ग्राहक दर्ता फारम`) in API Ghar Jagga MIS.

Source PDF:

`c:\Users\Acer\Downloads\documents-20260813T084204Z-1-001\documents\7. ANNEX-F-Client Registration Form.pdf`

---

## Approach

Same method as Annex A (property listing) and Annex B (sale/purchase agreement):

1. Read every section and field from the official PDF.
2. Map fields onto existing database tables where they already existed.
3. Add only the columns / tables the annex needs that were missing.
4. Build a Vue multi-step wizard using the **same visual system** (slate page, emerald step tracker, `FormField`, `SignatureUpload`, success card, PDF download).
5. Persist in one Laravel transaction and print an official PDF.

The public URL is `/client-registration`.

---

## Annex F sections vs implementation

| # | Annex section | In the form? | Stored in |
|---|---|---|---|
| 1 | Client Type (Owner, Buyer, Investor, Tenant, Agent, Other + specify) | Yes | `clients.client_type`, `clients.client_type_other` |
| 2 | Personal: Full Name, Father/Mother, Spouse, Citizenship No., Nationality, DOB, Gender, Occupation | Yes | `clients` |
| 3 | Contact: Mobile, Alternate Contact, Email, Permanent Address, Current Address | Yes | `clients` + `addresses` |
| 4 | Organization: Name, Registration No., PAN/VAT, Authorized Person, Designation, Office Address | Yes (optional) | `client_organizations` + office `addresses` |
| 5 | Property requirement (Purchase / Investment / Rent; Land / House / Apartment / Commercial; location, area, budget, timeline) | Yes | `client_property_requirements` |
| 6 | Owner listing: Available for Sale/Rent/Lease, location, Kitta No., land area, building details, expected price | Yes | `client_owner_listings` (new table) |
| 7 | Required services (6 checkboxes) | Yes — at least one required | `service_types` + `client_service_requests` |
| 8 | Document checklist (6 docs × Submitted / Pending) + other text | Yes | `document_types` + `client_documents` |
| 9 | Digital registration: Client ID, date, registered by, mobile app user ID, MIS status | Yes — Client ID auto-generated | `clients` extra columns + `client_code` |
| 10 | Declaration + Client / Registered By / Approved By signatures | Yes — client signature required; staff optional | signature image paths on `clients` |

Office-use names, designations, dates, and scanned staff signatures are on the last wizard step so the annex is not shortened.

---

## Why a new table was added

Most Annex F data already had tables from the original schema:

- `clients`, `addresses`
- `client_organizations`
- `client_property_requirements`
- `client_service_requests`, `service_types`
- `client_documents`, `document_types`

**Missing from the schema:** property-owner listing snapshot (section 6). That is not the full Annex A listing — it is a short register of “property available for / location / kitta / area / building / expected price”. Those fields were added as `client_owner_listings`.

**Also missing on `clients`:** `client_type_other`, scanned signature paths, and printed “Registered By / Approved By” name fields (the existing `registered_by` column is a staff FK, not free text).

Migration: `database/migrations/2026_08_16_201000_add_annex_f_client_registration_fields.php`

That migration also inserts the six service names and six document names if they are not already in the lookup tables (there was no seeder).

---

## Files created / updated

### New

- `database/migrations/2026_08_16_201000_add_annex_f_client_registration_fields.php`
- `app/Models/ClientOrganization.php`
- `app/Models/ClientPropertyRequirement.php`
- `app/Models/ClientOwnerListing.php`
- `app/Models/ServiceType.php`
- `app/Models/DocumentType.php`
- `app/Models/ClientServiceRequest.php`
- `app/Models/ClientDocument.php`
- `app/Http/Requests/StoreClientRegistrationRequest.php`
- `app/Http/Controllers/ClientRegistrationController.php`
- `resources/js/pages/ClientRegistration/ClientRegistrationForm.vue`
- `resources/views/pdf/client_registration.blade.php`

### Updated

- `app/Models/Client.php` — fillable fields and relations
- `routes/web.php` — GET/POST `/client-registration`, GET `/client-registration/{id}/pdf`

---

## Frontend design (same as previous forms)

Copied the existing wizard chrome:

- `min-h-screen bg-slate-50`, centered max-width card
- Chip with document code **AGJ-FRM-07**
- Circular emerald progress steps
- Shared `FormField.vue` and `SignatureUpload.vue`
- Fade-slide step transitions
- Global 422 error banner
- Success screen with Client ID + PDF download

**Six steps**

1. **Personal** — client type + personal information
2. **Contact** — mobile (10 digits starting with 9), alternate, email, addresses
3. **Services** — organization (if any) + required service checkboxes
4. **Property** — section 5 (buyer/investor/tenant) and section 6 (owner listing)
5. **Documents** — submitted / pending radio for each annex document
6. **Sign** — digital registration fields, staff signatures, declaration, client scanned signature

Conditional rules:

- Client type Other → specify text required
- Buyer / Investor / Tenant → requirement purpose required
- Property Owner → “available for” required
- Client scanned signature required (JPG/PNG/WEBP, max 2 MB)

Citizenship has **no minimum length** (can be 1 character), unique if provided.

---

## Save flow

```
GET /client-registration
        │
        ▼
Vue wizard (6 steps)
        │
        ▼
POST /client-registration  (multipart FormData + CSRF)
  StoreClientRegistrationRequest validates
  ClientRegistrationController@store (DB transaction):

    1. Address (permanent)
    2. Address (current)
    3. Client (code CLT-YYYYMMDD-0001) + signature files
    4. Organization + office address  (if org name filled)
    5. Property requirement           (if section 5 filled)
    6. Owner listing                  (if section 6 filled)
    7. Service requests               (lookup firstOrCreate by name)
    8. Document checklist rows        (lookup firstOrCreate by name)

        │
        ▼
JSON 201 { client_id, client_code }
        │
        ▼
Success → GET /client-registration/{id}/pdf
        → pdf/client_registration.blade.php (all 10 annex sections)
```

---

## Field mapping (form key → database)

| Form field | Table.column |
|---|---|
| `client_type`, `client_type_other` | `clients` |
| `full_name`, `father_mother_name`, `spouse_name` | `clients` |
| `citizenship_no`, `nationality`, `date_of_birth`, `gender`, `occupation` | `clients` |
| `mobile_no`, `alt_contact_no`, `email` | `clients` |
| `permanent_address`, `current_address` | `addresses` via FKs |
| organization fields | `client_organizations` |
| `req_*` | `client_property_requirements` |
| `available_for`, `property_location`, `kitta_no`, `land_area`, `building_details`, `expected_price` | `client_owner_listings` |
| `requested_services[]` | `client_service_requests` |
| `document_status[...]` | `client_documents.status` (`submitted` / `pending`) |
| `registration_date`, `mobile_app_user_id`, `mis_entry_status` | `clients` |
| `client_signature` / `client_signature_name` / `client_signature_date` | `clients.signature_path`, `clients.signature_name`, `clients.signature_date` |
| registered / approved by | `clients.registered_by_*` / `approved_by_*` |

---

## How to use

| Action | URL |
|---|---|
| Open form | `/client-registration` |
| Submit | `POST /client-registration` |
| PDF | `/client-registration/{client_id}/pdf` |

Client ID format: `CLT-20260816-0001`.
