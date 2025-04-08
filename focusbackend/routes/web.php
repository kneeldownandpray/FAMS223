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

Route::get('/api/image/{filename}', function ($filename) {
    $path = storage_path('app/public/profile_pictures/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    $mimeType = mime_content_type($path);

    return response()->file($path, [
        'Content-Type' => $mimeType,
        'Access-Control-Allow-Origin' => '*',
    ]);
});
