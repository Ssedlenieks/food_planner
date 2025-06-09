<template>
    <div class="add-recipe-container">
        <button @click="goBack" class="back-button">
            ← Go Back
        </button>
        <h1>Add New Recipe</h1>
        <form @submit.prevent="submitRecipe">
            <!-- Meal Name -->
            <div class="form-group">
                <label for="meal_name">Meal Name*</label>
                <input
                    v-model="form.meal_name"
                    type="text"
                    id="meal_name"
                    required
                    class="form-input"
                    placeholder="Enter meal name"
                />
            </div>

            <!-- Category -->
            <div class="form-group">
                <label for="category_id">Category*</label>
                <select
                    v-model="form.category_id"
                    id="category_id"
                    required
                    class="form-input"
                >
                    <option value="" disabled>Select a category</option>
                    <option
                        v-for="category in categories"
                        :key="category.id"
                        :value="category.id"
                    >
                        {{ category.name }}
                    </option>
                </select>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="meal_description">Description*</label>
                <textarea
                    v-model="form.meal_description"
                    id="meal_description"
                    required
                    class="form-textarea"
                    placeholder="Enter meal description"
                    rows="3"
                ></textarea>
            </div>

            <!-- Ingredients -->
            <div class="form-group">
                <label for="ingredients">Ingredients*</label>
                <textarea
                    v-model="form.ingredients"
                    id="ingredients"
                    required
                    class="form-textarea"
                    placeholder="List ingredients (one per line)"
                    rows="5"
                ></textarea>
            </div>

            <!-- Instructions -->
            <div class="form-group">
                <label for="instructions">Instructions*</label>
                <textarea
                    v-model="form.instructions"
                    id="instructions"
                    required
                    class="form-textarea"
                    placeholder="Step-by-step instructions"
                    rows="7"
                ></textarea>
            </div>

            <!-- Image URL -->
            <div class="form-group">
                <label for="meal_image">Image URL</label>
                <input
                    v-model="form.meal_image"
                    type="url"
                    id="meal_image"
                    class="form-input"
                    placeholder="https://example.com/image.jpg"
                />
            </div>

            <!-- Source URL -->
            <div class="form-group">
                <label for="meal_source_url">Source URL</label>
                <input
                    v-model="form.meal_source_url"
                    type="url"
                    id="meal_source_url"
                    class="form-input"
                    placeholder="https://original-recipe-source.com"
                />
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="submit-button" :disabled="loading">
                    <span v-if="loading">
                        <i class="fas fa-spinner fa-spin"></i> Submitting...
                    </span>
                    <span v-else>
                        <i class="fas fa-plus-circle"></i> Submit Recipe
                    </span>
                </button>
                <button
                    type="button"
                    class="cancel-button"
                    @click="cancelForm"
                    :disabled="loading"
                >
                    Cancel
                </button>
            </div>

            <!-- Error Message -->
            <div v-if="error" class="error-message">
                <i class="fas fa-exclamation-circle"></i> {{ error }}
            </div>

            <!-- Success Message -->
            <div v-if="success" class="success-message">
                <i class="fas fa-check-circle"></i> Recipe submitted successfully!
            </div>
        </form>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            form: {
                meal_name: '',
                category_id: '',
                meal_description: '',
                ingredients: '',
                instructions: '',
                meal_image: '',
                meal_source_url: ''
            },
            categories: [],
            loading: false,
            error: null,
            success: false
        };
    },
    methods: {
        goBack() {
            this.$router.push('/')
        },
        async fetchCategories() {
            try {
                await axios.get('/sanctum/csrf-cookie', { withCredentials: true });
                const response = await axios.get('/categories', { withCredentials: true });
                this.categories = response.data;
            } catch (err) {
                this.error = 'Error loading categories. Please refresh the page.';
                console.error(err);
            }
        },
        async submitRecipe() {
            this.loading = true;
            this.error = null;
            this.success = false;

            try {
                await axios.get('/sanctum/csrf-cookie', { withCredentials: true });
                const response = await axios.post('/public-meals', this.form, {
                    withCredentials: true,
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                this.success = true;
                this.resetForm();

                setTimeout(() => {
                    this.$router.push('/my-recipes');
                }, 1500);
            } catch (error) {
                if (error.response && error.response.data) {
                    let message = error.response.data.message || 'Failed to submit recipe';
                    if (error.response.data.errors) {
                        message = Object.values(error.response.data.errors)
                            .flat()
                            .join('\n');
                    }
                    this.error = message;
                } else {
                    this.error = error.message;
                }
                console.error('Submission error:', error);
            } finally {
                this.loading = false;
            }
        },
        resetForm() {
            this.form = {
                meal_name: '',
                category_id: '',
                meal_description: '',
                ingredients: '',
                instructions: '',
                meal_image: '',
                meal_source_url: ''
            };
        },
        cancelForm() {
            if (confirm('Are you sure you want to cancel? Your changes will be lost.')) {
                this.$router.push('/');
            }
        }
    },
    mounted() {
        this.fetchCategories();
    }
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
}

.submit-button,
.cancel-button {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s;
}

.submit-button {
    background-color: #38c172;
    color: white;
}

.submit-button:hover:not(:disabled) {
    background-color: #2fa360;
}

.cancel-button {
    background-color: #e3342f;
    color: white;
}

.cancel-button:hover:not(:disabled) {
    background-color: #cc1f1a;
}

button:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.error-message {
    margin-top: 1.5rem;
    padding: 1rem;
    background-color: #fff5f5;
    color: #e3342f;
    border-radius: 4px;
    border-left: 4px solid #e3342f;
    white-space: pre-line;
}

.success-message {
    margin-top: 1.5rem;
    padding: 1rem;
    background-color: #f0fff4;
    color: #38c172;
    border-radius: 4px;
    border-left: 4px solid #38c172;
}

i {
    margin-right: 0.5rem;
}

.fa-spin {
    animation: fa-spin 1s infinite linear;
}

@keyframes fa-spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

select.form-input {
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1em;
    padding-right: 2.5rem;
}
</style>
