<?php

// Review.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'website_id',
        'introduction',
        'what_offers_content',
        'what_offers_image',
        'faq_questions_and_answers',
        'review_pros',
        'review_cons',
        'safety_and_transparency_content',
    ];

    // Define relationship with Website model
    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}
