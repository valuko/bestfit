<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
 */
class Product extends Model
{
    protected $table = 'products';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'zalando_id',
        'season',
        'color',
        'ageGroup',
        'data'
    ];

    protected $guarded = [];

        
}