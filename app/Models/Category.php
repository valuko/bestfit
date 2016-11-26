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
        'filters',
        'parent_id',
    ];

    protected $guarded = [];

    public function products()
    {
        return $this->belongsToMany('App\Models\Products', 'products_in_categories');
    }

    public static function getParentCat($parentKey)
    {
        $cat = self::where('key',$parentKey)->first();
        if (!empty($cat)) {
            return $cat->id;
        }
        return null;
    }
        
}