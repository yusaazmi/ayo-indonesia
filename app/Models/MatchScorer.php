<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatchScorer extends Model
{
    use SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'match_result_id',
        'player_id',
        'scored_at_minute',
        'is_penalty',
        'is_own_goal',
    ];
    
    public function matchResult()
    {
        return $this->belongsTo(MatchResult::class, 'match_result_id');
    }
    
    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }
}
