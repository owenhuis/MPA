<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaderboardScore extends Model
{
    use HasFactory;

    protected $table = 'leaderboard_scores';

    protected $fillable = ['user_id', 'name', 'game', 'score'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
