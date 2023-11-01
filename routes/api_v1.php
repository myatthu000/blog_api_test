<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SubscribedController;
use App\Http\Controllers\UserController;
use App\Http\Resources\UserResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

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

Route::get('/all-user',function (Request $request){
    $users = User::query()->where('role','like','user')->get();
    return $users;
});

Route::get('onePost',[PostController::class,'onePost']);

Route::group([
    'prefix' => 'sanctum',
],function (){
    Route::post('/register', [AuthController::class,'register'])->name('register');
    Route::post('/login', [AuthController::class,'login'])->name('login');
});

Route::group([
    'middleware' => ['auth:sanctum'],
],function (){

    Route::apiResource('category', CategoryController::class)->middleware(['auth:sanctum']);
    Route::apiResource('post', PostController::class)->middleware(['auth:sanctum']);

    Route::get('/user', function (Request $request) {
        return new UserResource($request->user());
    });
    Route::get('/user/{id}', function (Request $request) {
        return new UserResource(User::findOrFail(\request('id')));
    });

    Route::prefix('sanctum')->group(function (){
        Route::post('/changeInfo', [AuthController::class,'changePassword'])->name('auth.changeInfo');
        Route::delete('/logout', [AuthController::class,'logout'])->name('auth.logout');
        Route::delete('/logout/all', [AuthController::class,'logoutAll'])->name('auth.logoutAll');
        Route::post('/tokens', [AuthController::class,'tokens'])->name('auth.tokens'); //get user's token
    });

    Route::prefix('notifications')->group(function (){
        Route::get('/',[NotificationController::class,'notifications'])->name('noti.all');
        Route::get('/choose',[NotificationController::class,'optionTypes'])->name('noti.types');
        Route::get('/read/{id}',[NotificationController::class,'markAsRead'])->name('noti.read');
        Route::post('/notification-send',function (){$subscribers = Post::query()->find(7);
//    return $subscribers;
            foreach ($subscribers as $subscriber) {
                Log::debug($subscriber);
            }
        });
    });

    Route::group([
        'prefix' => 'setting'
    ],function (){
//    Route::get('/subscribed',[SubscribedController::class,'subscribed'])->name('user.subscribed');
//    Route::get('/unsubscribed',[SubscribedController::class,'unsubscribed'])->name('user.unsubscribed');
        Route::post('/option',[SubscribedController::class,'option'])->name('user.option');
    });
});

Route::group([
    'middleware' => ['auth:sanctum','band'],
    'prefix' => 'administrators'
],function (){
    Route::get('/freeze-user',[UserController::class,'freeze'])->name('user.band');
    Route::get('/unfreeze-user',[UserController::class,'unfreeze'])->name('user.unband');
    Route::post('/user-access',[UserController::class,'accessUser'])->name('user.access');

    Route::post('/role/toAdmin',[UserController::class,'toAdmin'])->name('user.admin');
    Route::post('/role/toEditor',[UserController::class,'toEditor'])->name('user.editor');
    Route::post('/role/toAuthor',[UserController::class,'toAuthor'])->name('user.author');
    Route::post('/role/toUser',[UserController::class,'toUser'])->name('user.user');

    Route::get('trash',[PostController::class,'trashes'])->name('post.trash');
    Route::delete('post/forceDelete/{post}',[PostController::class,'forceDelete'])->name('post.force');
    Route::delete('post/restore/{post}',[PostController::class,'restore'])->name('post.restore');

});
