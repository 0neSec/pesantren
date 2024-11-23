<?php
namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except('index', 'show');
        $this->middleware('role:3,4')->only('store', 'update', 'destroy');
    }

    public function index()
    {
        return Role::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:roles|max:255',
            'description' => 'nullable|max:500'
        ]);

        return Role::create($validated);
    }

    public function show(Role $role)
    {
        return $role;
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'sometimes|unique:roles|max:255',
            'description' => 'nullable|max:500'
        ]);

        $role->update($validated);
        return $role;
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json(['message' => 'Role deleted successfully']);
    }
}
