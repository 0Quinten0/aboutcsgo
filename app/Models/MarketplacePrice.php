<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketplacePrice extends Model
{


    protected $fillable = [
        'item_price_id',
        'marketplace_id',
        'price',
        'active',
        'created_at',
        'updated_at',

    ];

    public function itemPrice()
    {
        return $this->belongsTo(ItemPrice::class);
    }

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }
}

