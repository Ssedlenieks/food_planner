<template>
    <div class="my-recipes-container">
        <button @click="goBack" class="back-button">
            ← Go Back
        </button>
        <h2 class="title">My Recipes</h2>

        <div v-if="recipes.length === 0" class="empty-message">
            You haven't added any recipes yet.
        </div>

        <div class="card-grid">
            <div
                v-for="recipe in recipes"
                :key="recipe.id"
                class="recipe-card"
            >
                <img
                    v-if="recipe.meal_image"
                    :src="recipe.meal_image"
                    alt="Meal Image"
                    class="recipe-image"
                />
                <div class="card-content">
                    <h3 class="recipe-title">{{ recipe.meal_name }}</h3>
                    <p class="recipe-category">
                        Category: {{ recipe.category?.name || 'Uncategorized' }}
                    </p>
                    <div class="button-group">
                        <router-link
                            :to="{ name: 'view-recipe', params: { id: recipe.id } }"
                            class="btn view-btn"
                        >
                            View
                        </router-link>
                        <router-link
                            :to="{ name: 'EditRecipe', params: { id: recipe.id } }"
                            class="btn edit-btn"
                        >
                            Edit
                        </router-link>
                        <button @click="deleteRecipe(recipe.id)" class="btn delete-btn">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'MyRecipes',
    data() {
        return {
            recipes: []
        };
    },
    methods: {
        goBack() {
            this.$router.push('/')
        },
        async deleteRecipe(id) {
            if (!confirm('Are you sure you want to delete this recipe?')) return;

            try {
                const token = localStorage.getItem('token');
                await axios.delete(`/recipes/${id}`, {
                    headers: {
                        Authorization: `Bearer ${token}`
                    }
                });
                this.recipes = this.recipes.filter(recipe => recipe.id !== id);
            } catch (error) {
                console.error('Error deleting recipe:', error);
            }
        }
    },
    async mounted() {
        const token = localStorage.getItem('token');
        if (!token) return;

        try {
            const response = await axios.get('/recipes', {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            });
            this.recipes = response.data;
        } catch (error) {
            console.error('Error fetching recipes:', error);
        }
    },
    beforeRouteEnter(to, from, next) {
        const token = localStorage.getItem('token');
        if (!token) {
            next('/login');
        } else {
            next();
        }
    }
};
</script>

<style scoped>
.my-recipes-container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 30px 20px;
    font-family: Arial, sans-serif;
    position: relative;
}

.back-button {
    background: none;
    border: none;
    color: #3490dc;
    cursor: pointer;
    font-size: 1rem;
    margin-bottom: 1rem;
    padding: 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.back-button:hover {
    text-decoration: underline;
}

.title {
    font-size: 32px;
    font-weight: bold;
    margin-bottom: 30px;
    text-align: center;
}

.empty-message {
    text-align: center;
    font-size: 18px;
    color: #666;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 6px;
}

.card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

.recipe-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    display: flex;
    flex-direction: column;
}

.recipe-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}

.recipe-image {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.card-content {
    padding: 15px 20px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.recipe-title {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 6px;
}

.recipe-category {
    font-size: 14px;
    color: #888;
    margin-bottom: 12px;
}

.button-group {
    margin-top: auto;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.btn {
    flex: 1;
    padding: 8px 12px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    transition: background-color 0.2s ease;
}

.view-btn {
    background-color: #3498db;
    color: white;
}

.edit-btn {
    background-color: #f1c40f;
    color: black;
}

.delete-btn {
    background-color: #e74c3c;
    color: white;
}

.btn:hover {
    opacity: 0.9;
}
</style>
