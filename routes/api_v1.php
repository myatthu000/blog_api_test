<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Resources\UserResource;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewPostNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

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
    return new UserResource($request->user());
});


//Route::middleware(['auth:sanctum'])->get('/api/user',function (Request $request){
//    return new $request->user();
//});


Route::middleware(['auth:sanctum'])->post('/notification-send',function (Request $request){

    $subscribers = Post::query()->find(7);

    return $subscribers;
//    foreach ($subscribers as $subscriber) {
//        Log::debug($subscriber);
////        auth()->user()->notify(new NewPostNotification($subscriber));
//    }
//
//    dd('done');

});

Route::get('onePost',[PostController::class,'onePost']);

Route::apiResource('category', CategoryController::class)->middleware(['auth:sanctum']);

Route::apiResource('post', PostController::class)
    ->middleware(['auth:sanctum'])
;

Route::post('/sanctum/register', [AuthController::class,'register']);

Route::post('/sanctum/login', [AuthController::class,'login']);

Route::post('/sanctum/changeInfo', [AuthController::class,'changePassword'])->middleware(['auth:sanctum']);

Route::delete('/sanctum/logout', [AuthController::class,'logout']);

Route::delete('/sanctum/logout/all', [AuthController::class,'logoutAll'])->middleware(['auth:sanctum']);

Route::post('/sanctum/tokens', [AuthController::class,'tokens'])->middleware(['auth:sanctum']);
