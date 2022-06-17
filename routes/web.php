<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\RedisController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('redis', [RedisController::class, 'index']);

Route::get('/publish', function () {
    // ...

    Redis::publish('channel', json_encode([
        'name' => 'Tuan'
    ]));
});

Route::get('/', function () {
    return view('welcome');
});

Route::post('blogs', [BlogController::class, 'store']);

Route::get('blogs', [BlogController::class, 'index']);

Route::get('blogs/{id}', [BlogController::class, 'show']);

Route::get('blogs/{id}/expire', [BlogController::class, 'showExpire']);

Route::put('blogs/update/{id}', [BlogController::class, 'update']);

Route::delete('blogs/delete/{id}', [BlogController::class, 'destroy']);

Route::get('blogs/{id}/exists', [BlogController::class, 'checkRedis']);
