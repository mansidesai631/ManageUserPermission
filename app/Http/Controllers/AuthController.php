<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        if (!Auth::guard('web')->attempt($credentials))
            return response()->json(['message'=>'Invalid credentials'], 401);

        $request->session()->regenerate();

        return response()->json(Auth::user());
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout(); 

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out']);
    }

    public function currentUser(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(null);
        }

        $user->load('role.permissions');
        $permissions = $user->role?->permissions->pluck('name') ?? [];
        
        // Attach permissions directly to the user object
        $user->permissions = $permissions;

        return response()->json([
            'user' => $user,
            'permissions' => $permissions,
        ]);
    }
}
