<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use SoftDeletes, HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'logo',
        'founded_year',
        'address',
        'city',
    ];

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function homeMatchesSchedule()
    {
        return $this->hasMany(MatchSchedule::class, 'home_team_id');
    }

    public function awayMatchesSchedule()
    {
        return $this->hasMany(MatchSchedule::class, 'away_team_id');
    }
}