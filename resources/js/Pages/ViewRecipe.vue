<template>
    <div class="recipe-container">

        <div v-if="loading" class="loading-state">
            <p>Loading recipe...</p>
        </div>

        <div v-else-if="error" class="error-state">
            <p>{{ error }}</p>
            <router-link to="/" class="back-btn">Back to Home</router-link>
        </div>

        <div v-else-if="meal" class="recipe-details">
            <h1>{{ meal.meal_name }}</h1>
            <img :src="meal.meal_image" :alt="meal.meal_name" class="recipe-image" />

            <div class="recipe-content">
                <h2>Ingredients</h2>
                <ul class="ingredients-list">
                    <li v-for="(ingredient, index) in ingredientsArray" :key="index">
                        {{ ingredient }}
                    </li>
                </ul>

                <h2>Instructions</h2>
                <div class="instructions" v-html="formattedInstructions"></div>
            </div>

            <router-link to="/" class="back-btn">Back to Home</router-link>
        </div>

        <div v-else class="no-recipe">
            <p>Recipe not found</p>
            <router-link to="/" class="back-btn">Back to Home</router-link>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "ViewRecipe",
    data() {
        return {
            meal: null,
            loading: true,
            error: null,
        };
    },
    computed: {
        formattedInstructions() {
            if (!this.meal?.meal_description) return '';
            return this.meal.meal_description
                .replace(/\r?\n/g, '<br>')
                .replace(/<br><br>/g, '</p><p>');
        },
        ingredientsArray() {
            if (!this.meal?.ingredients) return [];
            return Array.isArray(this.meal.ingredients)
                ? this.meal.ingredients
                : this.meal.ingredients.split('\n').filter(line => line.trim() !== '');
        }
    },
    async mounted() {
        try {
            const id = this.$route.params.id;

            await axios.get('/sanctum/csrf-cookie');

            const response = await axios.get(
                `${import.meta.env.VITE_API_BASE_URL}/public-meals/${id}`
            );

            if (response.data) {
                this.meal = response.data;
            } else {
                throw new Error("Recipe not found");
            }
        } catch (error) {
            console.error("Error fetching recipe:", error);
            this.error = error.response?.data?.message ||
                error.message ||
                "Failed to load recipe. Please try again later.";
        } finally {
            this.loading = false;
        }
    }
};
</script>

<style scoped>
.recipe-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
}

.loading-state,
.error-state,
.no-recipe {
    text-align: center;
    padding: 2rem;
    font-size: 1.2rem;
}

.error-state {
    color: #e53e3e;
}

.recipe-details {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.recipe-image {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 8px;
    margin: 1rem 0;
}

.recipe-content h2 {
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
    color: #2c5282;
}

.ingredients-list {
    list-style-type: disc;
    padding-left: 1.75rem;
    margin-bottom: 2rem;
    font-size: 1rem;
    line-height: 1.5;
    color: #4a5568;
}

.instructions {
    line-height: 1.6;
    font-size: 1rem;
    color: #2d3748;
    white-space: pre-line;
}

.back-btn {
    display: inline-block;
    margin-top: 2rem;
    padding: 0.5rem 1rem;
    background: #3182ce;
    color: white;
    border-radius: 4px;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.back-btn:hover {
    background: #2c5282;
}
</style>
