# Major System Changes Plan

## Overview
This document outlines the planned major changes to the Kalawag Barangay System. The goal is to:
- Move to public-facing request/incident submissions (no resident accounts)
- Simplify clearance workflow (purok leader as sole approver)
- Improve staff login (numeric username, role-labeled URLs)
- Add resident masterlist management by purok leader and barangay officials
- Add analytics graphs and PDF preview/editing

---

## 1) Authentication & Access Changes

### 1.1 Staff Login
- **Reuse existing `username` field** to store numeric staff ID (unique across all staff).
- Staff login pages:
  - `/superadmin` → login page with “Chairman” label
  - `/admin` → login page with “Barangay Official / Secretary” label
  - `/purok` → login page with “Purok Leader” label
- After login, redirect to appropriate dashboard based on role.
- No change to resident login (residents no longer log in).

### 1.2 Public Homepage
- Remove “Login” and “Register” buttons from public homepage.
- Public page will serve as the main entry point for residents to:
  - View announcements
  - Request purok clearance
  - Report incidents

---

## 2) Public Forms (No Authentication Required)

### 2.1 Public Purok Clearance Request
Fields:
- Full name
- Purok
- Contact number
- Optional email
- ID upload

Behavior:
- Anyone can submit.
- If the person is not in the resident masterlist, purok leader can choose to add them during validation.

### 2.2 Public Incident Report
Fields:
- Full name
- Contact number (required)
- Optional email
- Incident details
- Photo upload(s)

---

## 3) Clearance Workflow Simplification

### 3.1 New Workflow
1. Public submits clearance request.
2. Purok leader reviews and:
   - Approves (final approval)
   - Decline
   - Or, if not in masterlist, choose to add the resident first
3. On approval:
   - Generate printable clearance PDF
   - Optionally attach PDF to email (if email provided)
4. **Remove barangay final confirmation** for clearance requests.

### 3.2 Status Values
Keep existing DB status values; only UI wording changes:
- Display “resolved” as “closed/completed” (UI-only).

---

## 4) Resident Masterlist Management

### 4.1 Purok Leader Dashboard
- Add, edit, delete residents **within their purok only**.
- Used to validate requests and maintain their purok’s resident list.

### 4.2 Barangay Official/Secretary Dashboard
- Add, edit, delete residents **across all puroks**.
- Include a “Yes/No” confirmation dialog for destructive actions.

---

## 5) PDF Clearance Template & Preview

### 5.1 Template-Based Preview
- Use a clearance template (you will provide the template).
- System fills the template with input data:
  - Name, gender, age, address, purpose, etc.
- Preview shows the filled document.
- Staff can edit a few fields before finalizing/printing.

### 5.2 Branding
- Include barangay logo and proper header.
- Include the purok name on printed documents.

---

## 6) Analytics Dashboard (Admin/Official/Secretary)

### 6.1 Graphs
- Bar graphs for:
  - Purok clearance requests
  - Incident reports
- Time filters: monthly, quarterly, annual
- Status filters:
  - Clearances: approved, pending, declined
  - Incidents: in-progress, invalid, completed/closed

---

## 7) Implementation Order (to avoid breaking the system)

### Phase 1: Safe Foundations
1. Add `username` as staff numeric ID (ensure uniqueness).
2. Add new login routes (`/superadmin`, `/admin`, `/purok`) with role labels.
3. Update login controller to accept numeric username and redirect correctly.
4. Remove login/register buttons from public homepage.

### Phase 2: Public Forms (no auth)
5. Create new public controllers/views for:
   - Public announcements (reuse existing AnnouncementPublicController)
   - Public clearance request form
   - Public incident report form
6. Update routes to make these public pages accessible without auth.
7. Update incident report form to require phone number.

### Phase 3: Clearance Workflow Changes
8. Modify RequestController to allow public submissions (no user_id).
9. Remove barangay final confirmation logic (remove routes, views, controllers for barangay approval of clearances).
10. Update purok leader approval to be final approval.
11. Add PDF generation and preview for clearance on approval.

### Phase 4: Resident Masterlist Management
12. Add Resident CRUD views for purok leaders (scoped to their purok).
13. Add Resident CRUD views for barangay officials (all puroks, with confirmation dialogs).
14. Add “Add this resident” option during purok leader validation of requests.

### Phase 5: Analytics & UI Polish
15. Add analytics graphs to admin/official/secretary dashboards.
16. Update incident status UI wording from “resolved” to “closed/completed”.
17. Add live search, pagination, and filters where needed.

### Phase 6: Cleanup
18. Remove unused features/routes/views (old resident registration, old barangay approval flow for clearances).
19. Clean up migrations and unused code.

---

## 8) Notes for the Next AI/Developer

- **Do not break existing functionality for barangay officials** (except for the specific removal of barangay final confirmation for clearances).
- **Keep the incident report workflow** (barangay officials still approve/reject/incidents).
- **Reuse existing models and policies** where possible; extend rather than replace.
- **Test public forms** thoroughly to ensure they work without authentication and still validate properly.
- **Backup the database** before beginning Phase 3 (workflow changes).

---

## 9) Confirmation Checklist

- [ ] Staff numeric username via `username` field
- [ ] Role-labeled login URLs (`/superadmin`, `/admin`, `/purok`)
- [ ] Remove login/register from public homepage
- [ ] Public clearance request form (no auth)
- [ ] Public incident report form (no auth, phone required)
- [ ] Purok leader as final approver for clearances
- [ ] Remove barangay final confirmation for clearances
- [ ] Resident masterlist CRUD for purok leaders (their purok only)
- [ ] Resident masterlist CRUD for barangay officials (all puroks, confirm dialogs)
- [ ] Clearance PDF preview and editable fields
- [ ] Analytics graphs with time and status filters
- [ ] UI wording: resolved → closed/completed
- [ ] Add branding (logo, header, purok name) to PDFs
- [ ] Add “Add this resident” option during purok leader validation

---

## 10. Next Steps

1. Review and approve this plan.
2. Provide the clearance template (image or text) for PDF generation.
3. Begin implementation in the order outlined in Section 7.

---
 
## 11) Consolidated New Requirements (Preferences - Jan 29, 2026)
 
### 11.1 Authentication, Roles, and Access
- Employee number as staff login (use `username`, numeric and unique for staff).
- Separate interfaces and URLs; remove public login from homepage:
  - `/superadmin` → Super Admin (Chairman)
  - `/admin` → Barangay Official / Secretary
  - `/purok` → Purok Leader
- Current Admin account becomes the Super Admin account (for Chairman access).
- Super Admin can fully manage Secretary accounts (create, edit, reset password, activate/deactivate).
- Barangay Official can manage Purok Leaders (add, edit, add photo, delete) and add new Resident accounts.
- Admin should have features similar to Purok Leader for managing requests within their scope.
- Email is optional when Secretary encodes resident info.
 
### 11.2 Public Access and Forms (No Resident Accounts)
- Open public request forms for clearances and incident reports; no registration/login required.
- On Purok Leader approval, send an email (if provided) with a printable preview and attach an electronic copy of the Purok Clearance.
- Include ARTA form CSM in the process (IRL form and mapping to be provided).
- Printed outputs (resident info, clearances, etc.) must include Purok field, logo, and proper header.
 
### 11.3 Resident Masterlist
- Collect blank forms of resident information inputs (IRL task) to guide data fields.
 
### 11.4 Analytics and UI Enhancements
- Add graphs for Admin/Secretary/Barangay Official dashboards with annual, quarterly, and monthly filters.
- Add live search where needed.
- Add pagination and filter types on selected pages.
- Change status wording from "resolved" to "closed/completed" (UI-only).
 
---
 
## 12) Clarifications & Decisions
 
- Employee number login: Yes, numeric-only and unique across all staff roles.
- Role URLs: Yes, final route paths are `/superadmin`, `/admin`, and `/purok`.
- Super Admin authority over Secretary accounts: Yes—create, edit profile, reset password, activate/deactivate, and role changes.
- Barangay Official powers:
  - Residents created by officials are masterlist-only (no login).
  - Purok Leader account fields include photo specs, contact number, purok assignment, and personal info (age, gender, etc.); maintain audit logs.
- Electronic clearance delivery when no email is provided: Provide a downloadable link (no QR). Show a preview before download.
- ARTA CSM: Official form and field mapping to be provided (IRL dependency).
- Printing and branding: Include official logo and header; ensure Purok field is printed on resident info, clearances, etc.
- Search/pagination/filters: Not for now (scope to be defined later).
- Analytics specifics (status definitions, breakdowns, exports): Not for now.
