<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemSkin extends Model
{
    use HasFactory;

    protected $table = 'item_skin';

    protected $fillable = [
        'item_id',
        'skin_id',
        'quality_id',
        'stattrak',
        'souvenir',
        'description',
        'image_url',
    ];

    public function collections()
    {
        return $this->belongsToMany(Collection::class);
    }
    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_item_skin');
    }
    public function crates()
    {
        return $this->belongsToMany(Crate::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function skin()
    {
        return $this->belongsTo(Skin::class);
    }

    public function quality()
    {
        return $this->belongsTo(Quality::class);
    }
}
