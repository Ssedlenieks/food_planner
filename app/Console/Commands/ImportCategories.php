<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportCategories extends Command
{
    protected $signature = 'import:categories';

    protected $description = 'Fetch categories from TheMealDB and store them in the database';

    public function handle()
    {
        $response = Http::get('https://www.themealdb.com/api/json/v1/1/categories.php');

        if ($response->successful()) {
            $categories = $response->json()['categories'];

            foreach ($categories as $category) {
                Category::updateOrCreate(
                    ['name' => $category['strCategory']],
                    ['name' => $category['strCategory']]
                );
            }

            $this->info('Categories imported successfully!');
        } else {
            $this->error('Failed to fetch categories from TheMealDB');
        }
    }
}
