<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $fillable = ['customer_id', 'product_id', 'rating', 'review_text'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function setReviewTextAttribute($value)
    {
        $this->attributes['review_text'] = $this->censorBadWords($value);
    }

    private function censorBadWords($text)
    {
        foreach (BadWords() as $word) {
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i'; // Match whole words, case-insensitive
            $replacement = str_repeat('*', strlen($word));
            $text = preg_replace($pattern, $replacement, $text);
        }

        return $text;
    }

}
