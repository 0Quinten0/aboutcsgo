<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricalPriceRaw extends Model
{
    use HasFactory;

    protected $table = 'historical_prices_raw';

    protected $fillable = [
        'item_price_id',
        'price',
        'created_at',
    ];

    public $timestamps = false;
}
