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

    Route::group(['middleware' => 'auth:sanctum'],function (){
        Route::get('posts',[PostController::class ,'getUserPosts'])->name('posts');
        Route::post('post-create',[PostController::class ,'create'])->name('post-create');
        Route::get('post-delete/{postId}',[PostController::class ,'delete'])->name('post-delete');
        Route::get('posts-get',[PostController::class ,'getPosts'])->name('posts-get');
        Route::get('posts-pending',[PostController::class ,'getPostsPending'])->name('post-pending');
        Route::get('post-view/{postId}',[PostController::class ,'viewPost'])->name('post-view');

        Route::post('post-approve',[PostController::class ,'approve'])->name('post-approve');
        Route::post('post-reject',[PostController::class ,'reject'])->name('post-reject');

        Route::post('comment-add',[CommentsController::class ,'addComment'])->name('comment-add');
    });

    Route::post('register',[AuthController::class,'register']);
    Route::post('login',[AuthController::class,'login'])->name('login');
    Route::post('logout',[AuthController::class,'logout'])->name('logout');
});

