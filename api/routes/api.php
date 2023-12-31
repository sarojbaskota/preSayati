<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\FriendController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['namespace' => 'Api', 'prefix' => 'v1'], function () {
    Route::post('signin', [AuthenticationController::class, 'signin']);
    Route::post('signup', [AuthenticationController::class, 'signup']);

    Route::middleware('auth:api')->group( function () {
      Route::get('profile', [AuthenticationController::class, 'get']);
      Route::post('profile', [AuthenticationController::class, 'update']);
      //message
      Route::post('message/send', [MessageController::class, 'store']);
      Route::get('message/sent/list', [MessageController::class, 'index']);
      Route::get('message/sent/list/{id}', [MessageController::class, 'show']);

      Route::get('friends/list', [FriendController::class, 'index']);
      Route::post('friends/add', [FriendController::class, 'add']);
      Route::post('friends/confirm-request', [FriendController::class, 'confirm']);
      Route::get('friends/list/request', [FriendController::class, 'request']);
      Route::post('friends/unfriend', [FriendController::class, 'unfriend']);
      
    });
  });