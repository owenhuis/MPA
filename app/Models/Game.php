<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Game extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'route', 'description'];

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }
}
