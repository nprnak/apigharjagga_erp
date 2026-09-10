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
- [ ] Investor: investment-opportunity views, market insight/analytics — net new,
      no backing data model yet; scope this once we know what "analytics" should show
- [ ] Tenant: rental listing search, pay rent online, maintenance requests — net
      new; there's currently no lease/rental-agreement or maintenance-ticket model
      (rent payments would extend `PaymentReceipt`/`Agreement`, maintenance requests
      would likely reuse `Complaint` or need a small new model)

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
