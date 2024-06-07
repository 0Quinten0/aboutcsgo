<?php

// app/Models/StickerCapsule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StickerCapsule extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image'];

    public function stickers()
    {
        return $this->belongsToMany(Sticker::class, 'sticker_sticker_capsule');
    }
}
