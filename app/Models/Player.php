<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
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
    ];
    
    public function team(){
        return $this->belongsTo(Team::class);
    }
}
