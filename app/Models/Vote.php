<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $fillable = [
        'user_id',
        'item_skin_id',
        'sticker_id',
    ];

    public function sticker()
    {
        return $this->belongsTo(Sticker::class);
    }
}

