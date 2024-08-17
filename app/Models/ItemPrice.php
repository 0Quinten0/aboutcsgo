<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPrice extends Model
{
    use HasFactory;
    // protected $table = 'item_price';


    protected $fillable = [
        'item_skin_id',
        'exterior_id',
        'type_id',
        'created_at',
        'updated_at',
    ];



    public function itemSkin()
    {
        return $this->belongsTo(ItemSkin::class);
    }

    public function exterior()
    {
        return $this->belongsTo(Exterior::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function bitskinsActivePrice()
    {
        return $this->belongsTo(MarketplacePrice::class, 'bitskins_active_price_id');
    }

    public function skinportActivePrice()
    {
        return $this->belongsTo(MarketplacePrice::class, 'skinport_active_price_id');
    }

    public function steamActivePrice()
    {
        return $this->belongsTo(MarketplacePrice::class, 'steam_active_price_id');
    }
}

