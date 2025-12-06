<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);

        if (auth()->user()->isAdmin()) {
            $users = User::with('department')->paginate(15);
        } elseif (auth()->user()->isDepartment()) {
            $users = User::where('department_id', auth()->user()->department_id)
                ->with('department')
                ->paginate(15);
        } else {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($users);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $this->authorize('create', User::class);
        $departments = Department::all();
        return response()->json(['departments' => $departments]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:faculty,department',
            'department_id' => 'required|exists:departments,id',
        ]);

        // Department heads can only create faculty in their department
        if (auth()->user()->isDepartment()) {
            if ($validated['role'] === 'department') {
                return response()->json(['message' => 'Department heads can only create faculty users'], 403);
            }
            if ($validated['department_id'] != auth()->user()->department_id) {
                return response()->json(['message' => 'You can only create users in your department'], 403);
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'department_id' => $validated['department_id'],
        ]);

        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        return response()->json($user->load('department'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $departments = Department::all();
        return response()->json(['user' => $user, 'departments' => $departments]);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|confirmed',
            'role' => 'sometimes|in:faculty,department',
            'department_id' => 'sometimes|exists:departments,id',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
