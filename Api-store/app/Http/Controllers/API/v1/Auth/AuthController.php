<?php

namespace App\Http\Controllers\API\v1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ✅ Register
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|min:10|max:20|regex:/^[0-9+\-\s]+$/|unique:users,phone',
            'password' => 'required|confirmed'
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('mytoken')->plainTextToken;

        return response([
            'status'  => true,
            'message' => 'User registered successfully',
            'data'    => $user,
            'token'   => $token,
        ], 201);
    }

    // ✅ Login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'status'  => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('mytoken')->plainTextToken;

        return response([
            'status'  => true,
            'message' => 'Login successful',
            'data'    => $user,
            'token'   => $token,
        ], 200);
    }

    // ✅ Logout
    public function logout()
    {
        Auth::user()->tokens()->delete();

        return response([
            'status'  => true,
            'message' => 'Logout successful',
        ], 200);
    }

    // ✅ Delete User
    public function deleteUser()
    {
        $user = Auth::user();

        if (!$user) {
            return response([
                'status'  => false,
                'message' => 'User not found',
            ], 404);
        }

        $user->delete();

        return response([
            'status'  => true,
            'message' => 'User deleted successfully',
        ], 200);
    }
}
