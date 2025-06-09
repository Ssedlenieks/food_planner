<template>
    <div class="add-recipe-container">
        <h1>Edit Your Recipe</h1>

        <form @submit.prevent="updateRecipe" class="form-grid">
            <div class="left-column">
                <div class="form-group">
                    <label for="meal_name">Meal Title*</label>
                    <input
                        v-model="meal.meal_name"
                        type="text"
                        id="meal_name"
                        required
                        class="form-input"
                    />
                </div>

                <div class="form-group">
                    <label for="meal_description">Description*</label>
                    <textarea
                        v-model="meal.meal_description"
                        id="meal_description"
                        required
                        class="form-textarea"
                        rows="3"
                    ></textarea>
                </div>

                <div class="form-group image-url-group">
                    <label for="meal_image">Image URL</label>
                    <div class="image-url-row">
                        <input
                            v-model="meal.meal_image"
                            type="url"
                            id="meal_image"
                            class="form-input"
                        />
                        <div v-if="meal.meal_image" class="inline-image-preview">
                            <img :src="meal.meal_image" alt="Meal Preview" />
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select v-model="meal.category_id" id="category_id" class="form-input">
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="right-column">
                <div class="form-group">
                    <label for="ingredients">Ingredients*</label>
                    <textarea
                        v-model="meal.ingredients"
                        id="ingredients"
                        required
                        class="form-textarea"
                        rows="5"
                    ></textarea>
                </div>

                <div class="form-group">
                    <label for="instructions">Instructions*</label>
                    <textarea
                        v-model="meal.instructions"
                        id="instructions"
                        required
                        class="form-textarea"
                        rows="7"
                    ></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="submit-button">Save Changes</button>
            </div>

            <p v-if="message" class="success-message">{{ message }}</p>
            <p v-if="error" class="error-message">{{ error }}</p>
        </form>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "EditRecipe",
    data() {
        return {
            meal: {
                meal_name: "",
                meal_description: "",
                ingredients: "",
                instructions: "",
                meal_image: "",
                category_id: null,
            },
            categories: [],
            error: null,
            message: null,
        };
    },
    async mounted() {
        const id = this.$route.params.id;
        if (!id) {
            this.error = "Invalid recipe ID.";
            return;
        }

        try {
            const categoryRes = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/categories`);
            this.categories = categoryRes.data;

            const { data } = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/recipes/${id}`);

            this.meal = {
                meal_name: data.meal_name,
                meal_description: data.meal_description,
                ingredients: Array.isArray(data.ingredients)
                    ? data.ingredients.join("\n")
                    : data.ingredients,
                instructions: data.instructions,
                meal_image: data.meal_image,
                category_id: data.category_id,
            };
        } catch (err) {
            this.error = err.response?.data?.message || "Failed to load recipe.";
        }
    },
    methods: {
        async updateRecipe() {
            const id = this.$route.params.id;
            try {
                const payload = {
                    ...this.meal,
                    ingredients: this.meal.ingredients.trim(),
                };

                await axios.patch(`${import.meta.env.VITE_API_BASE_URL}/recipes/${id}`, payload);

                this.message = "Recipe updated successfully!";
                setTimeout(() => {
                    this.$router.push("/my-recipes");
                }, 1500);
            } catch (err) {
                this.error = err.response?.data?.message || "Failed to update recipe.";
            }
        },
    },
};
</script>

<style scoped>
.add-recipe-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #2c3e50;
}

.form-input,
.form-textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.form-input:focus,
.form-textarea:focus {
    outline: none;
    border-color: #3490dc;
    box-shadow: 0 0 0 3px rgba(52, 144, 220, 0.1);
}

.form-textarea {
    min-height: 100px;
    resize: vertical;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    justify-content: center;
}

.submit-button {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s;
    background-color: #38c172;
    color: white;
}

.submit-button:hover {
    background-color: #2fa360;
}

.error-message {
    margin-top: 1.5rem;
    padding: 1rem;
    background-color: #fff5f5;
    color: #e3342f;
    border-radius: 4px;
    border-left: 4px solid #e3342f;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.success-message {
    margin-top: 1.5rem;
    padding: 1rem;
    background-color: #f0fff4;
    color: #38c172;
    border-radius: 4px;
    border-left: 4px solid #38c172;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.image-url-group .image-url-row {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.inline-image-preview {
    width: 100px;
    height: 100px;
    overflow: hidden;
    border-radius: 8px;
    border: 1px solid #ddd;
    box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
    flex-shrink: 0;
}

.inline-image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.form-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
}

.left-column,
.right-column {
    flex: 1 1 300px;
}

@media (max-width: 768px) {
    .form-grid {
        flex-direction: column;
    }
}
</style>
