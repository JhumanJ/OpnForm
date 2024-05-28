<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        \Propaganistas\LaravelDisposableEmail\DisposableEmailServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        // channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
            'vapor/signed-storage-url',
            'upload-file',
        ]);

        $middleware->append([
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\AuthenticateJWT::class,
            \App\Http\Middleware\CustomDomainRestriction::class,
            \App\Http\Middleware\AcceptsJsonMiddleware::class,
        ]);

        $middleware->throttleApi('100,1');
        $middleware->api([
            \Illuminate\Session\Middleware\StartSession::class,
            \App\Http\Middleware\ImpersonationMiddleware::class,
        ]);

        $middleware->group('spa', [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->replace(\Illuminate\Foundation\Http\Middleware\TrimStrings::class, \App\Http\Middleware\TrimStrings::class);

        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
            'auth' => \App\Http\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'moderator' => \App\Http\Middleware\IsModerator::class,
            'not-subscribed' => \App\Http\Middleware\IsNotSubscribed::class,
            'pro-form' => \App\Http\Middleware\Form\ProForm::class,
            'protected-form' => \App\Http\Middleware\Form\ProtectedForm::class,
            'subscribed' => \App\Http\Middleware\IsSubscribed::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
