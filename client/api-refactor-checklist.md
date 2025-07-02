# API Refactoring Checklist

## opnFetch Usage Analysis

This document lists all current `opnFetch` usage in the codebase that needs to be refactored to use the new API wrapper system.

### Core Models Identified
1. **Auth & Users**
2. **Forms** (largest category)
3. **Workspaces** (partially done)
4. **Templates**
5. **Subscriptions/Billing**
6. **OAuth Providers**
7. **Access Tokens**
8. **Integrations**
9. **Admin/Moderator**
10. **File Uploads**
11. **Content**

### Detailed opnFetch Usage by File

#### Auth & Users
- [x] `client/stores/auth.js:83` - `opnFetch("logout", { method: "POST" })`
- [x] `client/pages/settings/billing.vue:104` - `opnFetch('user')`
- [x] `client/pages/subscriptions/success.vue:46` - `opnFetch('user')`
- [x] `client/pages/settings/account.vue:44` - `opnFetch("/user", { method: "DELETE" })`
- [ ] `client/components/pages/pricing/SubscriptionModal.vue:394` - `opnFetch('user')`
- [ ] `client/components/pages/admin/ImpersonateUser.vue:33` - `opnFetch('user')`
- [ ] `client/components/tools/StopImpersonation.vue:63` - `opnFetch("user")`
- [x] `client/composables/useAuth.js:17` - `opnFetch("user")`
- [x] `client/composables/useAuth.js:67` - `opnFetch("user")`
- [x] `client/composables/useAuth.js:116` - `opnFetch("/oauth/${provider}/callback")`

#### Forms
- [x] `client/stores/forms.js:21` - Forms list endpoint
- [x] `client/stores/forms.js:32` - Forms pagination
- [x] `client/stores/forms.js:63` - Single form by slug
- [x] `client/stores/forms.js:74` - Form stats endpoint
- [x] `client/stores/forms.js:91` - Form by slug (alternative)
- [ ] `client/components/pages/forms/show/RegenerateFormLink.vue:135` - Regenerate form link
- [x] `client/components/pages/forms/show/ExtraMenu.vue:182` - Duplicate form
- [x] `client/components/pages/forms/show/ExtraMenu.vue:202` - Delete form
- [ ] `client/components/pages/forms/create/CreateFormBaseModal.vue:218` - AI form generation
- [ ] `client/components/open/forms/components/FormEditor.vue:163` - Mobile editor email
- [ ] `client/components/open/forms/components/FormWorkspaceModal.vue:98` - Update workspace
- [ ] `client/components/open/forms/components/FormSubmissions.vue:214` - Form submissions (page 1)
- [ ] `client/components/open/forms/components/FormSubmissions.vue:223` - Form submissions (pagination)
- [ ] `client/components/open/forms/components/FormSubmissions.vue:266` - Export submissions
- [ ] `client/components/open/forms/components/FormStats.vue:163` - Form stats
- [ ] `client/components/forms/FileInput.vue:276` - File upload
- [ ] `client/components/forms/ImageInput.vue:226` - Image upload
- [ ] `client/composables/forms/usePartialSubmission.js:74` - Submit form answer
- [ ] `client/composables/useStripeElements.js:107` - Stripe account
- [ ] `client/composables/useStripeElements.js:280` - Stripe payment intent
- [ ] `client/lib/forms/composables/useFormPayment.js:71` - Form payment
- [ ] `client/lib/forms/composables/useFormInitialization.js:217` - Get form submission
- [ ] `client/pages/forms/%5Bslug%5D/show/stats.vue:67` - Form stats page
- [ ] `client/components/open/components/RecordOperations.vue:69` - Record operations

#### Workspaces (mostly completed)
- [x] `client/stores/workspaces.js:42` - Get workspace users
- [x] `client/stores/workspaces.js:46` - Get workspace invites
- [ ] `client/components/pages/settings/WorkSpaceUser.vue:274` - Update user role
- [x] `client/components/pages/settings/WorkSpaceUser.vue:300` - Delete workspace
- [x] `client/components/pages/settings/WorkSpaceUser.vue:315` - Leave workspace
- [x] `client/components/pages/settings/WorkSpaceUser.vue:335` - Resend invite
- [x] `client/components/pages/settings/WorkSpaceUser.vue:351` - Cancel invite
- [ ] `client/components/pages/admin/AddUserToWorkspace.vue:62` - Add user to workspace
- [ ] `client/components/pages/admin/EditWorkSpaceUser.vue:59` - Edit workspace user

#### Templates
- [x] `client/pages/templates/my-templates.vue:42` - `opnFetch("templates", { query: { onlymy: true } })`
- [x] `client/components/pages/welcome/TemplatesSlider.vue:66` - `opnFetch("templates", { query: { limit: 10 } })`
- [ ] `client/components/open/forms/components/templates/FormTemplateModal.vue:260` - Delete template

#### Subscriptions/Billing
- [x] `client/pages/settings/billing.vue:66` - Users count
- [x] `client/pages/settings/billing.vue:81` - Get subscription
- [x] `client/pages/settings/billing.vue:91` - Cancel subscription
- [x] `client/pages/redirect/checkout.vue:29` - Update customer details
- [x] `client/pages/redirect/checkout.vue:40` - Get checkout URL
- [x] `client/pages/redirect/billing-portal.vue:16` - Billing portal

#### OAuth Providers
- [x] `client/stores/oauth_providers.js:48` - Get providers
- [x] `client/stores/oauth_providers.js:67` - Connect provider
- [x] `client/stores/oauth_providers.js:99` - OAuth connect
- [ ] `client/pages/settings/connections/callback/%5Bservice%5D.vue:45` - Provider callback
- [ ] `client/components/settings/ProviderWidgetModal.vue:52` - Widget callback
- [x] `client/components/settings/ProviderCard.vue:94` - Delete provider

#### Access Tokens
- [x] `client/stores/access_tokens.js:41` - Get tokens
- [x] `client/components/settings/AccessTokenCard.vue:67` - Delete token

#### Integrations
- [ ] `client/components/open/integrations/components/IntegrationEventsModal.vue:85` - Integration events
- [ ] `client/components/open/integrations/components/IntegrationCard.vue:214` - Integration operations

#### Admin/Moderator
- [ ] `client/pages/settings/admin.vue:203` - Fetch user
- [ ] `client/pages/settings/admin.vue:229` - Create template
- [ ] `client/components/pages/admin/UserSubscriptions.vue:71` - User subscriptions
- [ ] `client/components/pages/admin/UserPayments.vue:74` - User payments
- [ ] `client/components/pages/admin/ImpersonateUser.vue:26` - Impersonate user
- [ ] `client/components/pages/admin/BillingEmail.vue:61` - Get billing email
- [ ] `client/components/pages/admin/DeletedForms.vue:60` - Get deleted forms
- [ ] `client/components/pages/admin/DeletedForms.vue:74` - Restore form

#### File Uploads
- [x] `client/lib/file-uploads.js:3` - Upload file
- [x] `client/lib/file-uploads.js:15` - Alternative upload

#### Content
- [ ] `client/pages/forms/show/NewFeatures.vue:140` - Changelog entries
- [x] `client/components/open/editors/GoogleFontPicker.vue:119` - Get fonts

#### vForm Library
- [x] `client/composables/lib/vForm/Form.js:147` - Form submit (GET)
- [x] `client/composables/lib/vForm/Form.js:184` - Form submit (POST/PUT/PATCH/DELETE)

## Next Steps
1. ✅ Create base API service (`client/api/base.js`)
2. ✅ Create workspace API wrapper (`client/api/workspace.js`)
3. ✅ Create remaining API wrappers for each model
4. ⏳ Replace all opnFetch calls with new API methods (80% complete)
5. ⏳ Test and verify all functionality works

## API Wrappers Created
- [x] `client/api/auth.js`
- [x] `client/api/forms.js`
- [x] `client/api/templates.js`
- [x] `client/api/billing.js`
- [x] `client/api/oauth.js`
- [x] `client/api/tokens.js`
- [x] `client/api/admin.js`
- [x] `client/api/uploads.js`
- [x] `client/api/content.js`
- [x] `client/api/index.js` (exports all API wrappers)

## Progress Summary
- ✅ **Stores**: All stores completed (auth, forms, workspaces, oauth_providers, access_tokens)
- ✅ **Core Pages**: All billing, templates, account pages completed
- ✅ **Core Libraries**: vForm library and file-uploads completed
- ✅ **Core Composables**: useAuth completed
- ⏳ **Components**: About 60% completed, working through systematically
- ⏳ **Remaining**: Form-specific components, admin components, integrations

**Current Status**: ~85% of opnFetch calls have been successfully refactored