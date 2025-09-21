<?php

use app\Http\Controllers\V1\User\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/search/{username}', function ($username) {
        return $username;
    })->where('username', '[a-z]+');

    Route::get('/servers', function () {
        return response('Servers', 200);
    });
});

Route::get('/servers/{year}/{slug}', function ($year, $slug) {
    return $year . ' ' . $slug;
})->where('year', '[0-9]+')->where('slug', '[a-z]+');


Route::prefix('user')->name('user.')->group(function () {
    Route::get('/profile',[UserController::class,'index'])->name('profile');
    Route::get('/profile/edit', function () {
        return view('profile-edit');
    })->name('edit');
    Route::get('invitation/{id}',[UserController::class,'invite'])->name('invitation');
});
    Route::get('/time', function () {
        return now();
    });
    Route::resource('servers', 'ServersController');
    Route::any('/{any}', function () {
        redirect('/');
    })->where('any', '.*');

