# Customer Complaint Form (Annex G) — How It Was Built

This file records the work done to implement **ANNEX–G / AGJ-FRM-08 Customer Complaint Form** (`ग्राहक गुनासो / उजुरी फारम`) in API Ghar Jagga MIS.

Source PDF:

`c:\Users\Acer\Downloads\documents-20260813T084204Z-1-001\documents\8. ANNEX-G-Customer Complaint Form.pdf`

---

## Approach

Same method as Annex A, B, and F:

1. Read every section and field from the official PDF.
2. Map fields onto existing database tables where they already existed.
3. Add only the columns the annex needs that were missing.
4. Build a Vue multi-step wizard using the **same visual system** (slate page, emerald step tracker, `FormField`, `SignatureUpload`, success card, PDF download).
5. Validate on both frontend and Laravel, persist in one transaction, and print an official PDF.

The public URL is `/complaint`.

---

## Annex G sections vs implementation

| # | Annex section | In the form? | Stored in |
|---|---|---|---|
| 1 | Registration: Complaint ID, date, time, received through (6 channels + other), received by | Yes — ID auto-generated | `complaints` |
| 2 | Customer: Full name, Client ID, mobile, email, address, type (Owner/Buyer/Investor/Tenant/Other) | Yes | `clients` + `addresses` via `complaints.client_id` |
| 3 | Property: Property ID, location, Kitta No., service taken, date of service | Yes | `complaints.property_id` (if known) + snapshot `property_location`, `kitta_no`, `service_reference`, `service_date` |
| 4 | Category (9 options including Other) | Yes — one required | `complaints.category`, `category_other` |
| 5 | Complaint description | Yes — min 20 characters | `complaints.description` |
| 6 | Evidence: Photo, Screenshot, Agreement Copy, Payment Receipt, Other | Yes — checkboxes + optional files | `complaint_evidence` |
| 7 | Priority: Low / Medium / High / Urgent | Yes | `complaints.priority` |
| 8 | Investigation: department, officer, dates, findings, corrective action, resolution date | Yes | `complaints` |
| 9 | Status: Registered / Under Investigation / Resolved / Closed / Pending Customer Response | Yes | `complaints.status` |
| 10 | Feedback: satisfaction + remarks | Yes | `complaints.satisfaction_level`, `customer_remarks` |
| 11 | Declaration + Customer / Received By / Reviewed & Approved signatures | Yes — customer signature required | signature paths on `complaints` |

The annex workflow (submission → registration → assignment → investigation → action → feedback → closure) is printed on the PDF footer.

---

## What already existed vs what was added

Existing tables from the original schema already matched most of Annex G:

- `complaints` — date, time, channel, category, description, priority, investigation fields, status, satisfaction
- `complaint_evidence` — photo / screenshot / agreement / receipt / other + `file_ref`

**Missing from the schema:** received-through Other text, category Other text, property location/kitta snapshots (so a walk-in complaint does not require an existing Property ID), free-text assigned officer, and the three signature blocks.

Migration: `database/migrations/2026_08_16_210000_add_annex_g_complaint_fields.php`

---

## Files created / updated

### New

- `database/migrations/2026_08_16_210000_add_annex_g_complaint_fields.php`
- `app/Models/Complaint.php`
- `app/Models/ComplaintEvidence.php`
- `app/Http/Requests/StoreComplaintRequest.php`
- `app/Http/Controllers/ComplaintController.php`
- `resources/js/pages/Complaint/ComplaintForm.vue`
- `resources/views/pdf/complaint.blade.php`

### Updated

- `app/Models/Client.php` — `complaints()` relation
- `routes/web.php` — GET/POST `/complaint`, GET `/complaint/{id}/pdf`

---

## Frontend design (same as previous forms)

- `min-h-screen bg-slate-50`, centered max-width card
- Chip with document code **AGJ-FRM-08**
- Circular emerald progress steps
- Shared `FormField.vue` and `SignatureUpload.vue`
- Fade-slide step transitions
- Global 422 error banner
- Success screen with Complaint ID + PDF download

**Six steps**

1. **Register** — date, time, received through, received-by staff
2. **Customer** — type, name, optional Client ID, mobile, email, address
3. **Property** — property ID/location/kitta/service + category
4. **Details** — description, evidence checklist + files, priority
5. **Action** — investigation fields and status
6. **Sign** — satisfaction, remarks, declaration, signatures

---

## Validation (frontend + Laravel)

| Field | Rule |
|---|---|
| Complaint date | Required, not in the future |
| Complaint time | Required, `HH:MM` |
| Received through | Required from the 6 annex channels |
| Received through Other | Required if channel is Other |
| Full name | Required, Unicode letters / space / hyphen / apostrophe / period |
| Client ID | Optional; must exist in `clients.client_code` if entered |
| Mobile | Required, exactly 10 digits starting with 9 |
| Email | Optional RFC email |
| Address | Required, min 8 characters |
| Customer type | Required; Other must be specified |
| Property ID | Optional; must exist in `properties.property_code` if entered |
| Kitta No. | Letters, digits, hyphen, slash |
| Category | Required; Other must be specified |
| Description | Required, 20–4000 characters |
| Evidence files | Optional JPG/PNG/WEBP/PDF, max 5 MB |
| Other evidence note | Required if Other is ticked |
| Priority | Required |
| Status | Required |
| Findings, corrective action, resolution date, satisfaction | Required if status is Resolved or Closed |
| Resolution date | Cannot be before complaint date |
| Declaration | Must be accepted |
| Customer signature | Required scanned JPG/PNG/WEBP, max 2 MB |

If Client ID is blank, a new client record is created from the customer fields so `complaints.client_id` is never empty.

---

## Save flow

```
GET /complaint
        │
        ▼
Vue wizard (6 steps)
        │
        ▼
POST /complaint  (multipart FormData + CSRF)
  StoreComplaintRequest validates
  ComplaintController@store (DB transaction):

    1. Resolve existing client by Client ID, or create client + address
    2. Resolve property_id from Property ID (optional)
    3. Store signature images
    4. Create complaint (code CMP-YYYYMMDD-0001)
    5. Create evidence rows (checklist and/or uploaded files)

        │
        ▼
JSON 201 { complaint_id, complaint_code }
        │
        ▼
Success → GET /complaint/{id}/pdf
        → pdf/complaint.blade.php (all 11 annex sections)
```

---

## How to use

| Action | URL |
|---|---|
| Open form | `/complaint` |
| Submit | `POST /complaint` |
| PDF | `/complaint/{complaint_id}/pdf` |

Complaint ID format: `CMP-20260816-0001`.
