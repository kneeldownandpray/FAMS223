<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

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
    return view('welcome');
});

// Add your test email route below
Route::get('/test-email', function () {
    Mail::raw('This is a test email from Hostinger SMTP.', function ($message) {
        $message->to('josephdichoso84@gmail.com') // Replace with your email
                ->subject('Test Email');
    });

    return 'Test email sent!';
});
