<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailController;

Route::post('/send-email', [EmailController::class, 'sendEmail'])->middleware('api.key');
