<?php

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
$versions = [1];
foreach ($versions as $version) {
    Route::group([
        "namespace" => "V{$version}",
        "prefix" => "v{$version}",
    ], function () {
            /*
            |--------------------------------------------------------------------------
            | SEND Messages
            |--------------------------------------------------------------------------
            */
            Route::post('send-whatsapp-message', 'CommunicationController@sendWhatsappMessage');
            Route::post('send-sms', 'CommunicationController@sendSms');
            Route::post('send-email', 'CommunicationController@sendEmail');
    });
}

