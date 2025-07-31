<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Utils\ResponseUtil;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);

        $query = User::with('roles')
            ->where('name', 'like', '%' . $search . '%')
            ->orWhere('email', 'like', '%' . $search . '%');
        $users = $query->paginate($perPage, ['*'], 'page', $page);

        return ResponseUtil::noticeResponse('success', 200, $users);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
                'role_id' => 'required|exists:roles,id',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id ?? null,
            ]);

            $role = Role::where('id', $request->role_id)->firstOrFail();
            $user->assignRole($role);

            return ResponseUtil::noticeResponse('success', 200, $user);
        } catch (Exception $e) {
            return ResponseUtil::errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = User::with('roles')->findOrFail($id);

            return ResponseUtil::noticeResponse('success', 200, $user);
        } catch (Exception $e) {
            return ResponseUtil::errorResponse('User not found', 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
        try {
            $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
                'password' => 'sometimes|required|string|min:6|confirmed',
                'role_id' => 'sometimes|required|exists:roles,id',
            ]);

            $user = User::findOrFail($id);
            $user->name = $request->name ?? $user->name;
            $user->email = $request->email ?? $user->email;
            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
            }
            if ($request->has('role_id')) {
                $role = Role::where('id', $request->role_id)->firstOrFail();
                $user->syncRoles($role);
            }
            $user->save();

            return ResponseUtil::noticeResponse('success', 200, $user);
        } catch (Exception $e) {
            return ResponseUtil::errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return ResponseUtil::noticeResponse('User deleted successfully', 200);
        } catch (Exception $e) {
            return ResponseUtil::errorResponse('User not found', 404);
        }
    }

    public function restore(string $id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            $user->restore();

            return ResponseUtil::noticeResponse('User restored successfully', 200, $user);
        } catch (Exception $e) {
            return ResponseUtil::errorResponse('User not found', 404);
        }
    }

    public function forceDelete(string $id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            $user->forceDelete();

            return ResponseUtil::noticeResponse('User permanently deleted', 200);
        } catch (Exception $e) {
            return ResponseUtil::errorResponse('User not found', 404);
        }
    }
}
