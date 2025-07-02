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
- [ ] `client/stores/auth.js:83` - `opnFetch("logout", { method: "POST" })`
- [ ] `client/pages/settings/billing.vue:104` - `opnFetch('user')`
- [ ] `client/pages/subscriptions/success.vue:46` - `opnFetch('user')`
- [ ] `client/pages/settings/account.vue:44` - `opnFetch("/user", { method: "DELETE" })`
- [ ] `client/components/pages/pricing/SubscriptionModal.vue:394` - `opnFetch('user')`
- [ ] `client/components/pages/admin/ImpersonateUser.vue:33` - `opnFetch('user')`
- [ ] `client/components/tools/StopImpersonation.vue:63` - `opnFetch("user")`
- [ ] `client/composables/useAuth.js:17` - `opnFetch("user")`
- [ ] `client/composables/useAuth.js:67` - `opnFetch("user")`
- [ ] `client/composables/useAuth.js:116` - `opnFetch("/oauth/${provider}/callback")`

#### Forms
- [ ] `client/stores/forms.js:21` - Forms list endpoint
- [ ] `client/stores/forms.js:32` - Forms pagination
- [ ] `client/stores/forms.js:63` - Single form by slug
- [ ] `client/stores/forms.js:74` - Form stats endpoint
- [ ] `client/stores/forms.js:91` - Form by slug (alternative)
- [ ] `client/components/pages/forms/show/RegenerateFormLink.vue:135` - Regenerate form link
- [ ] `client/components/pages/forms/show/ExtraMenu.vue:182` - Duplicate form
- [ ] `client/components/pages/forms/show/ExtraMenu.vue:202` - Delete form
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

#### Workspaces (partially completed)
- [ ] `client/stores/workspaces.js:42` - Get workspace users
- [ ] `client/stores/workspaces.js:46` - Get workspace invites
- [ ] `client/components/pages/settings/WorkSpaceUser.vue:274` - Update user role
- [ ] `client/components/pages/settings/WorkSpaceUser.vue:300` - Delete workspace
- [ ] `client/components/pages/settings/WorkSpaceUser.vue:315` - Leave workspace
- [ ] `client/components/pages/settings/WorkSpaceUser.vue:335` - Resend invite
- [ ] `client/components/pages/settings/WorkSpaceUser.vue:351` - Cancel invite
- [ ] `client/components/pages/admin/AddUserToWorkspace.vue:62` - Add user to workspace
- [ ] `client/components/pages/admin/EditWorkSpaceUser.vue:59` - Edit workspace user

#### Templates
- [ ] `client/pages/templates/my-templates.vue:42` - `opnFetch("templates", { query: { onlymy: true } })`
- [ ] `client/components/pages/welcome/TemplatesSlider.vue:66` - `opnFetch("templates", { query: { limit: 10 } })`
- [ ] `client/components/open/forms/components/templates/FormTemplateModal.vue:260` - Delete template

#### Subscriptions/Billing
- [ ] `client/pages/settings/billing.vue:66` - Users count
- [ ] `client/pages/settings/billing.vue:81` - Get subscription
- [ ] `client/pages/settings/billing.vue:91` - Cancel subscription
- [ ] `client/pages/redirect/checkout.vue:29` - Update customer details
- [ ] `client/pages/redirect/checkout.vue:40` - Get checkout URL
- [ ] `client/pages/redirect/billing-portal.vue:16` - Billing portal

#### OAuth Providers
- [ ] `client/stores/oauth_providers.js:48` - Get providers
- [ ] `client/stores/oauth_providers.js:67` - Connect provider
- [ ] `client/stores/oauth_providers.js:99` - OAuth connect
- [ ] `client/pages/settings/connections/callback/%5Bservice%5D.vue:45` - Provider callback
- [ ] `client/components/settings/ProviderWidgetModal.vue:52` - Widget callback
- [ ] `client/components/settings/ProviderCard.vue:94` - Delete provider

#### Access Tokens
- [ ] `client/stores/access_tokens.js:41` - Get tokens
- [ ] `client/components/settings/AccessTokenCard.vue:67` - Delete token

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
- [ ] `client/lib/file-uploads.js:3` - Upload file
- [ ] `client/lib/file-uploads.js:15` - Alternative upload

#### Content
- [ ] `client/pages/forms/show/NewFeatures.vue:140` - Changelog entries
- [ ] `client/components/open/editors/GoogleFontPicker.vue:119` - Get fonts

#### vForm Library
- [ ] `client/composables/lib/vForm/Form.js:147` - Form submit (GET)
- [ ] `client/composables/lib/vForm/Form.js:184` - Form submit (POST/PUT/PATCH/DELETE)

## Next Steps
1. ✅ Create base API service (`client/api/base.js`)
2. ✅ Create workspace API wrapper (`client/api/workspace.js`)
3. ⏳ Create remaining API wrappers for each model
4. ⏳ Replace all opnFetch calls with new API methods
5. ⏳ Test and verify all functionality works

## API Wrappers to Create
- [ ] `client/api/auth.js`
- [ ] `client/api/forms.js`
- [ ] `client/api/templates.js`
- [ ] `client/api/billing.js`
- [ ] `client/api/oauth.js`
- [ ] `client/api/tokens.js`
- [ ] `client/api/integrations.js`
- [ ] `client/api/admin.js`
- [ ] `client/api/uploads.js`
- [ ] `client/api/content.js`