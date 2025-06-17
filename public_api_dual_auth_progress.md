# Public API Dual Authentication Implementation Progress

_Last updated: 2025-06-17 00:05 UTC_
_Last updated: 2025-06-17 00:08 UTC_
_Last updated: 2025-06-17 00:11 UTC_
_Last updated: 2025-06-17 00:25 UTC_
_Last updated: 2025-06-17 00:32 UTC_
_Last updated: 2025-06-17 00:38 UTC_
_Last updated: 2025-06-17 00:43 UTC_
_Last updated: 2025-06-17 00:47 UTC_
_Last updated: 2025-06-17 00:52 UTC_

## Overview
This document tracks the implementation progress for introducing a dual-authentication layer (JWT & Sanctum) to expose a public API while reusing existing endpoints. All updates to this file should refresh the **Last updated** timestamp and append a brief note to the **Progress log**.

---

## Task Checklist
- [x] Create `api/config/sanctum_routes.php` with the full list of whitelisted route names.
- [x] Update `api/app/Enums/AccessTokenAbility.php` with `read`/`write` cases for all resources.
- [x] Create the `api/app/Http/Middleware/AuthenticateWithJwtOrSanctum.php` middleware class.
- [x] Register the middleware alias `auth.multi` in `api/app/Http/Kernel.php`.
- [x] Change `auth:api` to `auth.multi` in `api/routes/api.php`.
- [x] Create/Update `FormPolicy` with Sanctum ability checks.
- [x] Create/Update Policy for Submissions with Sanctum ability checks.
- [x] Create/Update `WorkspacePolicy` with Sanctum ability checks.
- [x] Create/Update Policy for Workspace Users with Sanctum ability checks.
- [x] Update `client/components/AccessTokenModal.vue` (or similar) to display new abilities.
- [ ] Run all tests described in the "Testing Instructions" section.
- [ ] Document all newly exposed endpoints in `openapi.json` and the Mintlify markdown files.

---

## Progress Log
- 2025-06-17 00:00 UTC – Initialized progress tracking file.
- 2025-06-17 00:05 UTC – Created `api/config/sanctum_routes.php` whitelist configuration.
- 2025-06-17 00:08 UTC – Added granular read/write abilities to `AccessTokenAbility` enum.
- 2025-06-17 00:11 UTC – Implemented `AuthenticateWithJwtOrSanctum` middleware.
- 2025-06-17 00:25 UTC – Added `auth.multi` alias to Kernel and updated routes to use new middleware.
- 2025-06-17 00:32 UTC – Added Sanctum ability checks to `FormPolicy`.
- 2025-06-17 00:38 UTC – Added `SubmissionPolicy` with Sanctum read/write checks and registered it.
- 2025-06-17 00:43 UTC – Added Sanctum ability checks to `WorkspacePolicy`.
- 2025-06-17 00:47 UTC – Added Sanctum write ability enforcement to workspace admin actions.
- 2025-06-17 00:52 UTC – Updated token modal and store to include granular abilities and enable selection.