# Public API Dual Authentication Implementation Progress

_Last updated: 2025-06-17 00:05 UTC_

## Overview
This document tracks the implementation progress for introducing a dual-authentication layer (JWT & Sanctum) to expose a public API while reusing existing endpoints. All updates to this file should refresh the **Last updated** timestamp and append a brief note to the **Progress log**.

---

## Task Checklist
- [x] Create `api/config/sanctum_routes.php` with the full list of whitelisted route names.
- [ ] Update `api/app/Enums/AccessTokenAbility.php` with `read`/`write` cases for all resources.
- [ ] Create the `api/app/Http/Middleware/AuthenticateWithJwtOrSanctum.php` middleware class.
- [ ] Register the middleware alias `auth.multi` in `api/app/Http/Kernel.php`.
- [ ] Change `auth:api` to `auth.multi` in `api/routes/api.php`.
- [ ] Create/Update `FormPolicy` with Sanctum ability checks.
- [ ] Create/Update Policy for Submissions with Sanctum ability checks.
- [ ] Create/Update `WorkspacePolicy` with Sanctum ability checks.
- [ ] Create/Update Policy for Workspace Users with Sanctum ability checks.
- [ ] Update `client/components/AccessTokenModal.vue` (or similar) to display new abilities.
- [ ] Run all tests described in the "Testing Instructions" section.
- [ ] Document all newly exposed endpoints in `openapi.json` and the Mintlify markdown files.

---

## Progress Log
- 2025-06-17 00:00 UTC – Initialized progress tracking file.
- 2025-06-17 00:05 UTC – Created `api/config/sanctum_routes.php` whitelist configuration.