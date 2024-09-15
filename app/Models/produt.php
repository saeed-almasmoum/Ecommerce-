<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class produt extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'img',
        'price',
        'description',
    ];
    
    protected static function boot()
    {
        parent::boot();

        static::created(
            function ($product) {
                $product->code = static::max('id') + 1;
            });
    }



}
