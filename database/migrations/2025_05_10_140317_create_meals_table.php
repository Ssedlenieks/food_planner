<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->string('meal_type')->nullable();
            $table->integer('prep_time')->nullable();
            $table->integer('cook_time')->nullable();
            $table->integer('servings')->nullable();
            $table->string('difficulty_level')->nullable();
            $table->string('created_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->dropColumn([
                'meal_type',
                'prep_time',
                'cook_time',
                'servings',
                'difficulty_level',
                'created_by'
            ]);
        });
    }
};
