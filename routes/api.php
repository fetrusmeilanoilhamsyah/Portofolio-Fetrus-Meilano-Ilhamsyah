<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// CSP Report Endpoint
Route::post('/csp-report', function (Request $request) {
    $content = substr($request->getContent(), 0, 2048);
    Log::channel('csp')->info('CSP Violation: '.$content);

    return response()->noContent();
})->middleware(['throttle:10,1']);
