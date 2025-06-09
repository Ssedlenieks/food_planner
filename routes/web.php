<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MealPlanController;

// --------------------
// Auth (Sanctum)
// --------------------
// Public auth routes (no authentication required)
Route::middleware('web')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });
});

// --------------------
// Public Routes
// --------------------
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/category/{name}', [CategoryController::class, 'showByName'])->name('categories.showByName');
Route::get('/public-meals', [MealController::class, 'indexPublic']);
Route::get('/public-meals/{id}', [MealController::class, 'showPublic']);

// --------------------
// Authenticated Routes
// --------------------
Route::middleware(['auth:sanctum', 'web'])->group(function () {
    // Logout (must be protected)
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Password change
    Route::patch('/user/password', function (Request $request) {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json(['message' => 'Password updated']);
    });

    // Meals
    Route::post('/public-meals', [MealController::class, 'store']);

    // Recipes
    Route::get('/recipes', [MealController::class, 'index'])->name('recipes.index');
    Route::get('/recipes/create', [MealController::class, 'create'])->name('recipes.create');
    Route::post('/recipes', [MealController::class, 'store'])->name('recipes.store');
    Route::get('/recipes/{recipe}', [MealController::class, 'show'])->name('recipes.show');
    Route::get('/recipes/{recipe}/edit', [MealController::class, 'edit'])->name('recipes.edit');
    Route::patch('/recipes/{recipe}', [MealController::class, 'update'])->name('recipes.update');
    Route::delete('/recipes/{recipe}', [MealController::class, 'destroy'])->name('recipes.destroy');

    // Meal Plans
    Route::get('/meal-plans', [MealPlanController::class, 'index']);
    Route::post('/meal-plans', [MealPlanController::class, 'store']);
    Route::delete('/meal-plans/{mealPlan}', [MealPlanController::class, 'destroy']);
});

// --------------------
// Vue SPA Fallback
// --------------------
Route::get('/{any}', function () {
    return view('app'); // Vue SPA entry point
})->where('any', '.*');
