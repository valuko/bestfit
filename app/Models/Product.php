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

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category', 'products_in_categories');
    }

    protected $guarded = [];

    public function buildNeededData()
    {
        return json_decode($this->data, true);
    }
        
}