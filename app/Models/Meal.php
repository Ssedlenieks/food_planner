<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'meal_name',
        'meal_description',
        'ingredients',
        'instructions',
        'meal_image',
        'meal_source_url',
        'user_id',
        'category_id',
        // 'meal_type',
        // 'prep_time',
        // 'cook_time',
        // 'servings',
        // 'difficulty_level',
        // 'created_by'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
