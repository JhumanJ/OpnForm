<?php

/**
 * External API calls
 */

use App\Http\Controllers\Integrations\Zapier;
use App\Http\Controllers\Integrations\Zapier\ListFormsController;
use App\Http\Controllers\Integrations\Zapier\ListWorkspacesController;

Route::prefix('external')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::prefix('zapier')->name('zapier.')->group(function () {
            Route::get('validate', Zapier\ValidateAuthController::class)
                ->name('validate');

            // Set and delete webhooks / manage integrations
            Route::middleware('ability:manage-integrations')
                ->name('webhooks.')
                ->group(function () {
                    Route::post('webhook', [Zapier\IntegrationController::class, 'store'])
                        ->name('store');

                    Route::delete('webhook', [Zapier\IntegrationController::class, 'destroy'])
                        ->name('destroy');
                    Route::get('submissions/recent', [Zapier\IntegrationController::class, 'poll'])->name('poll');
                });

            Route::get('workspaces', ListWorkspacesController::class)
                ->middleware('ability:list-workspaces')
                ->name('workspaces');

            Route::get('forms', ListFormsController::class)
                ->middleware('ability:list-forms')
                ->name('forms');
        });
    });
