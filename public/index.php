<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/helpers.php';

use SwiftPHP\Core\{Application, Route};
use SwiftPHP\Support\Env;

// Load environment variables
Env::load(__DIR__ . '/..');

$app = new Application();

// ===== New Fluent Route API =====

// Simple routes - less code!
Route::get('/', fn() => view('home'));

// Route groups with prefix and middleware
Route::prefix('users')->group(function() {
    Route::get('/', 'App\\Controllers\\UserController@index');
    Route::get('/create', 'App\\Controllers\\UserController@create');
    Route::get('/{id}', 'App\\Controllers\\UserController@show');
    Route::post('/', 'App\\Controllers\\UserController@store');
});

// API routes with CORS middleware
Route::prefix('api')->middleware('SwiftPHP\\Middleware\\CorsMiddleware')->group(function() {
    Route::get('/users', fn() => json(['users' => collect(\App\Models\User::all())->toArray()]));
});

// Protected admin routes
Route::prefix('admin')
    ->middleware('SwiftPHP\\Middleware\\AuthMiddleware')
    ->group(function() {
        Route::get('/dashboard', fn() => view('admin.dashboard'));
    });

$app->run();