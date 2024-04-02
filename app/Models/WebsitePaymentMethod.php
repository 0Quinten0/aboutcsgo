<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsitePaymentMethod extends Model
{

    public function paymentMethods()
{
    return $this->belongsToMany(PaymentMethod::class, 'website_payment_methods');
}

}
