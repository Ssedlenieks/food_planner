<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Meal;

class MealPlan extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'meal_id', 'date', 'meal_type'];

    public function recipe()
    {
        return $this->belongsTo(Meal::class, 'meal_id');
    }
}
