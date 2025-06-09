<?php

namespace App\Console\Commands;

use App\Models\Meal;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportMeals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:meals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all meals and categories from TheMealDB and store them in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // STEP 1: Fetch categories
        $categoryRes = Http::get('https://www.themealdb.com/api/json/v1/1/categories.php');
        $categories = $categoryRes->json()['categories'] ?? [];

        if (empty($categories)) {
            $this->error('Failed to fetch categories.');
            return;
        }

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['name' => $cat['strCategory']],
                [
                    'thumbnail' => $cat['strCategoryThumb'] ?? null,
                    'description' => $cat['strCategoryDescription'] ?? null,
                ]
            );
        }

        $this->info('✅ Categories imported');

        // STEP 2: Fetch meals by category
        foreach ($categories as $cat) {
            $catName = $cat['strCategory'];

            $mealListRes = Http::get("https://www.themealdb.com/api/json/v1/1/filter.php?c=" . urlencode($catName));
            $mealsList = $mealListRes->json()['meals'] ?? [];

            foreach ($mealsList as $mealSummary) {
                $mealId = $mealSummary['idMeal'];

                $detailRes = Http::get("https://www.themealdb.com/api/json/v1/1/lookup.php?i={$mealId}");
                $mealData = $detailRes->json()['meals'][0] ?? null;

                if (!$mealData) continue;

                // Ingredients list
                $ingredients = [];
                for ($i = 1; $i <= 20; $i++) {
                    $ingredient = $mealData["strIngredient{$i}"];
                    $measure = $mealData["strMeasure{$i}"];

                    if (!empty($ingredient)) {
                        $ingredients[] = trim("{$ingredient} - {$measure}");
                    }
                }

                // Find category ID from DB
                $category = Category::where('name', $mealData['strCategory'])->first();

                Meal::updateOrCreate(
                    ['meal_name' => $mealData['strMeal']],
                    [
                        'meal_description' => $mealData['strInstructions'] ?? '',
                        'ingredients' => implode("\n", $ingredients),
                        'instructions' => $mealData['strInstructions'] ?? '',
                        'meal_image' => $mealData['strMealThumb'] ?? null,
                        'meal_source_url' => $mealData['strSource'] ?? null,
                        'category_id' => $category ? $category->id : null,
                    ]
                );
            }

            $this->info("✅ Meals for category '{$catName}' imported");
        }

        $this->info('🎉 All meals and categories have been imported successfully.');
    }
}
