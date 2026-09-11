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
- [x] Agent-on-behalf-of-owner: Power of Attorney upload + verification, scoped
      access to only the properties they're authorized for. New
      `power_of_attorneys` table (agent_user_id, owner_client_id, document,
      status) — scoped to `owner_client_id` rather than the owner's own
      `user_id`, since an owner may have no web login at all (Client is the
      only ownership reference guaranteed to exist regardless of intake
      channel). `MyPowerOfAttorneyResource` (User panel, Agent only) lets an
      agent identify the owner by mobile/citizenship number and upload the
      signed document; admin `PowerOfAttorneyResource` (Document Officer /
      Legal Coordinator — matches their explicit org-chart "Power of
      Attorney verification" duty, MD/GM oversight) approves or rejects it.
      Once approved, `MyPropertyResource` grants that agent the same
      view/edit access to the owner's properties an owner would have,
      scoped in `getEloquentQuery()` — but not the ability to create a new
      listing on the owner's behalf, since the self-service creation wizard
      assumes the current user *is* the owner; that's a reasonable further
      extension, not built here.
      **Found and fixed a real bug while building this**: `User::resolvedClient()`
      (built in Phase 2 for Buyer/Owner agreements & payments) only checked
      citizenship-number matching, but the self-service property-listing
      flow (`CreateMyProperty`) links `User` → `Client` a different way —
      via `clients.mobile_app_user_id`, set directly when a user lists
      their first property. An owner who self-listed a property but whose
      KYC citizenship number didn't happen to match could have been invisible
      to their own `MyAgreementResource`/`MyPaymentResource`. Fixed to check
      the direct `mobile_app_user_id` link first, falling back to
      citizenship-number matching for clients registered other ways.
      Verified: RBAC gating for the admin resource; the full flow (agent
      blocked before approval, granted immediately after, sees exactly the
      right property) and the resubmit-after-rejection edge case (reuses
      the same row via the DB's unique constraint rather than erroring or
      duplicating) against real data in a rolled-back transaction.
- [x] Investor: curated investment-opportunity listing view. Built
      `MyInvestmentResource` (User panel, gated to `client_type === 'investor'`)
      over the existing `PropertyListing` model rather than a new data model —
      `purpose_of_listing = 'investment'` was already a valid value on the
      listing wizard, just never surfaced anywhere. Read-only (`canCreate()`
      false); a "View & Enquire" action links out to the existing public
      property detail page, reusing its inquiry form instead of building a new
      lead-capture mechanism. **Deferred**: "market insight/analytics" — no
      concrete scope was given for what that should show, and no backing data
      model exists to compute it from; revisit once there's a specific
      question to answer (e.g. rental yield, price-history trend).
- [x] Tenant: curated rental listing search. Built `MyRentalResource` (User
      panel, gated to `client_type === 'tenant'`), same pattern as Investor
      above — queries `PropertyListing` where `purpose_of_listing` is `rent`
      or `lease` (both already existed), shows `rental_amount`. **Deferred**:
      pay rent online and maintenance requests both need an active
      lease/tenancy record (who is renting *which specific unit*, for what
      term) that doesn't exist yet in the schema — this resource only covers
      the "browse what's available" half of the brief. Paying rent would
      extend `PaymentReceipt`, maintenance requests would likely reuse
      `Complaint` or need a small new model, but both are premature without
      the tenancy record they'd hang off of.
      Verified (both resources): `canViewAny()` checked against every
      `client_type` (owner/buyer/investor/tenant/agent) — each sees only its
      own resource; list pages render cleanly with zero data (empty state);
      view pages render correctly against a real investment and a real rental
      listing created in a rolled-back transaction, with price/rent
      formatting confirmed correct.

## Phase 3 — New Back-Office Modules — **DONE**

All three modules (Finance ledger, HR, Engineering/Construction PM) built
and each independently verified against real data in rolled-back
transactions. Finance and HR/Payroll and Projects/Payment Tracking all
share the same double-entry ledger core rather than three disconnected
money-tracking systems.

- [x] **Finance ledger** — DONE. Built a real double-entry core rather than
      four disconnected screens: `finance_accounts` (chart of accounts),
      `finance_transactions` + `finance_transaction_lines` (every posting is
      balanced debit=credit, enforced in `FinanceTransaction::postBalanced()`,
      never bypassable from the UI). Cash Book, Ledger, Profit & Loss, and
      Balance Sheet are all just different queries over that same data,
      living on one "Financial Reports" page with a shared date-range filter.
      `PaymentReceipt` (Annex-I) now auto-posts to the ledger the moment it's
      created (`PaymentReceiptObserver`) — existing income flows in with zero
      manual re-entry. Added `PaymentVoucherResource` (outgoing payments —
      approving one posts Debit expense / Credit Cash), `InvoiceResource`
      (line items, auto-totaled, PDF export), and `BudgetResource`
      (allocated vs. actual vs. variance per account per period). New
      permissions (`finance_accounts`, `budgets`, `invoices`, `vouchers`,
      `finance_reports.view`) wired so Finance Manager gets full control,
      MD/GM/Admin get view-only oversight, everyone else sees nothing.
      Verified: the balance/movement math, the unbalanced-transaction guard,
      receipt auto-posting, and voucher approve-and-post all confirmed
      correct against real data in rolled-back transactions; every resource
      and the reports page mount and render cleanly for every role.
      **Deferred**: Payroll (the proposal lists it under both Finance and HR;
      it's fundamentally an employee-comp feature, so it'll land with the HR
      module instead of being split across both).
- [x] **HR module** — DONE. Extended `Staff` (the existing internal employee
      directory) with a real HR profile (employee code, department,
      employment type, date of joining, basic salary, DOB/gender, address,
      emergency contact) instead of creating a parallel "Employee" model.
      Added `AttendanceResource` (daily status per staff, plus a "Mark
      Today's Attendance" bulk action that presence-marks every active
      staff member not yet recorded), `LeaveRequestResource` (apply/approve/
      reject, mirroring the Complaint assign/resolve pattern), and
      `PerformanceReviewResource` (1-5 rating, strengths/areas for
      improvement). `StaffResource` gained Documents and Contracts relation
      manager tabs (per-employee file uploads and contract records),
      matching the pattern already used for Agreement parties/witnesses.
      **Payroll** (`PayrollRunResource`) is where HR connects back to the
      Finance ledger built in the previous step: "Generate Payslips" pulls
      each active staff member's `basic_salary` into a payslip for the
      period (adjustable per-employee via a Payslips relation manager for
      allowances/deductions), and "Finalize & Post" posts one Salaries &
      Wages expense transaction for the period's total net pay — reusing
      `FinanceTransaction::postBalanced()` rather than inventing a second
      posting mechanism. No dedicated "HR" role exists in the org chart, so
      permissions went to the roles that already have staff-supervision or
      financial authority: Finance Manager owns Payroll end-to-end,
      General Manager manages Attendance/Leave/Performance day-to-day
      (matches its "staff supervision" org-chart duty) and approves leave,
      Managing Director and Admin get view-only oversight across all of it.
      Verified: RBAC gating confirmed correct for every role, attendance
      bulk-marking, and the full generate→finalize→ledger-post payroll flow
      (correctly skipping staff with no salary set) all checked against
      real data in a rolled-back transaction — cash decreased by exactly
      the posted payroll total.
- [x] **Engineering / Construction project management** — DONE, and Phase 3
      is now complete. `ProjectResource` is the hub (project registration,
      client + engineer assignment) with seven relation-manager tabs:
      Milestones, Site Visits, Progress Log, BOQ, Contractors, Material
      Records, and Inspection Reports (the last deliberately a new
      `project_inspection_reports` table — distinct from the existing
      Annex-D `site_inspections`, which are pre-listing property checks,
      not construction-phase quality inspections). Added a standalone
      `ContractorResource` directory since the same contractor works
      across multiple projects. "Payment Tracking" reuses the existing
      `PaymentVoucher` → Finance ledger flow rather than a second, parallel
      money-tracking system — a voucher just optionally links to the
      project it was spent on (`payment_vouchers.project_id`).
      Permission split mirrors the inspections.schedule/conduct pattern
      from Phase 0: `projects.manage` (Technical Manager, GM, Admin) covers
      registering projects, milestones, BOQ, and contractor assignment;
      `projects.log` (also Site Engineer) covers field data entry — site
      visits, progress updates, material records, inspection reports —
      without needing full management rights. MD gets view-only oversight;
      Finance Manager has no project access (their involvement is via the
      voucher itself). Verified: RBAC gating for all 6 relevant roles,
      every relation-manager tab, and a full data flow (milestones, site
      visit, materials, contractor assignment, BOQ, plus a project-linked
      voucher posting through the ledger) against real data in a
      rolled-back transaction — cash decreased by exactly the voucher amount.

## Phase 4 — Corporate Website + CMS — **DONE**

The marketing site (About, Team, Testimonials, Careers, News/Blog, Gallery,
Contact, Document Downloads) is now CMS-backed instead of needing a developer
for every content change.

- [x] Content models + Filament resources: `TeamMemberResource`,
      `TestimonialResource`, `JobOpeningResource` (+ an Applicants relation
      manager with a "Resume" quick-open action), `BlogPostResource` (rich
      text editor, slug auto-generated from title, publish toggle),
      `GalleryAlbumResource` (+ an Images relation manager),
      `SiteDocumentResource`, and `ContactMessageResource` (mirrors the
      existing Inquiry mark-contacted/close pattern). Every uploaded-file
      model got a `/storage/...` URL accessor matching the convention
      already established by `PropertyPhoto`, so the frontend never
      constructs storage paths itself.
- [x] Public Vue/Inertia pages: `About.vue` (team + testimonials),
      `Careers.vue` (listing + an apply modal with resume upload),
      `Blog/Index.vue` + `Blog/Show.vue` (with related posts + social share
      links), `Gallery.vue` (album grid + lightbox), `Documents.vue`
      (download list). `AppHeader` gained a `solid` prop so pages without a
      hero image don't inherit the landing page's transparent-until-scroll
      header (which would otherwise render white nav text on a white page).
      `AppHeader`/`AppFooter` nav updated to link to all of these instead of
      the placeholder `#` hrefs they had before.
- [x] Contact form: built as a genuinely separate `ContactMessage` model/
      flow rather than overloading `PropertyInquiry`, which always requires
      a specific `property_id` and serves marketplace buyer/tenant leads —
      a general "have a question" message isn't that.
- [x] Download Documents section — `SiteDocumentResource` (admin) +
      `Documents.vue` (public list, `is_public` scoped).
- [x] Google Maps — embedded on the Contact page via a live search-query
      iframe (`google.com/maps?q=...`) rather than a fabricated fixed
      coordinate, since the exact office address wasn't available; the page
      carries a visible note to update it once you have the real address.
- [x] SEO pass — per-page `<Head>` title + meta description on every new
      page (matching the pattern already used elsewhere in the app), plus a
      new `/sitemap.xml` route (`SitemapController`) covering every static
      page, published blog posts, and live property listings.
- [x] Social media integration — share links (Facebook/X/LinkedIn/WhatsApp)
      on blog posts, computed from the actual page URL at view time.
      Footer's social icons remain `#` placeholders — no real social media
      URLs exist yet to link them to; wire them up once you have them.
      No feed-embed widget was built (not requested beyond "integration").
- [x] New permissions (`content`, `careers`, `site_documents`,
      `contact_messages`) — Marketing Manager owns content/careers/documents
      end to end (matches its org-chart "manage social media integrations,
      approve digital content" duty), Customer Support Officer handles
      Contact Messages (matches its existing inquiry-handling role), GM gets
      view-only oversight, everyone else sees nothing.

Verified: RBAC gating for every role across all 7 resources; every admin
page and all 7 new public routes (`/about`, `/careers`, `/blog`,
`/gallery`, `/documents`, `/contact`, `/sitemap.xml`) return 200 and render
through the real Inertia+Vue stack via a live dev server — not just a
component-level check; the homepage was re-verified working after changing
the shared header/footer; TypeScript, ESLint, and Prettier all pass clean
on every new/modified frontend file; and the full data flow (team/
testimonial active-only filtering, job application linking, blog publish
visibility, gallery image URLs, contact message creation) confirmed
correct against real data in a rolled-back transaction.

## Phase 5 — Reporting & Analytics — **DONE**

- [x] Dedicated Reports section — six pages under a new "Reports" nav group:
      `PropertyReports` (status/type/approval breakdowns, 6-month listing
      trend), `ValuationReports` (request status, issued-report mix, average
      valuated amount per type, top valuators), `ClientReports` (client-type
      mix, KYC status, 6-month registration trend), `EmployeeReports`
      (department/employment-type mix, this month's attendance, leave
      status), `ProjectReports` (status mix, milestone progress, and a
      **BOQ-budgeted vs. actual-spend table per project** — the actual side
      pulled from `payment_vouchers` linked to that project, so it reflects
      real posted spend rather than a separate estimate), and
      `RevenueReports` (month/year totals, by-category breakdown, 6-month
      trend — built on the same `FinanceAccount::movementBetween()` the
      Phase 3 Financial Reports page already uses, so the two never
      disagree about what "revenue" means). "Financial Reports" itself
      already exists as Phase 3's Cash Book/Ledger/P&L/Balance Sheet page —
      not rebuilt here.
      Deliberately reused each domain's **existing** `.view` permission
      (`properties.view`, `valuations.view`, `clients.view`, `staff.view`,
      `projects.view`, `finance_reports.view`) to gate the matching report
      page instead of inventing a parallel `reports.*` permission set —
      one less thing to keep in sync, and it naturally reproduces the
      matrix's Operational/Client/Financial report-access split for free
      (verified below).
- [x] "Dashboard Analytics" (the permission matrix row, not the widget
      class) — satisfied by giving each role the reports pages relevant to
      what they already have `.view` access to, rather than cramming more
      stat tiles onto the generic `StatsOverview` widget. A role sees
      exactly the reports its existing permissions justify, with no
      separate toggle to maintain.

Verified: `canAccess()` gating checked for 6 roles across all 6 pages —
Finance Manager gets Client/Employee/Revenue but not Property/Valuation/
Project, Technical Manager gets everything except Revenue, Site Engineer
gets Property/Valuation/Project but not Client/Employee/Revenue, exactly
matching each role's existing permissions with no new gaps or overreach.
Every page renders cleanly for Admin. The two riskiest queries — a raw
SQL `JOIN` (top valuators by report count) and an aliased `withSum` across
two relations (BOQ budgeted vs. actual project spend) — were exercised
against real inserted rows in a rolled-back transaction and returned
exactly the expected numbers, not just their empty-state branch (most
tables are still near-empty in dev, so the plain render check alone
wouldn't have caught a broken JOIN or aggregate).

## Phase 6 — Annex-F KYC Overhaul — **DONE**

Turned the single-stage, narrow KYC form into a full bilingual Annex-F
identity gate with a real two-person review workflow, a document
checklist, and a printable certificate — and made it the one thing every
new portal login must clear before anything else in the User panel
becomes visible.

- [x] **Two write paths consolidated into one.** A legacy inline KYC form
      lived on the Inertia `Dashboard.vue` "kyc" tab (posting to
      `KycController::store()`), completely independent of the newer,
      more complete Filament Wizard (`KycVerificationPage`) — both wrote to
      the same `kyc_verifications` row with different, drifting field
      coverage. Removed the duplicate form and its dead `store()`
      method/route/`KycStoreRequest` (confirmed unused elsewhere first);
      the dashboard's KYC tab is now a status summary + a link to the one
      real form. `DashboardController` no longer ships the now-unused
      per-field KYC payload over the wire.
- [x] **Full Annex-F field parity.** Added the fields the Client
      Registration migration already has but `kyc_verifications` didn't:
      `grandfather_name`, `alt_contact_no`, `telephone_no`, and an
      applicant `signature_path`/`signature_date` — via
      `2026_09_16_090000_add_annex_f_fields_and_review_stages_to_kyc_verifications_table.php`.
- [x] **Two-stage review workflow**, per your direction to give the
      approval stage its own dedicated role rather than reusing General
      Manager: `status` is now `pending → verified → approved` (or
      `rejected` from either stage), with `verified_by_staff_id`/
      `verified_at` and `approved_by_staff_id`/`approved_at` columns.
      New granular permissions `kyc.verify` / `kyc.approve` (alongside the
      existing `kyc.view`); **Document Officer / Legal Coordinator**
      verifies (matches its existing PoA-verification duty), a brand new
      **KYC Approver** role gives final sign-off, General Manager and
      Customer Support Officer keep `kyc.view` only (oversight, no
      action). `KycVerificationResource` gained dedicated Verify/Approve/
      Reject table actions (staff picked via a dropdown in the action's
      own modal, matching the `LeaveRequestResource`-style pattern already
      used elsewhere since Staff and User logins aren't linked), each
      visible only to the permission and status stage it applies to.
- [x] **Document checklist with uploads.** New `kyc_verification_documents`
      table (mirrors the existing `client_documents`/`property_documents`
      checklist pattern against the shared `document_types` lookup).
      Three required documents — Citizenship Copy, Passport-Size Photo,
      Proof of Current Address (a new document type; the first two reuse
      the Wizard's existing ID/photo uploads rather than asking for the
      same file twice) — are synced into this table on every submission,
      so the admin resource and PDF can list checklist status generically
      instead of reading fixed columns.
      **Deferred**: only these three documents are enforced today; adding
      a fourth later means adding one more `KycVerification::requiredDocumentTypeNames()`
      entry and one more upload field, not a schema change.
- [x] **Bilingual (English + Nepali) labels** on every field in the
      Wizard and the admin form, matching the "English (नेपाली)" inline
      convention already used elsewhere in this app (`LocationSelects`'
      Province/District/Municipality/Ward dropdowns already did this by
      default — the Wizard's custom label overrides had been silently
      dropping that translation; fixed by no longer overriding them).
      The status page also gained a visible 3-step progress tracker
      (Submitted → Verified → Approved) instead of a flat status badge.
- [x] **Printable PDF certificate**, rebuilt on the existing Annex-style
      Dompdf convention (bilingual `.np`/Kalimati letterhead, bordered
      data tables) already used for the Agreement and Client Registration
      PDFs: added the new personal fields, a document-checklist table,
      and a three-way signature block (Applicant / Verified By / Approved
      By) with real staff names, designations, and dates instead of a
      single generic "Verified By (Admin)" line. Added a self-service
      route (`/kyc/pdf`, scoped to the logged-in user's own record — never
      accepts an arbitrary id) so an approved applicant can download their
      own certificate; the admin resource gained a PDF action too.
- [x] **Portal-wide KYC gate.** Added `User::hasApprovedKyc()` and applied
      it to `canViewAny()`/`canCreate()` on every self-service User-panel
      resource — MyProperty, MyValuationRequest, MyAgreement, MyPayment,
      MyPowerOfAttorney, MyInvestment, MyRental — so a login with no
      approved KYC sees only the KYC page; Filament's own authorization
      returns a 403 for anything else, matching "all other fields
      disabled until KYC is complete."

Verified: a full submit → verify → approve cycle exercised end-to-end
against a throwaway user in a rolled-back transaction — gating correctly
blocks before submission and during the `verified` stage, unlocks only
after approval; the Document Officer / KYC Approver permission split
confirmed via `hasPermission()` for every relevant role (including that
Document Officer cannot approve and KYC Approver cannot verify); every
admin and User-panel page renders cleanly; the PDF generates successfully
with all new fields, the checklist table, and the three-way signature
block populated from real data. `WorkflowDemoSeeder` updated to match —
Investor's demo KYC is deliberately left `pending` so the verify/approve
flow has a live example to click through; Tenant and Agent are
pre-approved so their own portal features stay testable.

**Follow-up — full Annex-F parity + a cleaner, single-page result.** After
checking the KYC form against the actual 10-section Annex F structure
(the reference implementation already exists in
`resources/views/pdf/client_registration.blade.php`, the staff-side
public intake form), three sections were still missing from the
self-service KYC flow:

- [x] **§4 Organization Details (if applicable)** — new
      `kyc_organization_details` table (1:1 with `kyc_verifications`),
      only kept while at least one field is filled in, since an
      individual applicant has nothing to put here.
- [x] **§5 Property Requirement Details** — new `kyc_property_requirements`
      table, same "only keep if filled in" rule; purpose/property type/
      location/area/budget/timeline for Buyer, Investor, or Tenant
      applicants.
- [x] **§7 Required Service Selection** — new `kyc_service_requests`
      table (many-to-many against the existing `service_types` lookup),
      a simple checkbox list synced on every submission.
- **§6 Property Owner Details is deliberately not included** — per
      instruction, that belongs to a later "list my property" step, not
      identity KYC itself.

Both the Wizard (two new steps) and the PDF (renumbered to match Annex
F's own section numbers exactly, including the intentional gap at §6)
were updated together so the two never disagree about what Annex F
contains. The admin resource also gained read-only summaries of all
three sections so reviewers see the same information.

**Also simplified the page itself**, per direction to make it cleaner
and to show everything in one place once submitted: replaced the busy,
multi-colored status banners with one plain status bar (a colored dot +
label + stage counter, no gradients or animation), and — the more
substantial change — a locked/submitted record no longer re-renders the
disabled multi-step Wizard at all. Instead it shows one continuous,
printable summary page (every section, in reading order, with a browser
Print button alongside the existing PDF download) built directly from
the same data the PDF uses, so "preview" and "print" are two views of
the same underlying record rather than two things that could drift apart.

Verified: organization/property-requirement/service-selection data
created directly against a throwaway KYC record surfaces correctly in
all three places — the single-page summary view, the admin resource, and
the PDF (checked by asserting the actual submitted values, e.g. an
organization name and a selected service name, appear in each rendered
output) — and the existing demo logins (mixed approved/pending) all
still render the page correctly after the change.

**Second follow-up — §1 Client Type visibility, multi-select §5, and a
verifier-owned §9.** Three more corrections after review:

- [x] **§1 Client Type wasn't visible anywhere in the KYC form itself** —
      it only showed up in the PDF. Added a read-only pill display (the
      applicant's `client_type`, set at registration) at the top of the
      Wizard's Personal Details step and as its own numbered section on
      the single-page summary and PDF.
- [x] **§5 Purpose and Property Type needed to allow multiple selections**
      (e.g. a buyer open to either purchase *or* rent, interested in
      either a house *or* an apartment) — both were single-value enum
      columns. Migrated `kyc_property_requirements.purpose` and
      `.property_type` to JSON arrays, changed the Wizard fields from a
      `Select` to a `CheckboxList`, and updated every place that reads
      them (summary page, admin resource, PDF) to render the selected
      values as a set of pills rather than a single value.
- [x] **§9 Digital Registration Details is filled in by the verifying
      staff member, not the applicant** — added `digital_client_id`
      (auto-generated the moment a record is verified,
      `KycVerification::generateDigitalClientId()`) and
      `mobile_app_user_id` (typed in by the verifier, defaulting to the
      applicant's own user id) to `kyc_verifications`. The admin
      resource's **Verify** action now collects the Mobile App User ID
      alongside the verifying staff member, and Registration Date /
      Registered By are simply `verified_at` / `verifiedBy` — the same
      staff action that verifies also completes §9 in one step, matching
      the fact that the Annex F reference lists all four §9 items as
      staff-entered rather than applicant-entered.
- [x] **Visual pass on both the summary page and the PDF**, matching a
      supplied "official document" reference more closely: a masthead
      with org name/doc reference, a submission-details strip, numbered
      section badges, and a rotating registration/approval seal —
      "APPROVED" (green) once a record clears final approval, shown on
      both the in-app summary and the downloadable PDF. Purpose, Property
      Type, and Client Type all render as pill/checklist widgets (filled
      pill = selected) instead of plain text, consistent with how the
      Document Checklist and Service Selection sections already looked.

Verified: multi-select purpose/property type round-trips correctly
end-to-end (created with two values each, confirmed both appear,
comma-joined, in the admin resource, and both render as filled pills on
the summary page); the Verify action's new Mobile App User ID field
persists and surfaces correctly in the summary, admin view, and PDF's §9
section; the approval seal renders on both the summary page and the PDF
only once a record reaches `approved`; and all pre-existing demo KYC
records (approved directly by the seeder, never through the Verify
action, so `digital_client_id` is null) still render cleanly everywhere
rather than erroring on the missing value.

**Bug fix — Print showed a blank page in dark mode.** The summary page's
light-on-dark theme colors survive into the print stylesheet (the browser
drops the dark background when printing, but the text stays whatever
color the current theme set it to), so viewing the page in dark mode and
printing produced near-white text on a white page — readable on screen,
invisible on paper. Fixed by forcing the printable region to plain
black-on-white regardless of the active theme, then re-asserting the few
colors that carry meaning (selected pills, status badges, the approval
seal) on top of that base.

**Brought the PDF and the in-app summary/print page into exact parity.**
The two had drifted apart after several rounds of edits — different
titles, the PDF's stamp only appearing when `approved` versus the
summary's seal appearing from `pending` onward, Client Type/Purpose/
Property Type/Services shown as plain text in the PDF but as pills in
the summary, and the PDF missing the submission-details strip. Rebuilt
`resources/views/pdf/kyc_verification.blade.php` section-for-section
against the summary page so both now show: the same title, the same
submission strip (Client ID / Submitted / Verified / Approved), the same
stamp/seal logic (SUBMITTED/VERIFIED/APPROVED, shown from `pending`
onward, matching color per stage), and the same pill-style rendering for
Client Type, Purpose, Property Type, and Required Services (every
option listed, selected ones highlighted) instead of comma-separated
text. Also added the Photograph/Identity Document thumbnails and the
Applicant's signature image to the in-app summary page, which the PDF
already had but the summary didn't.

Verified: rendered both the summary page and the PDF from the exact same
KYC record (organization details, multi-select purpose/property type,
one selected service, verifier and approver names, digital client ID)
and confirmed every one of those values appears in both outputs, and
that both list the Annex F sections in the identical order
(1, 2, 3, 4, 5, 7, 8, 9, 10 — §6 still deliberately absent).

## Deferred / Out of Scope for Now

- **Native mobile apps** (Customer/Buyer/Investor/Tenant) — treated as
  requirements for the existing responsive web portal instead, per earlier decision.
  Revisit only if the web portal proves insufficient.
- **System Administration features** implied by the permission matrix (System
  Settings, Backup/Restore, Manage Notifications) — no backing functionality
  exists yet; permission keys were deliberately *not* added for these in Phase 0
  to avoid dead checkboxes. Build the feature first, add the permission key with it.

## Test Data — `WorkflowDemoSeeder`

A single seeder (`database/seeders/WorkflowDemoSeeder.php`, run manually via
`php artisan db:seed --class=WorkflowDemoSeeder` — not wired into
`DatabaseSeeder`'s default list, since it's a large one-time demo dataset
rather than a required baseline) populates realistic, cross-linked rows
across every module so the 19 seeded logins (UserSeeder) each have real
data to click through instead of empty lists: 14 Staff (one per org-chart
role), 6 Clients (including the owner/buyer demo logins, linked the same
two ways `User::resolvedClient()` actually resolves them — direct
`mobile_app_user_id` link and citizenship-number KYC match), properties
across every purpose (sale/rent/lease/investment) and approval state,
site inspections, property verifications, valuation requests/reports,
agreements (sale-purchase and listing-brokerage) with parties/witnesses,
payment receipts (auto-posting to the Finance ledger via the existing
observer), complaints, service orders/certificates, a property handover
certificate, Power of Attorney records in all three states, invoices,
budgets, payment vouchers (some left in `draft` so you can test the
approve action yourself, others already posted for report history),
attendance/leave/payroll/performance/documents/contracts for HR, two
construction projects with the full set of milestones/site
visits/BOQ/contractors/materials/inspections, and CMS content (team,
testimonials, careers, blog, gallery, documents, contact messages).
Guarded by a single marker check (`CLI-DEMO-BUYER` client) so it only
seeds once — re-running it is a no-op rather than a duplicate dataset.
**Found and fixed a real bug while building this**: `User::resolvedClient()`
picks the *first* client matching `mobile_app_user_id`, so seeding a brand
new client with that same link for the owner-demo login (which already had
one from an earlier self-listed-property flow) silently created data that
the login could never actually see. Fixed by having the seeder look up and
reuse an existing linked client instead of creating a competing one.
Verified: every admin resource list page and every user-portal resource
(scoped correctly per `client_type`) renders cleanly against the seeded
data, and the owner-demo login's agreements/payments/properties all
resolve through the real `resolvedClient()`/`user_id` link paths rather
than a coincidental match.

---

### How we'll work through this

Tell me which phase/item to pick up and I'll scope it into concrete steps (data
model changes, migrations, resources/pages, and — for anything public-facing —
a browser check before calling it done). I'll keep this file updated as we
complete items, so it stays the source of truth across sessions.
