<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatchResult extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'match_schedule_id',
        'home_team_score',
        'away_team_score',
        'winning_team_id'
    ];
    
    public function matchSchedule()
    {
        return $this->belongsTo(MatchSchedule::class);
    }

    public function matchScorers()
    {
        return $this->hasMany(MatchScorer::class, 'match_result_id');
    }

    public function winningTeam()
    {
        return $this->belongsTo(Team::class, 'winning_team_id');
    }
}
