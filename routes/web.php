<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\StorageController;
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
Route::get('/test', [HomeController::class, 'getTest'])->name('home1');
Route::get('/page={page}.{id}', [HomeController::class, 'searchMoreMovie'])->name('moremovie');
Route::get('/search={key}', [HomeController::class, 'searchMovie'])->name('search');
Route::get('/category/{id}', [HomeController::class, 'searchMovieCategory'])->name('category');
Route::post('/search_advanced', [HomeController::class, 'searchMovieAdvanced'])->name('search_advanced');
Route::post('/search_advanced_more', [HomeController::class, 'searchMovieAdvancedMore'])->name('search_advanced_more');
Route::post('/key-search', [HomeController::class, 'searchKey'])->name('key-search');
Route::post('/home-ajax', [HomeController::class, 'getHomeAjax'])->name('home-ajax');

Route::prefix('movies')->name('movie.')->group(function() {
    Route::get('/phim-{name}', [MovieController::class, 'getMovieByName'])->name('detail_name');
    Route::get('/category={category}&id={id}&episode={episode}', [MovieController::class, 'getMovieEpisode'])->name('episode');
    Route::get('/category={category}&id={id}', [MovieController::class, 'getMovie'])->name('detail');
    Route::post('/episode-ajax', [MovieController::class, 'getEpisodeAjax'])->name('episode-ajax');
});

Route::get('/storage-ajax', [StorageController::class, 'saveImage'])->name('storage-ajax');
Route::get('/storage-movie-ajax', [StorageController::class, 'saveMovie'])->name('storage-movie-ajax');

