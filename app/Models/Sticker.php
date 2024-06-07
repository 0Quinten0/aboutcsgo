<?php

// app/Models/Sticker.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sticker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'rarity_id', 'rarity_name', 'rarity_color',
        'tournament_event', 'tournament_team', 'type', 'market_hash_name',
        'effect', 'image'
    ];

    public function stickerCapsules()
    {
        return $this->belongsToMany(StickerCapsule::class, 'sticker_sticker_capsule');
    }
    // app/Models/Sticker.php

public function votes()
{
    return $this->hasMany(Vote::class);
}

}
