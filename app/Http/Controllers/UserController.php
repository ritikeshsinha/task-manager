<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Get all users (cached)
     * GET /api/users
     */
    public function index()
    {
        $users = cache()->remember('users', 60, function () {
            return User::all();
        });

        return response()->json($users);
    }

    /**
     * Store a new user
     * POST /api/users
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Clear cache after insert
        cache()->forget('users');

        return response()->json([
            'message' => 'User created successfully',
            'user'    => $user
        ], 201);
    }

    /**
     * Update user details
     * PUT /api/users/{id}
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
        ]);

        $user->update($request->only(['name', 'email']));

        // Clear cache after update
        cache()->forget('users');

        return response()->json([
            'message' => 'User updated successfully',
            'user'    => $user
        ]);
    }

    /**
     * Soft delete user
     * DELETE /api/users/{id}
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        // Clear cache after delete
        cache()->forget('users');

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
