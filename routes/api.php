<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
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


Route::group(['prefix' => 'v1'], function () {

    //guest routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login'])->name('login');

    //auth needed routes
    Route::group(['middleware' => 'auth:sanctum'], function () {

        //posts routes
        Route::group(['prefix' => 'posts'], function () {
            Route::get('all', [PostController::class, 'getUserPosts'])->name('posts');
            Route::post('create', [PostController::class, 'create'])->name('post-create');
            Route::get('delete/{postId}', [PostController::class, 'delete'])->name('post-delete');
            Route::get('get', [PostController::class, 'getPosts'])->name('posts-get');
            Route::get('pending', [PostController::class, 'getPostsPending'])->name('post-pending');

            Route::get('view/{postId}', [PostController::class, 'viewPost'])->name('post-view');

            Route::post('approve', [PostController::class, 'approve'])->name('post-approve');
            Route::post('reject', [PostController::class, 'reject'])->name('post-reject');
        });

        //comments routes
        Route::group(['prefix' => 'comment'], function () {
            Route::post('add', [CommentsController::class, 'addComment'])->name('comment-add');
        });

        //logout
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    });

});

