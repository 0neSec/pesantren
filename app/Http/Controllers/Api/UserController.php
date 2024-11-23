<?php
namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return User::with('role')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:8',
            'role_id' => 'required|exists:roles,id'
        ]);

        // Prevent creating super admin unless you're a super admin
        if ($validated['role_id'] == 4 && auth()->user()->role_id != 4) {
            return response()->json(['message' => 'Cannot create super admin'], 403);
        }

        $validated['password'] = Hash::make($validated['password']);

        return User::create($validated);
    }

    public function show(User $user)
    {
        return $user->load('role');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
            'password' => 'sometimes|min:8',
            'role_id' => 'sometimes|exists:roles,id'
        ]);

        // Prevent changing to super admin unless you're a super admin
        if (isset($validated['role_id']) && $validated['role_id'] == 4 && auth()->user()->role_id != 4) {
            return response()->json(['message' => 'Cannot change role to super admin'], 403);
        }

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        return $user->load('role');
    }

    public function destroy(User $user)
    {
        // Only allow super admin to delete users
        if (auth()->user()->role_id != 4) {
            return response()->json(['message' => 'Unauthorized to delete users'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
