<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Category
 */
class Category extends Model
{
    protected $table = 'categories';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'key',
        'filters'
    ];

    protected $guarded = [];

        
}