<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Agj\NovaTranslations\Http\Controllers\TranslatorController;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::get('/list', TranslatorController::class . '@index');

Route::post('/update', TranslatorController::class . '@update_translation');
