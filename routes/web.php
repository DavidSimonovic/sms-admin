<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CollectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageBirdController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NumberController;
use App\Http\Controllers\ScriptController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\WhatsappController;
use App\Http\Controllers\WhatsappTemplatesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    return redirect('/login');
});

// Login routes
Route::get('login', [AuthController::class, 'loginGet'])->name('login');
Route::post('/login', [AuthController::class, 'loginPost']);
Route::get('logout', [AuthController::class, 'logout']);

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Home route redirects to campaigns
    Route::get('/', function () {
        return redirect('/campaigns');
    });

    // Generate password route
    Route::get('generatepass', [AuthController::class, 'generatePass']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Campaign routes
    Route::get('/campaigns', [CampaignController::class, 'index']);
    Route::get('/campaign/create', [CampaignController::class, 'create']);
    Route::post('/campaign/save', [CampaignController::class, 'save']);
    Route::get('/campaign/edit/{id}', [CampaignController::class, 'edit']);
    Route::post('/campaign/update', [CampaignController::class, 'update']);
    Route::get('/campaign/delete/{id}', [CampaignController::class, 'delete']);

    // Number routes
    Route::get('/numbers', [NumberController::class, 'index']);
    Route::get('/number/create', [NumberController::class, 'create']);
    Route::post('/number/save/{id}', [NumberController::class, 'save']);
    Route::get('/number/edit/{id}', [NumberController::class, 'edit']);
    Route::post('/number/update', [NumberController::class, 'update'])->name('number.update');
    Route::get('/number/block/{id}', [NumberController::class, 'block']);
    Route::get('/number/delete/{id}', [NumberController::class, 'delete']);
    Route::get('/number/unblock/{id}', [NumberController::class, 'unblock']);
    Route::post('/number/search', [NumberController::class, 'search']);

    // Whatsapp
    Route::get('/whatsapp', [WhatsappController::class, 'index']);
    Route::get('/whatsapp/has_whatsapp/{id}', [WhatsappController::class, 'hasWhatsapp']);

    // WA templates

    Route::get('/whatsapp_templates', [WhatsappTemplatesController::class, 'index']);
    Route::get('/whatsapp_templates/create', [WhatsappTemplatesController::class, 'create']);
    Route::post('/whatsapp_templates/save', [WhatsappTemplatesController::class, 'save']);
    Route::get('/whatsapp_templates/edit/{id}', [WhatsappTemplatesController::class, 'edit']);
    Route::get('/whatsapp_templates/delete/{id}', [WhatsappTemplatesController::class, 'delete']);
    Route::post('/whatsapp_templates/update/{id}', [WhatsappTemplatesController::class, 'update']);


    // Template routes
    Route::get('/templates', [TemplateController::class, 'index']);
    Route::get('/template/create', [TemplateController::class, 'create']);
    Route::post('/template/save', [TemplateController::class, 'save']);
    Route::get('/template/edit/{id}', [TemplateController::class, 'edit']);
    Route::get('/template/delete/{id}', [TemplateController::class, 'delete']);
    Route::post('/template/update/{id}', [TemplateController::class, 'update']);

    // Template routes
    Route::get('/collect', [CollectController::class, 'index']);
    Route::get('/template/create', [TemplateController::class, 'create']);
    Route::post('/template/save', [TemplateController::class, 'save']);
    Route::get('/template/edit/{id}', [TemplateController::class, 'edit']);
    Route::get('/template/delete/{id}', [TemplateController::class, 'delete']);
    Route::post('/template/update/{id}', [TemplateController::class, 'update']);


    Route::get('/sites', [SiteController::class, 'index']);
    Route::get('/sites/create', [SiteController::class, 'create']);
    Route::post('/sites/save', [SiteController::class, 'save']);
    Route::get('/sites/delete/{id}', [SiteController::class, 'delete']);
    Route::get('/sites/edit/{id}', [SiteController::class, 'edit']);
    Route::post('/sites/update/{id}', [SiteController::class, 'update']);

    // Write SMS and Send SMS routes
    Route::get('/writesms', [MessageController::class, 'index']);

    Route::get('/start_sending/{id}', [MessageController::class, 'startSending']);
    Route::get('/stop_sending/{id}', [MessageController::class, 'stopSending']);

    Route::post('/sendsms', [MessageController::class, 'sendSms']);

});


Route::post('/webhook/messagebird', [MessageBirdController::class, 'handle'])->name('webhook.messagebird');

