<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MealPlanController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        Log::info('Authenticated User ID: ' . $userId);

        $plans = MealPlan::with('recipe')//cd
            ->where('user_id', $userId)
            ->get();

        Log::info('MealPlans count: ' . $plans->count());

        return $plans;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'meal_id' => 'required|exists:meals,id',
            'date' => 'required|date',
            'meal_type' => 'required|in:breakfast,lunch,dinner,snack'
        ]);

        $validated['user_id'] = Auth::id();
        return MealPlan::create($validated);
    }

    public function destroy(MealPlan $mealPlan)
    {
        $this->authorize('delete', $mealPlan);
        $mealPlan->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
