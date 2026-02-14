<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('categories', [ApiController::class, 'categories']);

Route::post('user-signup', [ApiController::class, 'userSignup']);
Route::post('user-signin', [ApiController::class, 'userSignin']);

Route::middleware(['throttle:60,1'])->group(function () {
	Route::middleware('auth:sanctum')->group( function (){
		Route::post('user-signout', [ApiController::class, 'userSignOut']);
		//sliders
		Route::get('/sliders', [ApiController::class, 'sliders']);
	});
});