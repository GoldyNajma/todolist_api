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
        $this->middleware('auth');
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
}
