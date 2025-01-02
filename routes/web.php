<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;

Route::get('/', function () {
    return view('welcome');
});
Route::post('/process-video', [VideoController::class, 'processVideo']);
Route::post('/process-logo-video', [VideoController::class, 'processLogoVideo']);
Route::post('/process-crop-video', [VideoController::class, 'processCropVideo']);
Route::post('/process-resize-video', [VideoController::class, 'resizeVideo']);
Route::post('/trim-video', [VideoController::class, 'trimVideo']);
Route::get('/video-form', function () {
    return view('process_video');
});
Route::get('/video-logo-form', function () {
    return view('process_logo_Video');
});
Route::get('/video-trim-form', function () {
    return view('trimVideo');
});
Route::get('/video-crop-form', function () {
    return view('cropVideo');
});
Route::get('/video-resize-form', function () {
    return view('resizeVideo');
});
