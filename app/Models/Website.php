<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    protected $fillable = ['image_url', 'name', 'rating', 'refferal_link', 'bonus_code', 'bonus_percentage', 'bonus_max'];

    public function websiteInformation()
    {
        return $this->hasMany(WebsiteInformation::class, 'website_id');
    }
    
    public function games()
    {
        return $this->belongsToMany(Game::class, 'website_games', 'website_id', 'game_id');
    }
    
    public function paymentMethods()
    {
        return $this->belongsToMany(PaymentMethod::class, 'website_payment_methods', 'website_id', 'payment_method_id');
    }
    
    
}
