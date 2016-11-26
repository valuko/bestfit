<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Filter
 */
class Filter extends Model
{
    protected $table = 'filters';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'display_name',
        'type',
        'values'
    ];

    protected $guarded = [];

        
}