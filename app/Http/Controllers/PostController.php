<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    const MESSAGE_SERVER_ERROR = 'Server error';

    /**
     * create a new post
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validation = Validator::make($request->all(), [
                'title' => 'required',
                'text' => 'required',
            ]);

            if ($validation->fails()) {
                return response()->json(['validation_errors' => $validation->errors(), 'message' => 'Post creation failed'], 422);
            }

            if (Post::store($request)) {
                return response()->json(['message' => 'Post created successfully'], 200);
            }

            return response()->json(['message' => 'Error occured while creating post'], 500);

        } catch (\Exception $exception) {
            logThis($exception);
            return response()->json(['message' => self::MESSAGE_SERVER_ERROR], 500);
        }

    }

    /**
     * Delete a post using the post id
     * @param $postId
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($postId): \Illuminate\Http\JsonResponse
    {
        try {
            if ($postId !== ''){

                if(\Auth::user()->hasRole(Config::get('app.access.role.admin'))){
                    $post = Post::find($postId);
                   $deleted =  $post->delete();
                }else{
                    $post  = \Auth::user()->posts()->find($postId);
                    $deleted = $post->delete();
                }

                if ($deleted){
                    return response()->json(['message' => 'Post deleted'], 200);
                }else{
                    return response()->json(['message' => 'Error occured while deleting post'], 500);
                }

            }else{
                return response()->json(['message' => 'Invalid post id'], 500);
            }

        } catch (\Exception $exception) {
            logThis($exception);
            return response()->json(['message' => self::MESSAGE_SERVER_ERROR], 500);
        }

    }

    /**
     * get all posts using a provided search string.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPosts(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $search = '';

            if ($request->has('search')){
                $search = $request->search;
            }
            $user = \Auth::user();
            $posts = Post::getPostsByRole(\Auth::user(),$search);

            return response()->json([
                'posts' => $posts,
                'role' => $user->getRoleNames(),
            ], 200);

        } catch (\Exception $exception) {
            logThis($exception);
            return response()->json(['message' => self::MESSAGE_SERVER_ERROR], 500);
        }
    }


    /**
     * view single post
     * @param $postId
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewPost($postId): \Illuminate\Http\JsonResponse
    {
        try {
            $post = Post::where('id', $postId)->with('comments.user')->get();
            return response()->json($post);
        } catch (\Exception $exception) {
            logThis($exception);
            return response()->json(['message' => self::MESSAGE_SERVER_ERROR], 500);
        }
    }

    /**
     * approve pending post
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function approve(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validation = Validator::make($request->all(), [
                'postId' => 'required',
            ]);

            if ($validation->fails()) {
                return response()->json(['validation_errors' => $validation->errors(), 'message' => 'Post approval failed'], 422);
            }

            if (\Auth::user()->hasRole(Config::get('app.access.role.admin'))) {

                if(Post::approve($request->postId)){
                    return response()->json(['message' => 'Post approved'], 200);
                }else{
                    return response()->json(['message' => 'Post approval failed'], 500);
                }

            } else {
                return response()->json(['message' => 'Un-Authorized action'], 401);
            }

        } catch (\Exception $exception) {
            logThis($exception);
            return response()->json(['message' => self::MESSAGE_SERVER_ERROR], 500);
        }
    }

    /**
     * reject post
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reject(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validation = Validator::make($request->all(), [
                'postId' => 'required',
            ]);

            if ($validation->fails()) {
                return response()->json(['validation_errors' => $validation->errors(), 'message' => 'Post rejection failed'], 422);
            }

            if (\Auth::user()->hasRole(Config::get('app.access.role.admin')))
            {
                Post::reject($request->postId);
                return response()->json(['message' => 'Post rejected'], 200);
            } else {
                return response()->json(['message' => 'Un-Authorized action'], 401);
            }

        } catch (\Exception $exception) {
            logThis($exception);
            return response()->json(['message' => self::MESSAGE_SERVER_ERROR], 500);
        }
    }

    /**
     * get all pending posts
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPostsPending(): \Illuminate\Http\JsonResponse
    {
        try {

            $user = \Auth::user();
            $posts = Post::getPendingPosts(\Auth::user());

            return response()->json([
                'posts' => $posts,
                'role' => $user->getRoleNames(),
            ], 200);

        } catch (\Exception $exception) {
            logThis($exception);
            return response()->json(['message' => self::MESSAGE_SERVER_ERROR], 500);
        }
    }

    /**
     * get all posts relevent to a user
     * @return \Illuminate\Http\JsonResponse
     */
    public function  getUserPosts(): \Illuminate\Http\JsonResponse
    {
        try {
            $user = \Auth::user();
            $posts = $user->posts()->with('comments')->get();

            return response()->json([
                'posts' => $posts,
            ], 200);

        } catch (\Exception $exception) {
            logThis($exception);
            return response()->json(['message' => self::MESSAGE_SERVER_ERROR], 500);
        }
    }
}
