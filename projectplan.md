# API GharJagga MIS — Project Plan

A living checklist for turning the existing Laravel/Filament/Inertia system into the
full corporate-website + ERP + client-portal platform described in the proposal and
process-flow brief. Check items off as we complete them together. Each phase links
back to the annex/process-flow item or org-chart role it serves, where relevant.

Status key: `[ ]` not started · `[~]` in progress · `[x]` done

---

## Phase 0 — RBAC Overhaul — **DONE**

- [x] Extend the permission catalog with granular action keys (`properties.approve`,
      `valuations.assign/conduct/review`, `inspections.schedule/conduct`,
      `complaints.assign/resolve`, `agreements.view/manage/review`, `system.audit_logs`)
- [x] Restructure the 7 generic roles into the 14 org-chart roles (migration +
      `RolesSeeder`), preserving existing user→role assignments
- [x] Wire the new granular permissions into `PropertyResource`, `ComplaintResource`,
      `ValuationRequestResource` (+ its Reports relation manager), `SiteInspectionResource`
- [x] Close two live gaps found along the way: ungated property approve/reject,
      ungated valuation-report relation manager
- [x] Add `AgreementResource` (staff-side view/review/track for Annex-B agreements —
      previously no admin screen existed at all)
- [x] Add `AuditLogResource` (the `audit_logs` table existed unused since Aug 14)

## Phase 1 — Verify & Land the RBAC Work

- [x] Verify every role's access, automatically: scripted a check across all 15
      admin accounts × 17 Filament resources (`canViewAny`/`canCreate`/`canEdit`),
      confirming each of the fine-grained splits behaves exactly as designed —
      e.g. Site Engineer can conduct inspections but not schedule them, Survey
      Coordinator is the reverse; Valuation Officer can open a valuation report
      but General Manager/MD get review-only access instead of conduct
- [x] Confirmed both new resources (`AgreementResource`, `AuditLogResource`)
      boot cleanly and their Livewire pages mount + render without errors
- [x] **Found & fixed a live regression**: `UserSeeder` still referenced the old
      pre-Phase-0 role names (`'Manager'`, `'Engineer'`, `'Finance'`,
      `'Customer Support'`), which no longer exist after the rename migration —
      a fresh reseed would have silently granted those demo accounts full
      admin access instead of the intended limited role (a `null` role_id on
      an admin account falls back to full access). Fixed the names, added
      demo logins for all 14 roles, and added a guard so a future typo throws
      instead of silently over-granting access.
- [ ] **Needs your input**: re-assign your *real* staff accounts to the new
      roles. Right now only the 7 legacy demo accounts + `admin@apigharjagga.com`
      have real role assignments (auto-migrated onto their renamed/merged
      role); the 7 brand-new roles (Managing Director, Marketing Manager,
      Survey Coordinator, Sales/Brokerage Rep, Document Officer, Receptionist,
      IT Support) only have placeholder demo logins so far. Tell me who's who
      (or assign them yourself via Roles & Permissions in `/admin`) and I'll
      wire it up.
- [x] Real staff assignment: since this is still the development phase, the
      seeded demo accounts stand in for real people for now — one login per
      role (see the 14 accounts added in `UserSeeder`). Reassign the emails
      to real staff whenever convenient; no code change needed to do that.
- [ ] Optional: a manual click-through in the browser as 1-2 roles, if you want
      eyes-on confirmation beyond the automated checks above.

## Phase 2 — Client-Facing Portal Roles

Differentiates the single generic "Client" experience into the 5 external user
types the brief describes.

**Architecture note**: `Client` (Annex-F, staff-entered) and `User` (web login)
were already two separate concepts in this codebase — `properties` tracks
`owner_client_id` and `user_id` independently, and a `Client` can exist with no
web login at all (e.g. a walk-in seller). Rather than force them together, we
added a new `client_type` enum directly on `users`, separate from
`clients.client_type` — it answers a narrower question: which portal dashboard
does *this login* see. Existing users were backfilled to `owner` (the only
portal experience that existed before now); new signups choose explicitly.

- [x] Added `users.client_type` (owner/buyer/investor/tenant/agent), a "I am
      a..." selector on the registration page, and one demo login per type
      (`buyer@apigharjagga.com`, `investor@…`, `tenant@…`, `agent@…`,
      `user@…` = owner) for testing
- [x] Scoped the User-panel dashboard/navigation by `client_type`: gated
      `MyPropertyResource` and `MyValuationRequestResource` to Owner only
      (matches the RBAC matrix's "List Property" / "Request Valuation" rows),
      and reworked the dashboard's welcome banner, stat cards, and quick
      actions so non-owners get an honest "marketplace browsing + more
      coming" view instead of a broken Owner-shaped dashboard with dead links
      to pages they're now blocked from
- [x] Owner: verified "My Properties" / "My Valuation Requests" still work
      exactly as before (no regression) — confirmed via automated render checks
- [ ] Owner: add "Accept/Reject Offer" once agreements reach that stage
- [x] Buyer/Owner: view own agreements (Annex-B) and payment receipts (Annex-I).
      Built `MyAgreementResource` + `MyPaymentResource` (read-only — agreements
      and receipts are issued at the staff counter, not self-service) in the
      User panel, gated to `client_type` owner/buyer. Since `Agreement` and
      `PaymentReceipt` hang off `Client` (not `User`), added
      `User::resolvedClient()`, which matches the logged-in account to its
      `Client` record via citizenship number (`KycVerification.citizenship_no`
      ↔ `Client.citizenship_no` — both already existed, no new schema needed).
      Verified end-to-end with a rolled-back transaction: a matching KYC +
      Client + Agreement + Payment surfaced correctly in both resources, and
      cleanly returns empty (not an error) when no match exists yet.
- [ ] Agent-on-behalf-of-owner: Power of Attorney upload + verification, scoped
      access to only the properties they're authorized for — new data model needed
      (no POA concept exists yet; this is *why* Agent doesn't get MyPropertyResource
      access yet even though they're logically owner-adjacent)
- [ ] Investor: investment-opportunity views, market insight/analytics — net new,
      no backing data model yet; scope this once we know what "analytics" should show
- [ ] Tenant: rental listing search, pay rent online, maintenance requests — net
      new; there's currently no lease/rental-agreement or maintenance-ticket model
      (rent payments would extend `PaymentReceipt`/`Agreement`, maintenance requests
      would likely reuse `Complaint` or need a small new model)

## Phase 3 — New Back-Office Modules

None of these exist today beyond what's noted. Each is independent — can be
built and shipped on its own.

- [ ] **Finance ledger**: Cash Book, general Ledger, Profit & Loss, Balance Sheet,
      Budget Planning, Invoices — today only `PaymentReceipt` (Annex-I) exists;
      this is proper double-entry-adjacent bookkeeping on top of it
- [ ] **HR module**: Employee management (beyond the current bare `Staff` directory),
      Attendance, Leave, Payroll, Performance Review, Employee Documents, Contracts
- [ ] **Engineering / Construction project management**: Project registration,
      Engineer assignment, Site visits, Milestones, BOQ, Contractor management,
      Material tracking, Inspection reports (distinct from Annex-D site inspections,
      which are pre-listing not construction-phase), Payment tracking

## Phase 4 — Corporate Website + CMS

The marketing site (Home, About, Services, Projects Portfolio, Team, Testimonials,
Career, News/Blog, Gallery, Contact) needs CMS-backed content instead of hard-coded
Vue pages, so non-technical staff can update it.

- [ ] Content models + Filament resources: Team members, Testimonials, Job
      openings/Career postings, Blog/News posts, Gallery albums
- [ ] Public Vue/Inertia pages consuming that content
- [ ] Contact/Online Inquiry form (confirm existing `PropertyInquiryController`
      covers this or needs a general-purpose contact form)
- [ ] Download Documents section (public document library)
- [ ] Google Maps integration on Contact/property pages
- [ ] SEO pass (meta tags, sitemap, structured data)
- [ ] Social media integration (share buttons / feed embeds)

## Phase 5 — Reporting & Analytics

- [ ] Dedicated Reports section: Financial, Property, Valuation, Customer,
      Employee, Project, Revenue reports (today only ad-hoc Filament widgets exist)
- [ ] Expand `StatsOverview`/dashboard widgets per-role so each role's dashboard
      reflects the "Dashboard Analytics" row of the permission matrix

## Deferred / Out of Scope for Now

- **Native mobile apps** (Customer/Buyer/Investor/Tenant) — treated as
  requirements for the existing responsive web portal instead, per earlier decision.
  Revisit only if the web portal proves insufficient.
- **System Administration features** implied by the permission matrix (System
  Settings, Backup/Restore, Manage Notifications) — no backing functionality
  exists yet; permission keys were deliberately *not* added for these in Phase 0
  to avoid dead checkboxes. Build the feature first, add the permission key with it.

---

### How we'll work through this

Tell me which phase/item to pick up and I'll scope it into concrete steps (data
model changes, migrations, resources/pages, and — for anything public-facing —
a browser check before calling it done). I'll keep this file updated as we
complete items, so it stays the source of truth across sessions.
