<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Utils\ResponseUtil;
use App\Models\Team;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        
        $query = Team::where('name', 'like', '%' . $search . '%');
        $teams = $query->paginate($perPage, ['*'], 'page', $page);

        if ($teams->isEmpty()) {
            return ResponseUtil::noticeResponse('No teams found', 404);
        }

        return ResponseUtil::noticeResponse('success', 200, $teams);
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:teams,name',
            'founded_year' => 'integer|min:1100|max:' . date('Y'),
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:400',
        ]);

        if ($validator->fails()) {
            return ResponseUtil::errorResponse($validator->errors()->first(), 422);
        }

        try {
            $team = Team::create([
                'name' => $request->name,
                'founded_year' => $request->founded_year,
                'address' => $request->address,
                'city' => $request->city,
            ]);
        } catch (\Exception $e) {
            return ResponseUtil::errorResponse($e->getMessage(), 500);
        }

        return ResponseUtil::noticeResponse('Team created successfully', 201, $team);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $team = Team::find($id);

        if (!$team) {
            return ResponseUtil::errorResponse('Team not found', 404);
        }

        return ResponseUtil::noticeResponse('success', 200, $team);
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
        $team = Team::find($id);

        if (!$team) {
            return ResponseUtil::errorResponse('Team not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|unique:teams,name,' . $id,
            'founded_year' => 'sometimes|integer|min:1100|max:' . date('Y'),
            'address' => 'sometimes|string|max:500',
            'city' => 'sometimes|string|max:400',
        ]);

        if ($validator->fails()) {
            return ResponseUtil::errorResponse($validator->errors()->first(), 422);
        }

        try {
            $team->name = $request->name;
            $team->founded_year = $request->founded_year;
            $team->address = $request->address;
            $team->city = $request->city;
            $team->save();
        } catch (\Exception $e) {
            return ResponseUtil::errorResponse($e->getMessage(), 500);
        }

        return ResponseUtil::noticeResponse('Team updated successfully', 200, $team);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $team = Team::find($id);

        if (!$team) {
            return ResponseUtil::errorResponse('Team not found', 404);
        }

        try {
            $team->delete();
        } catch (\Exception $e) {
            return ResponseUtil::errorResponse($e->getMessage(), 500);
        }

        return ResponseUtil::noticeResponse('Team deleted successfully', 200);
    }

    /**
     * Upload team logo.
     */
    public function uploadLogo(Request $request, string $id)
    {
        $team = Team::find($id);

        if (!$team) {
            return ResponseUtil::errorResponse('Team not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return ResponseUtil::errorResponse($validator->errors()->first(), 422);
        }

        try {

            if($team->logo) {
                $oldLogoPath = str_replace('/storage/', '', $team->logo);
                Storage::disk('public')->delete($oldLogoPath);
            }

            $file = $request->file('logo');
            
            $path = $file->storeAs('logos', time() . '.' . $file->getClientOriginalExtension(), 'public');
            $team->logo = Storage::url($path);
            $team->save();
        } catch (\Exception $e) {
            return ResponseUtil::errorResponse($e->getMessage(), 500);
        }

        return ResponseUtil::noticeResponse('Logo uploaded successfully', 200, ['logo' => $team->logo]);
    }

    /**
     * Restore a deleted team.
     */
    public function restore(string $id)
    {
        $team = Team::withTrashed()->find($id);

        if (!$team) {
            return ResponseUtil::errorResponse('Team not found', 404);
        }

        try {
            $team->restore();
        } catch (\Exception $e) {
            return ResponseUtil::errorResponse($e->getMessage(), 500);
        }

        return ResponseUtil::noticeResponse('Team restored successfully', 200, $team);
    }

    /**
     * Force delete a team.
     */
    public function forceDelete(string $id)
    {
        $team = Team::withTrashed()->find($id);

        if (!$team) {
            return ResponseUtil::errorResponse('Team not found', 404);
        }

        try {
            if ($team->logo) {
                $logoPath = str_replace('/storage/', '', $team->logo);
                Storage::disk('public')->delete($logoPath);
            }
            $team->forceDelete();
        } catch (\Exception $e) {
            return ResponseUtil::errorResponse($e->getMessage(), 500);
        }

        return ResponseUtil::noticeResponse('Team permanently deleted', 200);
    }

}
