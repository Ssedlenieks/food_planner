<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Meal;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function showByName($name)
    {
        $category = Category::where('name', $name)->first();

        if (!$category) {
            abort(404, 'Category not found');
        }

        // Get and validate sorting parameters
        $sortBy = request()->input('sort_by', 'created_at');
        $sortDir = request()->input('sort_dir', 'desc');

        // Validate sortable fields to prevent SQL injection
        $validSortFields = ['created_at', 'meal_name'];
        $sortBy = in_array($sortBy, $validSortFields) ? $sortBy : 'created_at';
        $sortDir = in_array(strtolower($sortDir), ['asc', 'desc']) ? $sortDir : 'desc';

        $meals = Meal::where('category_id', $category->id)
            ->orderBy($sortBy, $sortDir)
            ->get();

        return response()->json([
            'category' => $category,
            'meals' => $meals,
        ]);
    }
}
