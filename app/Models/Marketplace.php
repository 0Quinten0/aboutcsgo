<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marketplace extends Model



{


    protected $fillable = [
        'name',
        'created_at',
        'updated_at',
    ];


    public function prices()
    {
        return $this->hasMany(MarketplacePrice::class);
    }
}

