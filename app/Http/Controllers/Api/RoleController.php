<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Utils\ResponseUtil;
use Spatie\Permission\Models\Role;
use Exception;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($params = [])
    {
        $search = $params['search'] ?? '';
        $page = $params['page'] ?? 1;
        $perPage = $params['perPage'] ?? 10;

        $query = Role::where('name', 'like', '%' . $search . '%');
        $roles = $query->paginate($perPage, ['*'], 'page', $page);

        return ResponseUtil::noticeResponse('success', 200, $roles);
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
                'name' => 'required|string|max:255|unique:roles,name',
            ]);
            
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'sanctum'
            ]);

        } catch (Exception $e) {
            return ResponseUtil::errorResponse($e->getMessage(), 500);
        }

        return ResponseUtil::noticeResponse('success', 200, $role);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $role = Role::findOrFail($id);
        } catch (Exception $e) {
            return ResponseUtil::errorResponse('Role not found', 404);
        }

        return ResponseUtil::noticeResponse('success', 200, $role);
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
                'name' => 'required|string|max:255'
            ]);
            
            $role = Role::findOrFail($id);
            $role->name = $request->name;
            $role->save();

        } catch (Exception $e) {
            return ResponseUtil::errorResponse($e->getMessage(), 500);
        }

        return ResponseUtil::noticeResponse('success', 200, $role);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $role = Role::findOrFail($id);
            if ($role) {
                $role->delete();
            }
        } catch (Exception $e) {
            return ResponseUtil::errorResponse($e->getMessage(), 500);
        }

        return ResponseUtil::noticeResponse('success', 200, null, 'Role deleted successfully');
    }
}
