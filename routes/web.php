<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Backend\MoviesController;
use App\Http\Controllers\Backend\NewsController as BackendNewsController;
use App\Http\Controllers\Backend\UsersController;
use App\Http\Controllers\User\MoviesController as UserMoviesController;

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

Route::prefix('movies')->name('movie.')->group(function () {
    Route::get('/category={category}&id={id}&episode={episode}', [MovieController::class, 'getMovieEpisode'])->name('episode');
    Route::get('/category={category}&id={id}andname={name}', [MovieController::class, 'getMovie'])->name('detail');
    Route::post('/episode-ajax', [MovieController::class, 'getEpisodeAjax'])->name('episode-ajax');
    Route::post('/get-view-movie-ajax', [MovieController::class, 'getViewMovieAjax'])->name('get-view-movie-ajax');
});

Route::get('/tin-tuc-{slug}-{id}', [NewsController::class, 'getNewsDetail'])->name('news_detail');

Route::get('/storage-ajax', [StorageController::class, 'saveImage'])->name('storage-ajax');
Route::get('/storage-movie-ajax', [StorageController::class, 'saveMovie'])->name('storage-movie-ajax');
Route::get('/header-ajax', [HomeController::class, 'getHeaderAjax'])->name('header-ajax');
Route::get('/load_first_home_ajax', [HomeController::class, 'getFirstHomeAjax'])->name('load_first_home_ajax');
Route::get('/update_film', [HomeController::class, 'getUpdateFilm'])->name('update_film');
Route::get('/update_movie_id', [HomeController::class, 'updateMovieId'])->name('update_movie_id');
Route::get('/traffic', [HomeController::class, 'getTraffic'])->name('traffic');
Route::get('/update-slug-movie', [MovieController::class, 'updateSlugMovie'])->name('update_slug');


// admin

Route::get('/login', [AdminController::class, 'getLogin'])->name('login');
Route::get('/logout', [AdminController::class, 'getLogout'])->name('logout');
Route::post('/post-login', [AdminController::class, 'postLogin'])->name('post_login');
Route::post('/post_register', [AdminController::class, 'postRegister'])->name('post_register');
Route::get('/register', [AdminController::class, 'getRegister'])->name('register');


Route::prefix('admin')->middleware(['checked_user'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'getDashboard'])->name('dashboard');
    Route::get('/to-admin', [AdminController::class, 'toBeAdmin'])->name('toBeAdmin');

    Route::prefix('movies')->namespace('Backend')->name('movie.')->group(function () {
        Route::get('create', [MoviesController::class, 'create'])->name('create');
        Route::get('develop', [MoviesController::class, 'develop'])->name('develop');
        Route::get('website', [MoviesController::class, 'website'])->name('website');
        Route::get('develop/{id}', [MoviesController::class, 'developById'])->name('develop_by_id');
        Route::post('update/{id_movie}', [MoviesController::class, 'update'])->name('update');
        Route::post('search_add_movie', [MoviesController::class, 'searchAddMovie'])->name('search_add_movie');
        Route::post('search_develop_movie', [MoviesController::class, 'searchDevelopMovie'])->name('search_develop_movie');
        Route::post('get_url_edit', [MoviesController::class, 'getUrlEdit'])->name('get_url_edit');
        Route::post('view_movie', [MoviesController::class, 'viewMovie'])->name('view_movie');
        Route::post('create_view', [MoviesController::class, 'createView'])->name('create_view');
    });

    Route::prefix('users')->namespace('Backend')->name('user.')->group(function () {
        Route::get('index', [UsersController::class, 'index'])->name('index');
    });

    Route::prefix('news')->namespace('Backend')->name('news.')->group(function () {
        Route::get('list', [BackendNewsController::class, 'list'])->name('list');
        Route::get('create-news', [BackendNewsController::class, 'createNews'])->name('create_news');
        Route::get('edit-news/{id}', [BackendNewsController::class, 'editNews'])->name('edit_news');
        Route::post('store', [BackendNewsController::class, 'store'])->name('store');
        Route::post('update/{id}', [BackendNewsController::class, 'update'])->name('update');
        Route::get('destroy/{id}', [BackendNewsController::class, 'destroy'])->name('destroy');
    });
});

Route::prefix('user')->namespace('User')->name('user.')->group(function () {
    Route::get('/trang-chu/{id}', [UserMoviesController::class, 'getHome'])->name('home');
    Route::get('/phim-{name}/{id}', [UserMoviesController::class, 'getMovieByName'])->name('detail_name');
    Route::post('/update-view', [UserMoviesController::class, 'updateView'])->name('update_view');
    Route::get('/phim-{name}/tap-{episode_id}/{id}', [UserMoviesController::class, 'getMovieByNameEposode'])->name('detail_name_episode');
});


Route::get('/', [HomeController::class, 'getHomePage'])->name('home');
Route::get('/test', [HomeController::class, 'getTest'])->name('home1');
Route::get('/page={page}.{id}', [HomeController::class, 'searchMoreMovie'])->name('moremovie');
Route::get('/search={key}', [HomeController::class, 'searchMovie'])->name('search');
Route::get('/category/{id}', [HomeController::class, 'searchMovieCategory'])->name('category');
Route::post('/search_advanced', [HomeController::class, 'searchMovieAdvanced'])->name('search_advanced');
Route::post('/search_advanced_more', [HomeController::class, 'searchMovieAdvancedMore'])->name('search_advanced_more');
Route::post('/key-search', [HomeController::class, 'searchKey'])->name('key-search');
Route::post('/home-ajax', [HomeController::class, 'getHomeAjax'])->name('home-ajax');
Route::get('/phim-{name}', [MovieController::class, 'getMovieByName'])->name('detail_name');
Route::get('/phim-{name}/edit', [MovieController::class, 'getMovieEdit'])->name('edit');
Route::post('/phim-{name}/update', [MovieController::class, 'postMovieUpdate'])->name('update');
Route::get('/phim-{name}/update', [MovieController::class, 'getMovieUpdate'])->name('update');
Route::get('/phim-{name}/tap-{episode_id}', [MovieController::class, 'getMovieByNameEposode'])->name('detail_name_episode');
