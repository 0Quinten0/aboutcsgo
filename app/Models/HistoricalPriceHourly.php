<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricalPriceHourly extends Model
{
    use HasFactory;

    protected $table = 'historical_prices_hourly';

    protected $fillable = [
        'item_price_id',
        'hour',
        'lowest_price',
        'avg_price',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;
}
