<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'position',
        'number',
        'team_id',
        'height_cm',
        'weight_kg',
        'player_number'
    ];

    public function team(){
        return $this->belongsTo(Team::class);
    }
}
