<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class QuestionSession
 */
class QuestionSession extends Model
{
    protected $table = 'question_sessions';

    public $timestamps = true;

    const CAT_ID = 1;

    protected $fillable = [
        'session_id',
        'step',
        'is_completed',
        'category',
    ];

    protected $guarded = [];


    public static function startNew($category)
    {
        $sessId = str_random(8);
        // Check if the key exists in the DB
        $model = static::where('session_id', $sessId)->where('category', $category)->first();
        if (!empty($model)) {
            // return current one
            return $model;
        }

        $model = new self([
            'session_id' => $sessId, 'step' => 1, 'is_completed' => false, 'category' => $category,
        ]);
        $model->save();
        return $model;
    }
        
}