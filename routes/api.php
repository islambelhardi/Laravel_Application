<?php

use App\Http\Controllers\AgencyController;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use \App\Http\Controllers\AgencyAuthController;
use \App\Http\Controllers\AnnounceController;
use \App\Http\Controllers\FilterController;
use \App\Http\Controllers\CommentController;
use \App\Http\Controllers\LikeController;
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
//User Authentication
Route::prefix('user')->group(function (){
    //to make a sign up
    Route::post('register',[AuthController::class,'register']);
    //login view
    Route::get('login',[AuthController::class,'create'])->name('login')->middleware('auth:api');
    //to make a login
    Route::post('login',[AuthController::class,'login']);
    // to logout
    Route::get('logout',[AuthController::class,'logout'])->middleware('auth:api');
    Route::get('info',[UserController::class,'index'])->middleware('auth:api');

});
//User Authentication
//User Personal Data
Route::prefix('user/modify')->middleware('auth:api')->group(function (){
    Route::post('info',[UserController::class,'modifyinfo'])->name('updateinfo');
    Route::post('password',[UserController::class,'modifypassword']);
    Route::post('photo',[UserController::class,'modifyphoto'])->name('modifyphoto');
    Route::post('phone-number',[UserController::class,'modifyphone'])->name('modifyphone');
});
//User Personal Data
//Agency Auth Service
Route::prefix('agency')->group(function (){
    //to make a sign up
    Route::post('register',[AgencyAuthController::class,'register']);
    //login view
//   Route::get('login',[AgencyAuthController::class,'create'])->name('login');
    //to make a login
    Route::post('login',[AgencyAuthController::class,'login']);
    // to logout
    Route::get('logout',[AgencyAuthController::class,'logout'])->middleware('auth:api');
});
//Agency Auth Service
Route::prefix('agency/modify')->middleware('auth:api')->group(function (){
    Route::post('info',[AgencyController::class,'modifyinfo'])->name('modifyinfo');
    Route::post('password',[AgencyController::class,'modifypassword'])->name('modifypassword');
});
Route::prefix('announce')->group(function(){
    Route::post('create',[AnnounceController::class,'createannounce'])->middleware('auth:api');
    Route::post('modify',[AnnounceController::class,'modifyannounce'])->middleware('auth:api');
    Route::get('show',[AnnounceController::class,'showannounce']);
    Route::delete('delete',[AnnounceController::class,'deleteannounce']);
    Route::post('filter',[FilterController::class,'filterannounces']);
    Route::post('comment',[CommentController::class,'create']);
    Route::get('forrent',[AnnounceController::class,'getrent']);
    Route::get('forsell',[AnnounceController::class,'getsell']);
    Route::get('myannounces',[AnnounceController::class,'myannounces'])->middleware('auth:api');
    Route::get('all',[AnnounceController::class,'index']);
});
Route::resource('likes', LikeController::class)->only([
    'index', 'store', 'destroy'
]);
Route::get('/email/verify', function () {
        return Response('Email verification sent', 200);
    })->middleware('auth')->name('verification.notice');
    // the route that handle email verification
Route::get('/email/verify/{id}/{hash}', function (Request $request) {
        $user = User::where('id', $request->id)->first();
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }
        echo 'email verified';
    })->middleware(['signed'])->name('verification.verify');

