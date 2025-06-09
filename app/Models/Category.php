<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Attiecība: kategorijai ir daudzas receptes (meals)
    public function meals()
    {
        return $this->hasMany(Meal::class);
    }
}
