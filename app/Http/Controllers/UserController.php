<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) ($request->input('per_page', 3));
        $search  = $request->input('search');

        $authUser = auth()->user();
        $query = User::query()->where('user_type', '!=', 1);

        if ($authUser->user_type != 1) {
            $query->where('created_by', $authUser->id);
        }

        $query->when($search, function ($q, $search) {
            $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        });

        $users = $query->latest()->paginate($perPage)->appends(['search' => $search]);

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'mobile'    => 'required|string|max:20',
            'user_type' => 'required|in:2,3',
            'password'  => 'required|string|min:6',
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'mobile'    => $validated['mobile'],
            'user_type' => $validated['user_type'],
            'password'  => bcrypt($validated['password']),
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'User created successfully.',
            'data'    => $user,
        ], 201);
    }

    public function update(Request $request, User $user) {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => "required|email|unique:users,email,{$user->id}",
            'mobile'    => 'required|string|max:20',
            'user_type' => 'required|in:2,3',
            'password'  => 'nullable|string|min:6',
        ]);

        $updateData = [
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'mobile'   => $validated['mobile'],
            'role_id'  => $validated['user_type'],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($validated['password']);
        }

        $user->update($updateData);

        return response()->json([
            'message' => 'User updated successfully.',
            'data'    => $user->load('role'),
        ]);
    }

    public function destroy(User $user) 
    {
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully.'
        ], 200);
    }
}
