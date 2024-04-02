<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteInformation extends Model
{
    protected $fillable = ['website_id', 'information'];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}
