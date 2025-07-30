<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MatchResult;
use App\Models\MatchScorer;
use App\Models\Player;
use App\Models\MatchSchedule;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Utils\ResponseUtil;

class MatchResultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            'match_schedule_id' => 'required|exists:match_schedules,id',
            'home_team_score' => 'required|integer|min:0',
            'away_team_score' => 'required|integer|min:0',
            'scorers' => 'array',
            'scorers.*.player_id' => 'required|exists:players,id',
            'scorers.*.scored_at_minute' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return ResponseUtil::errorResponse($validator->errors()->first(), 422);
        }

        // Check if the match schedule exists
        $matchSchedule = MatchSchedule::find($request->match_schedule_id);
        if (!$matchSchedule) {
            return ResponseUtil::errorResponse('Match schedule not found', 404);
        }

        try {
            $totalScore = $request->home_team_score + $request->away_team_score;
            if ($totalScore != count($request->scorers)) {
                return ResponseUtil::errorResponse('Total score does not match the number of scorers', 422);
            }

            $matchResult = MatchResult::create([
                'match_schedule_id' => $request->match_schedule_id,
                'home_team_score' => $request->home_team_score,
                'away_team_score' => $request->away_team_score,
                'winning_team_id' => $request->home_team_score > $request->away_team_score ? $matchSchedule->home_team_id : ($request->home_team_score < $request->away_team_score ? $matchSchedule->away_team_id : 0),
            ]);

            foreach ($request->scorers as $scorer) {
                MatchScorer::create([
                    'match_result_id' => $matchResult->id,
                    'player_id' => $scorer['player_id'],
                    'scored_at_minute' => $scorer['scored_at_minute'],
                    'is_penalty' => $scorer['is_penalty'] ?? 0,
                    'is_own_goal' => $scorer['is_own_goal'] ?? 0,
                ]);
            }

            $matchSchedule->is_completed = 1;
            $matchSchedule->save();
            
            $matchResult->load(['matchSchedule', 'winningTeam', 'matchScorers.player']);

            return ResponseUtil::noticeResponse('Match result created successfully', 201, $matchResult);
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
            $matchResult = MatchResult::with(['matchSchedule', 'winningTeam', 'matchScorers.player'])->findOrFail($id);

            $totalWinningHomeTeam = MatchResult::where('winning_team_id', $matchResult->matchSchedule->home_team_id)->count();
            $totalWinningAwayTeam = MatchResult::where('winning_team_id', $matchResult->matchSchedule->away_team_id)->count();
            $matchResult->total_winning_home_team = $totalWinningHomeTeam;
            $matchResult->total_winning_away_team = $totalWinningAwayTeam;

            $topScorer = MatchScorer::where('match_result_id', $matchResult->id)
                ->selectRaw('player_id, COUNT(*) as total_goals')
                ->groupBy('player_id')
                ->where('is_own_goal', 0)
                ->orderByDesc('total_goals')
                ->first();
            
            if ($topScorer) {
                $topScorerPlayer = Player::find($topScorer->player_id);
                $matchResult->top_scorer = [
                    'player_id' => $topScorerPlayer->id,
                    'name' => $topScorerPlayer->name,
                    'total_goals' => $topScorer->total_goals,
                ];
            } else {
                $matchResult->top_scorer = null;
            }

            return ResponseUtil::noticeResponse('Match result retrieved successfully', 200, $matchResult);
        } catch (Exception $e) {
            return ResponseUtil::errorResponse($e->getMessage(), 404);
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
        $validator = Validator::make($request->all(), [
            'home_team_score' => 'required|integer|min:0',
            'away_team_score' => 'required|integer|min:0',
            'scorers' => 'array',
            'scorers.*.player_id' => 'required|exists:players,id',
            'scorers.*.scored_at_minute' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return ResponseUtil::errorResponse($validator->errors()->first(), 422);
        }

        try {
            $matchResult = MatchResult::findOrFail($id);
            $matchSchedule = $matchResult->matchSchedule;

            $totalScore = $request->home_team_score + $request->away_team_score;
            if ($totalScore != count($request->scorers)) {
                return ResponseUtil::errorResponse('Total score does not match the number of scorers', 422);
            }

            $matchResult->update([
                'home_team_score' => $request->home_team_score,
                'away_team_score' => $request->away_team_score,
                'winning_team_id' => $request->home_team_score > $request->away_team_score ? $matchSchedule->home_team_id : ($request->home_team_score < $request->away_team_score ? $matchSchedule->away_team_id : 0),
            ]);

            // Clear existing scorers
            MatchScorer::where('match_result_id', $matchResult->id)->delete();

            foreach ($request->scorers as $scorer) {
                MatchScorer::create([
                    'match_result_id' => $matchResult->id,
                    'player_id' => $scorer['player_id'],
                    'scored_at_minute' => $scorer['scored_at_minute'],
                    'is_penalty' => $scorer['is_penalty'] ?? 0,
                    'is_own_goal' => $scorer['is_own_goal'] ?? 0,
                ]);
            }

            $matchResult->load(['matchSchedule', 'winningTeam', 'matchScorers.player']);

            return ResponseUtil::noticeResponse('Match result updated successfully', 200, $matchResult);
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
            $matchResult = MatchResult::findOrFail($id);
            $matchResult->matchScorers()->delete();
            $matchResult->delete();

            return ResponseUtil::noticeResponse('Match result deleted successfully', 200);
        } catch (Exception $e) {
            return ResponseUtil::errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        try {
            $matchResult = MatchResult::withTrashed()->findOrFail($id);
            $matchResult->restore();

            $matchScorers = MatchScorer::withTrashed()->where('match_result_id', $id)->get();
            foreach ($matchScorers as $scorer) {
                $scorer->restore();
            }

            return ResponseUtil::noticeResponse('Match result restored successfully', 200, $matchResult);
        } catch (Exception $e) {
            return ResponseUtil::errorResponse($e->getMessage(), 500);
        }
    }
    /**
     * Force delete the specified resource from storage.
     */
    public function forceDelete(string $id)
    {
        try {
            $matchResult = MatchResult::withTrashed()->findOrFail($id);
            $matchScorers = MatchScorer::withTrashed()->where('match_result_id', $id)->get();
            foreach ($matchScorers as $scorer) {
                $scorer->forceDelete();
            }
            $matchResult->forceDelete();

            return ResponseUtil::noticeResponse('Match result permanently deleted', 200);
        } catch (Exception $e) {
            return ResponseUtil::errorResponse($e->getMessage(), 500);
        }
    }
}
