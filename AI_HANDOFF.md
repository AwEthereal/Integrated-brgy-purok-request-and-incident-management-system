# AI Handoff — Project Status and Instructions

This file is a persistent handoff for the next AI/developer. It summarizes context, current priorities, and where to continue. Update this file and `ai_tasks_status.json` after each meaningful step.

Last updated: <fill-on-edit>

---

## 1) Context Summary
- Framework: Laravel 11, PHP 8.2, Vite/Tailwind on frontend
- Key features: Requests (clearances), Incident reports, Purok Leader workflow, Admin/Secretary dashboards, Notifications, PDFs
- Email: Gmail SMTP configured; queue worker must run (see `start-dev.bat` which now starts `queue:work`)
- AJAX middleware: Custom handling added (e.g., `EnsureEmailIsVerified`, `CheckResidentApproved`, `CheckRole`) to return JSON on AJAX

## 2) Current Objectives (Confirmed in Major Plan)
- Public forms (no resident accounts) for clearances and incident reports
- Role URLs: `/superadmin`, `/admin`, `/purok` (remove public login from homepage)
- Super Admin (Chairman) fully manages Secretary accounts
- Barangay Official can manage Purok Leaders and add Residents (masterlist entries, no login)
- Purok Leader approval is final; email with preview + attached e-copy; if no email, downloadable link (no QR) with preview
- PDF branding: logo/header + Purok field
- Analytics: charts with monthly/quarterly/annual filters
- UI: live search, pagination/filters, “resolved” → “closed/completed”

## 3) Where We Are
- Major plan merged with Sections 11–12: see `MAJOR_CHANGES_PLAN.md`
- Read-only audits generated:
  - `AUDIT_ROUTES.txt`
  - `AUDIT_CONTROLLERS.txt`
  - `AUDIT_VIEWS.txt`
  - `AUDIT_NOTIFICATIONS.txt`
  - `AUDIT_MIDDLEWARE.txt`
- Email/queue and middleware fixes completed previously (see docs below)

## 4) References (recent docs)
- `BUG_FIX_404_REQUEST_SUBMISSION.md`
- `EMAIL_VERIFICATION_FIX.md`
- `EMAIL_NOT_SENDING_FIX.md`
- `QUICK_FIX_EMAILS.txt`

## 5) Task Board (authoritative)
- See `ai_tasks_status.json` for the live list of tasks with statuses.
- Update that JSON and this file after each change.

## 6) Immediate Next Steps (Phase 1: Foundations)
- Add DB fields (backward-compatible) for public submissions and staff `username`
- Allow staff login via numeric `username` (keep existing working while transitioning)
- Add `config/features.php` to toggle rollouts

## 7) Deprecation Targets (remove only after new flows are live)
- Resident auth pages/routes (register, my-requests, resident incidents)
- Barangay final approval routes/views in Requests
- Backup/test blades: `*.bak`, `test*.blade.php`, `layouts/app.blade.php.new`
- Duplicate JS handlers (choose one camera handler)

## 8) Quality & Security
- Run Pint for formatting, add basic Pest smoke tests
- Consider Larastan gradually
- Public endpoints: CSRF, throttles, honeypot/CAPTCHA, strict file size/MIME limits

## 9) How to Work
- Create small PRs/commits per phase
- After enabling a new flow via feature flag and validating, remove old flows in the same or next PR
- Keep this file and `ai_tasks_status.json` updated (commit them)

## 10) Contact/Decisions Needed
- Scope for live search and analytics exports: “not for now” (defer)
- ARTA CSM form assets & mapping: to be provided IRL

