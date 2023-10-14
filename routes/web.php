<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post(
    '/stripe/webhook',
    [\App\Http\Controllers\Webhook\StripeController::class, 'handleWebhook']
)->name('cashier.webhook');

Route::post(
    '/vapor/signed-storage-url',
    [\App\Http\Controllers\Content\SignedStorageUrlController::class, 'store']
)->middleware([]);
Route::post(
    '/upload-file',
    [\App\Http\Controllers\Content\FileUploadController::class, 'upload']
)->middleware([]);
Route::get('local/temp/{path}', function (Request $request, string $path){
    if (!$request->hasValidSignature()) {
        abort(401);
    }
    $response = Response::make(Storage::get($path), 200);
    $response->header("Content-Type", Storage::mimeType($path));
    return $response;
})->where('path', '(.*)')->name('local.temp');

Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'getSitemap'])->name('sitemap');
