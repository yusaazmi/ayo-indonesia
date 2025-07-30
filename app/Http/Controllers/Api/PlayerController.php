<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Utils\ResponseUtil;
use App\Models\Player;
use Exception;

class PlayerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);

        $query = Player::with('team')
            ->where('name', 'like', '%' . $search . '%');
        $players = $query->paginate($perPage, ['*'], 'page', $page);

        return ResponseUtil::noticeResponse('success', 200, $players);
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
            'name' => 'required|string|max:255',
            'height_cm' => 'required|integer|min:50|max:350',
            'weight_kg' => 'required|integer|min:30|max:150',
            'position' => 'required|string|max:100|in:penyerang,gelandang,bertahan,penjaga gawang',
            'player_number' => 'required|integer|min:1|unique:players,player_number',
            'team_id' => 'required|exists:teams,id',
        ]);

        if ($validator->fails()) {
            return ResponseUtil::errorResponse($validator->errors()->first(), 422);
        }

        try {
            $player = Player::create([
                'name' => $request->name,
                'height_cm' => $request->height_cm,
                'weight_kg' => $request->weight_kg,
                'position' => $request->position,
                'player_number' => $request->player_number,
                'team_id' => $request->team_id,
                'position' => $request->position,
                'age' => $request->age,
            ]);
        } catch (Exception $e) {
            return ResponseUtil::errorResponse($e->getMessage(), 500);
        }

        return ResponseUtil::noticeResponse('Player created successfully', 201, $player);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $player = Player::with('team')->find($id);

        if (!$player) {
            return ResponseUtil::errorResponse('Player not found', 404);
        }

        return ResponseUtil::noticeResponse('success', 200, $player);
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
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'team_id' => 'sometimes|required|exists:teams,id',
            'height_cm' => 'sometimes|required|integer|min:50|max:350',
            'weight_kg' => 'sometimes|required|integer|min:30|max:150',
            'player_number' => 'sometimes|required|integer|min:1|unique:players,player_number,' . $id,
            'position' => 'sometimes|required|string|max:100',
            'age' => 'sometimes|required|integer|min:16|max:40',
        ]);

        if ($validator->fails()) {
            return ResponseUtil::errorResponse($validator->errors()->first(), 422);
        }

        $player = Player::find($id);
        if (!$player) {
            return ResponseUtil::errorResponse('Player not found', 404);
        }

        try {
            $player->update($request->only(['name', 'team_id', 'position', 'age']));
        } catch (Exception $e) {
            return ResponseUtil::errorResponse($e->getMessage(), 500);
        }

        return ResponseUtil::noticeResponse('Player updated successfully', 200, $player);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $player = Player::find($id);
        if (!$player) {
            return ResponseUtil::errorResponse('Player not found', 404);
        }

        try {
            $player->delete();
        } catch (Exception $e) {
            return ResponseUtil::errorResponse($e->getMessage(), 500);
        }

        return ResponseUtil::noticeResponse('Player deleted successfully', 200);
    }
    /**
     * Restore deleted player
     */
    public function restore(string $id)
    {
        $player = Player::withTrashed()->find($id);
        if (!$player) {
            return ResponseUtil::errorResponse('Player not found', 404);
        }

        try {
            $player->restore();
        } catch (Exception $e) {
            return ResponseUtil::errorResponse($e->getMessage(), 500);
        }

        return ResponseUtil::noticeResponse('Player restored successfully', 200, $player);
    }

    /**
     * Force delete player
     */
    public function forceDelete(string $id)
    {
        $player = Player::withTrashed()->find($id);
        if (!$player) {
            return ResponseUtil::errorResponse('Player not found', 404);
        }

        try {
            $player->forceDelete();
        } catch (Exception $e) {
            return ResponseUtil::errorResponse($e->getMessage(), 500);
        }

        return ResponseUtil::noticeResponse('Player permanently deleted successfully', 200);
    }
}
