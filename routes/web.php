<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
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

Route::get('/', [HomeController::class, 'getHomePage'])->name('home');
Route::prefix('movies')->name('movie.')->group(function() {
    Route::get('/category={category}&id={id}&episode={episode}', [MovieController::class, 'getMovieEpisode'])->name('episode');
    Route::get('/category={category}&id={id}', [MovieController::class, 'getMovie'])->name('detail');
    Route::post('/episode-ajax', [MovieController::class, 'getEpisodeAjax'])->name('episode-ajax');
});

