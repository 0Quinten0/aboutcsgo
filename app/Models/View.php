<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_skin_id',
        'viewed_at',
    ];

    // Disable automatic timestamps
    public $timestamps = false;

    public function itemSkin()
    {
        return $this->belongsTo(ItemSkin::class);
    }
}
