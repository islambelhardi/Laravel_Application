<?php

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
    //to make a sign up
    Route::post('register',[AuthController::class,'register']);
    //login view
    Route::get('login',[AuthController::class,'create'])->name('login');
    //to make a login
    Route::post('login',[AuthController::class,'login'] );


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

