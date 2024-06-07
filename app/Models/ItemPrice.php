<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPrice extends Model
{
    use HasFactory;
    protected $table = 'item_price';


    protected $fillable = [
        'item_skin_id',
        'exterior_id',
        'type_id',
        'Bitskins_Value',
        'Skinport_Value',
        'Steam_Value',
    ];

    public function itemSkin()
    {
        return $this->belongsTo(ItemSkin::class);
    }
    
    public function exterior()
    {
        return $this->belongsTo(Exterior::class);
    }
}
