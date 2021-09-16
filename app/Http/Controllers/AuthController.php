<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * User registrations
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validation = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'email:rfc|max:50|unique:users',
                'password' => 'required|min:6|confirmed',
                'password_confirmation' => 'required',
            ]);

            if ($validation->fails()) {
                return response()->json(['validation_errors' => $validation->errors(), 'message' => 'User registration failed'], 422);
            }

            if ($user = User::store($request)) {
                $user->assignRole(\Config::get('app.access.role.user'));
                return response()->json(['message' => 'User has registered successfully'], 200);
            }

        } catch (\Exception $exception) {
            logThis($exception);
            return response()->json(['message' => 'Server error'], 500);
        }

    }


    /**
     * User login
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validation = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validation->fails()) {
                return response()->json(['validation_errors' => $validation->errors(), 'message' => 'Login failed'], 422);
            }

            if (Auth::attempt($request->only(['email', 'password']))) {
                return response()->json(Auth::user(), 200);
            } else {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

        } catch (\Exception $exception) {
            logThis($exception);
            return response()->json(['message' => 'Server error'], 500);
        }
    }

    public function logout()
    {
        try {
            Auth::logout();
        } catch (\Exception $exception) {
            logThis($exception);
            return response()->json(['message' => 'Server error'], 500);
        }
    }

}
