<?php

use Illuminate\Http\Request;

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


Route::get('/infusionsoft_test_create_contact', 'ApiController@exampleCustomer')->name('api.exampleCustomer');
Route::post('module_reminder_assigner', 'ApiController@moduleReminderAssigner')
    ->name('api.module_reminder_assigner');
Route::post('user_module_add', 'ApiController@userModuleAdd')->name('api.user_module_add');
