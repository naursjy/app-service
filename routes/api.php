<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [AuthController::class, 'Register']);
Route::post('login', [AuthController::class, 'Login']);
Route::post('logout', [AuthController::class, 'Logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::apiResource('/posts', PostController::class);

Route::get('/posts', [PostController::class, 'index']);
// ->middleware('auth:sanctum');

Route::get('/show/{id}', [PostController::class, 'show'])
    ->middleware('auth:sanctum');
Route::post('/store', [PostController::class, 'store'])
    ->middleware('auth:sanctum');
Route::match(['POST', 'PUT'], '/update/{id}', [PostController::class, 'update'])
    ->middleware('auth:sanctum');
Route::delete('/delete/{id}', [PostController::class, 'destroy'])
    ->middleware('auth:sanctum');

// Route::get('/posts/{id}', [PostController::class, 'show'])
//     ->middleware('auth:sanctum');
// Route::get('/posts', App\Http\Controllers\Api\PostController::class, 'store');
