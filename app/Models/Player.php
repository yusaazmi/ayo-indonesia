<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Player extends Model
{
    use SoftDeletes, HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'position',
        'team_id',
        'height_cm',
        'weight_kg',
        'player_number'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
