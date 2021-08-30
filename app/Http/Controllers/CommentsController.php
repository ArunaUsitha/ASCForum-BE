<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentsController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addComment(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validation = Validator::make($request->all(), [
                'comment' => 'required',
                'postId' => 'required',
            ]);

            if ($validation->fails()) {
                return response()->json(['validation_errors' => $validation->errors(), 'message' => 'Adding comment failed'], 422);
            }

            if (Comment::store($request)) {
                return response()->json(['message' => 'Comment added successfully'], 200);
            }

            return response()->json(['message' => 'Error occured while adding the comment'], 500);

        } catch (\Exception $exception) {
            logThis($exception);
            return response()->json(['message' => 'Server error'], 500);
        }
    }
}
