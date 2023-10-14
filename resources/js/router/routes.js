const pages = import.meta.glob('../pages/**')

function page (path) {
  return pages[`../pages/${path}`]
}

export default [
  // Logged Users
  { path: '/home', name: 'home', component: page('home.vue') },

  // Forms
  { path: '/forms/create', name: 'forms.create', component: page('forms/create.vue') },
  { path: '/forms/create/guest', name: 'forms.create.guest', component: page('forms/create-guest.vue') },
  { path: '/forms/:slug/edit', name: 'forms.edit', component: page('forms/edit.vue') },
  {
    path: '/forms/:slug/show',
    component: page('forms/show/index.vue'),
    children: [
      { path: '', redirect: { name: 'forms.show' } },
      { path: 'submissions', name: 'forms.show', component: page('forms/show/submissions.vue') },
      { path: 'share', name: 'forms.show.share', component: page('forms/show/share.vue') },
      { path: 'analytics', name: 'forms.show.analytics', component: page('forms/show/stats.vue') }
    ]
  },

  // Subscription
  { path: '/subscriptions/success', name: 'subscriptions.success', component: page('subscriptions/success.vue') },
  { path: '/subscriptions/error', name: 'subscriptions.error', component: page('subscriptions/error.vue') },

  // Settings
  {
    path: '/settings',
    component: page('settings/index.vue'),
    children: [
      { path: '', redirect: { name: 'settings.workspaces' } },
      { path: 'workspaces', name: 'settings.workspaces', component: page('settings/workspace.vue') },
      { path: 'billing', name: 'settings.billing', component: page('settings/billing.vue') },
      { path: 'profile', name: 'settings.profile', component: page('settings/profile.vue') },
      { path: 'account', name: 'settings.account', component: page('settings/account.vue') },
      { path: 'password', name: 'settings.password', component: page('settings/password.vue') },
      { path: 'admin', name: 'settings.admin', component: page('settings/admin.vue') }
    ]
  },

  // Auth Routes
  { path: '/login', name: 'login', component: page('auth/login.vue') },
  { path: '/register', name: 'register', component: page('auth/register.vue') },
  { path: '/password/reset', name: 'password.request', component: page('auth/password/email.vue') },
  { path: '/password/reset/:token', name: 'password.reset', component: page('auth/password/reset.vue') },
  { path: '/email/verify/:id', name: 'verification.verify', component: page('auth/verification/verify.vue') },
  { path: '/email/resend', name: 'verification.resend', component: page('auth/verification/resend.vue') },

  // Public Content

  // Legal Routes
  { path: '/privacy-policy', name: 'privacy-policy', component: page('legal-help/privacy-policy.vue') },
  { path: '/terms-conditions', name: 'terms-conditions', component: page('legal-help/terms-conditions.vue') },

  // Community
  { path: '/discount-students-academics-ngos', name: 'discount-community', component: page('community/students-academics-ngos.vue') },

  // Guest Routes
  { path: '/', name: 'welcome', component: page('welcome.vue') },
  { path: '/pricing', name: 'pricing', component: page('pricing.vue') },
  { path: '/ai-form-builder', name: 'aiformbuilder', component: page('ai-form-builder.vue') },
  { path: '/integrations', name: 'integrations', component: page('integrations.vue') },
  { path: '/forms/:slug', name: 'forms.show_public', component: page('forms/show-public.vue') },

  // Templates
  { path: '/my-templates', name: 'my_templates', component: page('templates/my_templates.vue') },
  { path: '/form-templates', name: 'templates', component: page('templates/templates.vue') },
  { path: '/form-templates/:slug', name: 'templates.show', component: page('templates/show.vue') },

  { path: '*', component: page('errors/404.vue') }
]
