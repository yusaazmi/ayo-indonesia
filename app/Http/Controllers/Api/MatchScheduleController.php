<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MatchSchedule;
use Illuminate\Support\Facades\Validator;
use App\Utils\ResponseUtil;
use Exception;

class MatchScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        $date = $request->input('date', null);

        $query = MatchSchedule::query()
            ->join('teams as home', 'home.id', '=', 'match_schedules.home_team_id')
            ->join('teams as away', 'away.id', '=', 'match_schedules.away_team_id')
            ->where(function ($q) use ($search) {
                $q->where('home.name', 'like', '%' . $search . '%')
                ->orWhere('away.name', 'like', '%' . $search . '%');
            })
            ->when($date, function ($q) use ($date) {
                $q->whereDate('match_schedules.match_date', $date);
            })
            ->select('match_schedules.*')
            ->with(['homeTeam', 'awayTeam']);

        $schedules = $query->paginate($perPage, ['*'], 'page', $page);

        return ResponseUtil::noticeResponse('success', 200, $schedules);
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
            'match_date' => 'required|date',
            'match_time' => 'required|date_format:H:i',
            'home_team_id' => 'required|exists:teams,id',
            'away_team_id' => 'required|exists:teams,id',
        ]);

        if ($validator->fails()) {
            return ResponseUtil::noticeResponse($validator->errors()->first(),422);
        }
        try {
            $matchSchedule = MatchSchedule::create([
                'match_date' => $request->match_date,
                'match_time' => $request->match_time,
                'home_team_id' => $request->home_team_id,
                'away_team_id' => $request->away_team_id,
            ]);
        } catch (Exception $e) {
            return ResponseUtil::errorResponse('Failed to create match schedule: ' . $e->getMessage(), 500);
        }
        return ResponseUtil::noticeResponse('Match schedule created successfully', 201, $matchSchedule);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $matchSchedule = MatchSchedule::with(['homeTeam', 'awayTeam'])->find($id);

        if (!$matchSchedule) {
            return ResponseUtil::errorResponse('Match schedule not found', 404);
        }

        return ResponseUtil::noticeResponse('success', 200, $matchSchedule);
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
            'match_date' => 'sometimes|required|date',
            'match_time' => 'sometimes|required|date_format:H:i',
            'home_team_id' => 'sometimes|required|exists:teams,id',
            'away_team_id' => 'sometimes|required|exists:teams,id',
            'is_completed' => 'sometimes|required|in:0,1',
        ]);

        if ($validator->fails()) {
            return ResponseUtil::noticeResponse($validator->errors()->first(), 422);
        }

        $matchSchedule = MatchSchedule::find($id);
        if (!$matchSchedule) {
            return ResponseUtil::errorResponse('Match schedule not found', 404);
        }

        try {
            $matchSchedule->match_date = $request->input('match_date', $matchSchedule->match_date);
            $matchSchedule->match_time = $request->input('match_time', $matchSchedule->match_time);
            $matchSchedule->home_team_id = $request->input('home_team_id', $matchSchedule->home_team_id);
            $matchSchedule->away_team_id = $request->input('away_team_id', $matchSchedule->away_team_id);
            $matchSchedule->is_completed = $request->input('is_completed', $matchSchedule->is_completed);
        } catch (Exception $e) {
            return ResponseUtil::errorResponse('Failed to update match schedule: ' . $e->getMessage(), 500);
        }

        return ResponseUtil::noticeResponse('Match schedule updated successfully', 200, $matchSchedule);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $matchSchedule = MatchSchedule::find($id);
        if (!$matchSchedule) {
            return ResponseUtil::errorResponse('Match schedule not found', 404);
        }

        try {
            $matchSchedule->delete();
        } catch (Exception $e) {
            return ResponseUtil::errorResponse('Failed to delete match schedule: ' . $e->getMessage(), 500);
        }

        return ResponseUtil::noticeResponse('Match schedule deleted successfully', 200, null);
    }

    /**
     * Restore deleted match schedule.
     */
    public function restore(string $id)
    {
        $matchSchedule = MatchSchedule::withTrashed()->find($id);
        if (!$matchSchedule) {
            return ResponseUtil::errorResponse('Match schedule not found', 404);
        }

        try {
            $matchSchedule->restore();
        } catch (Exception $e) {
            return ResponseUtil::errorResponse('Failed to restore match schedule: ' . $e->getMessage(), 500);
        }

        return ResponseUtil::noticeResponse('Match schedule restored successfully', 200, $matchSchedule);
    }

    /**
     * Force delete match schedule.
     */
    public function forceDelete(string $id)
    {
        $matchSchedule = MatchSchedule::withTrashed()->find($id);
        if (!$matchSchedule) {
            return ResponseUtil::errorResponse('Match schedule not found', 404);
        }

        try {
            $matchSchedule->forceDelete();
        } catch (Exception $e) {
            return ResponseUtil::errorResponse('Failed to force delete match schedule: ' . $e->getMessage(), 500);
        }

        return ResponseUtil::noticeResponse('Match schedule force deleted successfully', 200, null);
    }
}