<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    public function websites()
    {
        return $this->belongsToMany(Website::class, 'website_games');
    }
}
