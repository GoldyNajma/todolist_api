<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Instantiate a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['login', 'register']]);
    }

    public function register(Request $request)
    {
        $this->validate($request, User::$registrationRules);

        try {
            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->image_path = $request->input('image_path');
            $user->save();

            return response()->json([
                'user' => $user, 
                'message' => 'User registration success.',
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'User registration failed.',
            ], 409);
        }
    }

    public function login(Request $request)
    {
        $this->validate($request, User::$loginRules);
        $credentials = $request->only(['email', 'password']);

        if (($token = Auth::setTTL(null)->attempt($credentials))) {
            return $this->respondWithToken($token, Auth::user());            
        }

        return response()->json([
            'message' => 'Invalid login credentials.',
        ], 401);
    }

    public function show($id)
    {
        try {
            return response()->json([
                'user' => User::findOrFail($id),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }
    }
}
