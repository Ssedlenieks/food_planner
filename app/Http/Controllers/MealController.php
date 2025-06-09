<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meal;
use Illuminate\Support\Facades\Auth;

class MealController extends Controller
{
    // Public endpoint: get all meals with category
    public function indexPublic(Request $request)
    {
        $query = Meal::with('category');

        // Filter by category name if provided
        if ($request->has('category')) {
            $categoryName = $request->query('category');
            $query->whereHas('category', function ($q) use ($categoryName) {
                $q->where('name', $categoryName);
            });
        }

        // Sort by field if provided, default to 'meal_name'
        $sortBy = $request->query('sort_by', 'meal_name');
        $sortDir = $request->query('sort_dir', 'asc');

        // Validate the sort direction
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'asc';
        }

        // Optional: Only allow sorting by specific columns
        $allowedSorts = ['meal_name', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'meal_name';
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortDir);

        return response()->json($query->get());
    }


    // Public endpoint: show a specific meal by id with category
    public function showPublic($id)
    {
        $meal = Meal::with('category')->findOrFail($id);
        return response()->json($meal);
    }

    // Authenticated user: get meals created by logged-in user
    public function index()
    {
        $user = Auth::user();
        $meals = Meal::with('category')->where('user_id', $user->id)->get();
        return response()->json($meals);
    }

    // Store a new meal created by logged-in user
    public function store(Request $request)
    {
        $validated = $request->validate([
            'meal_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'meal_description' => 'required|string',
            'ingredients' => 'required|string',
            'instructions' => 'required|string',
            'meal_image' => 'nullable|url',
            'meal_source_url' => 'nullable|url',
        ]);

        $meal = Meal::create([
            'meal_name' => $validated['meal_name'],
            'category_id' => $validated['category_id'],
            'meal_description' => $validated['meal_description'], // ✅ include this!
            'ingredients' => $validated['ingredients'],
            'instructions' => $validated['instructions'],
            'meal_image' => $validated['meal_image'] ?? null,
            'meal_source_url' => $validated['meal_source_url'] ?? null,
            'user_id' => auth()->id(), // assuming you're using auth
        ]);

        return response()->json($meal, 201);
    }


    // Show one meal owned by logged-in user
    public function show(Meal $recipe)
    {
        $this->authorizeOwnership($recipe);
        return response()->json($recipe);
    }

    // Edit meal owned by logged-in user (return meal data)
    public function edit(Meal $recipe)
    {
        $this->authorizeOwnership($recipe);
        return response()->json($recipe);
    }

    // Update meal owned by logged-in user
    public function update(Request $request, Meal $recipe)
    {
        $this->authorizeOwnership($recipe);

        $validated = $request->validate([
            'meal_name' => 'required|string|max:255',
            'instructions' => 'required|string',
            'ingredients' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'meal_image' => 'nullable|string',
        ]);

        $recipe->update($validated);

        return response()->json($recipe);
    }

    // Delete meal owned by logged-in user
    public function destroy(Meal $recipe)
    {
        $this->authorizeOwnership($recipe);
        $recipe->delete();

        return response()->json(['message' => 'Recipe deleted successfully']);
    }

    // Private helper to check ownership of the meal by current user
    private function authorizeOwnership(Meal $meal)
    {
        if ($meal->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
