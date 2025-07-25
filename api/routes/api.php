<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Forms\FormController;
use App\Http\Controllers\Forms\FormStatsController;
use App\Http\Controllers\Forms\FormSubmissionController;
use App\Http\Controllers\Forms\Integration\FormIntegrationsController;
use App\Http\Controllers\Forms\Integration\FormIntegrationsEventController;
use App\Http\Controllers\Forms\Integration\FormZapierWebhookController;
use App\Http\Controllers\Forms\PublicFormController;
use App\Http\Controllers\Settings\OAuthProviderController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\TokenController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Forms\TemplateController;
use App\Http\Controllers\Auth\UserInviteController;
use App\Http\Controllers\Forms\FormPaymentController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\WorkspaceUserController;
use App\Http\Middleware\Form\ResolveFormMiddleware;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\HealthCheckController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

if (config('app.self_hosted')) {
    Route::get('/healthcheck', [HealthCheckController::class, 'apiCheck']);
}

Route::group(['middleware' => 'auth.multi'], function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');


    Route::get('user', [UserController::class, 'current'])->name('user.current');
    Route::delete('user', [UserController::class, 'deleteAccount']);

    Route::prefix('/settings')->name('settings.')->group(function () {
        Route::patch('/profile', [ProfileController::class, 'update']);
        Route::patch('/password', [PasswordController::class, 'update']);

        Route::prefix('/tokens')->name('tokens.')->middleware('require-pro')->group(function () {
            Route::get('/', [TokenController::class, 'index'])->name('index');
            Route::post('/', [TokenController::class, 'store'])->name('store');
            Route::delete('{token}', [TokenController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('/providers')->name('providers.')->group(function () {
            Route::delete('/{provider}', [OAuthProviderController::class, 'destroy'])->name('destroy');
        });
    });

    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::put('/update-customer-details', [SubscriptionController::class, 'updateStripeDetails'])->name('update-stripe-details');
        Route::get('/new/{subscription}/{plan}/checkout/{trial?}', [SubscriptionController::class, 'checkout'])
            ->name('checkout')
            ->where('subscription', '(' . implode('|', SubscriptionController::SUBSCRIPTION_NAMES) . ')')
            ->where('plan', '(' . implode('|', SubscriptionController::SUBSCRIPTION_PLANS) . ')');
        Route::get('/billing-portal', [SubscriptionController::class, 'billingPortal'])->name('billing-portal');
        Route::get('/users-count', [SubscriptionController::class, 'getUsersCount'])->name('users-count');
    });

    Route::prefix('open')->name('open.')->group(function () {
        Route::get('/providers', [OAuthProviderController::class, 'index'])->name('providers');

        Route::get('/forms', [FormController::class, 'indexAll'])->name('forms.index-all');
        Route::get('/forms/{slug}', [FormController::class, 'show'])->name('forms.show');

        Route::prefix('workspaces')->name('workspaces.')->group(function () {
            Route::get('/', [WorkspaceController::class, 'index'])->name('index');
            Route::post('/create', [WorkspaceController::class, 'create'])->name('create');

            Route::prefix('/{workspaceId}')->group(function () {
                Route::get(
                    '/users',
                    [WorkspaceUserController::class, 'listUsers']
                )->name('users.index');
                Route::get(
                    '/invites',
                    [UserInviteController::class, 'listInvites']
                )->name('invites.index');

                Route::post(
                    '/users/add',
                    [WorkspaceUserController::class, 'addUser']
                )->name('users.add');

                Route::delete(
                    '/users/{userId}/remove',
                    [WorkspaceUserController::class, 'removeUser']
                )->name('users.remove');

                Route::post(
                    '/invites/{inviteId}/resend',
                    [UserInviteController::class, 'resendInvite']
                )->name('invites.resend');

                Route::delete(
                    '/invites/{inviteId}/cancel',
                    [UserInviteController::class, 'cancelInvite']
                )->name('invites.cancel');

                Route::put(
                    '/users/{userId}/update-role',
                    [WorkspaceUserController::class, 'updateUserRole']
                )->name('users.update-role');

                // leave workspace route
                Route::post(
                    '/leave',
                    [WorkspaceUserController::class, 'leaveWorkspace']
                )->name('leave');

                Route::get(
                    '/forms',
                    [FormController::class, 'index']
                )->name('forms.index');
                Route::put('/custom-domains', [WorkspaceController::class, 'saveCustomDomain'])->name('save-custom-domains');
                Route::put('/email-settings', [WorkspaceController::class, 'saveEmailSettings'])->name('save-email-settings');
                Route::put('/', [WorkspaceController::class, 'update'])->name('update');
                Route::delete('/', [WorkspaceController::class, 'delete'])->name('delete');

                Route::middleware('pro-form')->group(function () {
                    Route::get('form-stats/{formId}', [FormStatsController::class, 'getFormStats'])->name('form.stats');
                    Route::get('form-stats-details/{formId}', [FormStatsController::class, 'getFormStatsDetails'])->name('form.stats-details');
                });
            });
        });

        Route::prefix('forms')->name('forms.')->group(function () {
            Route::post('/', [FormController::class, 'store'])->name('store');
            Route::post('/{id}/workspace/{workspace_id}', [FormController::class, 'updateWorkspace'])->name('workspace.update');
            Route::put('/{id}', [FormController::class, 'update'])->name('update')->middleware([ResolveFormMiddleware::class]);
            Route::delete('/{id}', [FormController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/mobile-editor-email', [FormController::class, 'mobileEditorEmail'])->name('mobile-editor-email');

            Route::prefix('/{id}/submissions')->name('submissions.')->group(function () {
                Route::get('/', [FormSubmissionController::class, 'submissions'])->name('index');
                Route::put('/{submission_id}', [FormSubmissionController::class, 'update'])->name('update')->middleware([ResolveFormMiddleware::class]);
                Route::post('/export', [FormSubmissionController::class, 'export'])->name('export');
                Route::get('/file/{filename}', [FormSubmissionController::class, 'submissionFile'])
                    ->middleware('signed')
                    ->withoutMiddleware(['auth.multi'])
                    ->name('file');
                Route::delete('/{submission_id}', [FormSubmissionController::class, 'destroy'])->name('destroy');
            });

            // Form Admin tool
            Route::put(
                '/{id}/regenerate-link/{option}',
                [FormController::class, 'regenerateLink']
            )
                ->where('option', '(uuid|slug)')
                ->name('regenerate-link');
            Route::post(
                '/{id}/duplicate',
                [FormController::class, 'duplicate']
            )->name('duplicate');

            // Assets & uploaded files
            Route::post(
                '/assets/upload',
                [FormController::class, 'uploadAsset']
            )->withoutMiddleware(['auth.multi'])->name('assets.upload');
            Route::get(
                '/{id}/uploaded-file/{filename}',
                [FormController::class, 'viewFile']
            )->name('uploaded_file');

            // Integrations
            Route::post(
                '/webhooks/zapier',
                [FormZapierWebhookController::class, 'store']
            )->name('integrations.zapier-hooks.store');
            Route::delete(
                '/webhooks/zapier/{id}',
                [FormZapierWebhookController::class, 'delete']
            )->name('integrations.zapier-hooks.delete');
            Route::get(
                '/{id}/integrations',
                [FormIntegrationsController::class, 'index']
            )->name('integrations');
            Route::post(
                '/{id}/integration',
                [FormIntegrationsController::class, 'create']
            )->name('integration.create');
            Route::put(
                '/{id}/integration/{integrationid}',
                [FormIntegrationsController::class, 'update']
            )->name('integration.update');
            Route::delete(
                '/{id}/integration/{integrationid}',
                [FormIntegrationsController::class, 'destroy']
            )->name('integration.destroy');
            Route::get(
                '/{id}/integration/{integrationid}/events',
                [FormIntegrationsEventController::class, 'index']
            )->name('integrations.events');
        });
    });

    Route::group(['middleware' => 'moderator', 'prefix' => 'moderator'], function () {
        Route::post(
            'create-template',
            [\App\Http\Controllers\Admin\AdminController::class, 'createTemplate']
        );
        Route::get(
            'fetch-user/{identifier}',
            [\App\Http\Controllers\Admin\AdminController::class, 'fetchUser']
        );
        Route::get(
            'impersonate/{userId}',
            [\App\Http\Controllers\Admin\ImpersonationController::class, 'impersonate']
        );
        Route::patch(
            'apply-discount',
            [\App\Http\Controllers\Admin\AdminController::class, 'applyDiscount']
        );
        Route::patch(
            'extend-trial',
            [\App\Http\Controllers\Admin\AdminController::class, 'extendTrial']
        );
        Route::patch(
            'cancellation-subscription',
            [\App\Http\Controllers\Admin\AdminController::class, 'cancelSubscription']
        );

        Route::patch(
            'send-password-reset-email',
            [\App\Http\Controllers\Admin\AdminController::class, 'sendPasswordResetEmail']
        );

        Route::post(
            'block-user',
            [\App\Http\Controllers\Admin\AdminController::class, 'blockUser']
        );
        Route::post(
            'unblock-user',
            [\App\Http\Controllers\Admin\AdminController::class, 'unblockUser']
        );

        Route::group(['prefix'  => 'billing'], function () {
            Route::get('{userId}/email', [\App\Http\Controllers\Admin\BillingController::class, 'getEmail']);
            Route::patch('/email', [\App\Http\Controllers\Admin\BillingController::class, 'updateEmail']);
            Route::get('{userId}/subscriptions', [\App\Http\Controllers\Admin\BillingController::class, 'getSubscriptions']);
            Route::get('{userId}/payments', [\App\Http\Controllers\Admin\BillingController::class, 'getPayments']);
        });

        Route::group(['prefix' => 'forms'], function () {
            Route::get('{userId}/deleted-forms', [\App\Http\Controllers\Admin\FormController::class, 'getDeletedForms']);
            Route::patch('{slug}/restore', [\App\Http\Controllers\Admin\FormController::class, 'restoreDeletedForm']);
        });
    });
});

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', [LoginController::class, 'login'])->name('login');
    Route::post('register', [RegisterController::class, 'register']);

    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('password/reset', [ResetPasswordController::class, 'reset']);

    Route::post('email/verify/{user}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend']);
});

Route::group(['prefix' => 'appsumo'], function () {
    Route::get('oauth/callback', [\App\Http\Controllers\Auth\AppSumoAuthController::class, 'handleCallback'])->name('appsumo.callback');
    Route::post('webhook', [\App\Http\Controllers\Webhook\AppSumoController::class, 'handle'])->name('appsumo.webhook');
});

/*
 * OAuth routes (public - authentication handled in controller)
 */
Route::prefix('oauth')->name('oauth.')->group(function () {
    Route::post('/connect/{provider}', [OAuthController::class, 'redirect'])->name('redirect');
    Route::post('/{provider}/callback', [OAuthController::class, 'callback'])->name('callback');
    Route::post('/widget-callback/{provider}', [OAuthController::class, 'handleWidgetCallback'])->name('widget.callback');
});

/*
 * Public Forms related routes
 */
Route::prefix('forms')->name('forms.')->group(function () {
    Route::middleware('protected-form')->group(function () {
        Route::post('{slug}/answer', [PublicFormController::class, 'answer'])->name('answer')->middleware(HandlePrecognitiveRequests::class);
        Route::get('{slug}/stripe-connect/get-account', [FormPaymentController::class, 'getAccount'])->name('stripe-connect.get-account')->middleware(HandlePrecognitiveRequests::class);
        Route::post('{slug}/stripe-connect/payment-intent', [FormPaymentController::class, 'createIntent'])->name('stripe-connect.create-intent')->middleware(HandlePrecognitiveRequests::class);

        // Form content endpoints (user lists, relation lists etc.)
        Route::get(
            '{slug}/users',
            [PublicFormController::class, 'listUsers']
        )->name('users.index');
    });

    // Get form and submit
    Route::get('{slug}', [PublicFormController::class, 'show'])->name('show');
    Route::get('{slug}/submissions/{submission_id}', [PublicFormController::class, 'fetchSubmission'])->name('fetchSubmission');

    // File uploads
    Route::get('assets/{assetFileName}', [PublicFormController::class, 'showAsset'])->name('assets.show');

    // AI
    Route::post('ai/generate', [\App\Http\Controllers\Forms\AiFormController::class, 'generateForm'])->name('ai.generate');
    Route::get('ai/{aiFormCompletion}', [\App\Http\Controllers\Forms\AiFormController::class, 'show'])->name('ai.show');
    Route::post('ai/generate-fields', [\App\Http\Controllers\Forms\AiFormController::class, 'generateFields'])->name('ai.generate-fields');
});

/**
 * Other public routes
 */
Route::prefix('content')->name('content.')->group(function () {
    Route::get('/feature-flags', [\App\Http\Controllers\Content\FeatureFlagsController::class, 'index'])->name('feature-flags');
    Route::get('changelog/entries', [\App\Http\Controllers\Content\ChangelogController::class, 'index'])->name('changelog.entries');
});

Route::get('/sitemap-urls', [\App\Http\Controllers\Content\SitemapController::class, 'index'])->name('sitemap.index');

// Fonts
Route::get('/fonts', [\App\Http\Controllers\Content\FontsController::class, 'index'])->name('fonts.index');

// Templates
Route::prefix('templates')->group(function () {
    Route::get('/', [TemplateController::class, 'index'])->name('templates.index');
    Route::post('/', [TemplateController::class, 'create'])->name('templates.create');
    Route::get('/{slug}', [TemplateController::class, 'show'])->name('templates.show');
    Route::put('/{id}', [TemplateController::class, 'update'])->name('templates.update');
    Route::delete('/{id}', [TemplateController::class, 'destroy'])->name('templates.destroy');
});

Route::post(
    '/stripe/webhook',
    [\App\Http\Controllers\Webhook\StripeController::class, 'handleWebhook']
)->name('cashier.webhook');

Route::post(
    '/vapor/signed-storage-url',
    [\App\Http\Controllers\Content\SignedStorageUrlController::class, 'store']
)->name('vapor.signed-storage-url');
Route::post(
    '/upload-file',
    [\App\Http\Controllers\Content\FileUploadController::class, 'upload']
)->name('upload-file');

Route::get('local/temp/{path}', function (Request $request, string $path) {
    if (!$request->hasValidSignature()) {
        abort(401);
    }

    return response()->file(Storage::path($path), ['Content-Type' => Storage::mimeType($path)]);
})->where('path', '(.*)')->name('local.temp');

Route::get('caddy/ask-certificate/{secret?}', [\App\Http\Controllers\CaddyController::class, 'ask'])
    ->name('caddy.ask')->middleware(\App\Http\Middleware\CaddyRequestMiddleware::class);
