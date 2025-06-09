<template>
    <div>
        <NavBar />
        <div class="category-meals-layout">


            <!-- Main content -->
            <main class="main-content">
                <header class="category-header">
                    <h1>Meals in <span>{{ selectedCategory || 'All Categories' }}</span></h1>
                    <p class="category-description">
                        Select a category to filter meals. Use the search box to find meals by name.
                    </p>

                    <div class="filter-controls">
                        <div class="filter-group">
                            <label for="category-select">Category</label>
                            <div class="select-wrapper">
                                <select id="category-select" v-model="selectedCategory" @change="fetchMeals">
                                    <option value="">All Categories</option>
                                    <option
                                        v-for="cat in allCategories"
                                        :key="cat.id"
                                        :value="cat.name"
                                    >{{ cat.name }}</option>
                                </select>
                                <span class="select-arrow">▼</span>
                            </div>
                        </div>

                        <div class="filter-group">
                            <label for="search-input">Search Meals</label>
                            <div class="search-wrapper">
                                <input
                                    id="search-input"
                                    type="text"
                                    v-model="searchQuery"
                                    placeholder="Search by name..."
                                    @input="onSearchInput"
                                />
                                <span class="search-icon">🔍</span>
                            </div>
                        </div>

                        <div class="filter-group">
                            <label for="sort-select">Sort By</label>
                            <div class="select-wrapper">
                                <select id="sort-select" v-model="sortOption" @change="fetchMeals">
                                    <option value="created_at-desc">Newest First</option>
                                    <option value="created_at-asc">Oldest First</option>
                                    <option value="meal_name-asc">A–Z</option>
                                    <option value="meal_name-desc">Z–A</option>
                                </select>
                                <span class="select-arrow">▼</span>
                            </div>
                        </div>
                    </div>
                </header>

                <div v-if="loading" class="loading">Loading meals...</div>
                <div v-else-if="error" class="error">{{ error }}</div>
                <div v-else-if="filteredMeals.length === 0" class="empty">No meals found.</div>

                <div v-else class="meals-list">
                    <div v-for="meal in filteredMeals" :key="meal.id" class="meal-card">
                        <img
                            :src="meal.meal_image || 'https://via.placeholder.com/300'"
                            :alt="meal.meal_name"
                            class="meal-image"
                        />
                        <h2>{{ meal.meal_name || 'Untitled Meal' }}</h2>
                        <p>{{ mealDescriptionPreview(meal.meal_description) }}</p>
                        <router-link
                            :to="{ name: 'view-recipe', params: { id: meal.id } }"
                            class="view-btn"
                        >
                            View Recipe
                        </router-link>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import NavBar from "@/Components/NavBar.vue";

export default {
    name: "CategoryMeals",
    components: {NavBar},
    props: {
        categoryName: {
            type: String,
            default: null,
        },
    },
    data() {
        return {
            allCategories: [],
            selectedCategory: this.categoryName || "",
            meals: [],
            loading: false,
            error: null,
            searchQuery: "",
            sortOption: "created_at-desc", // default sorting
        };
    },
    computed: {
        filteredMeals() {
            if (!this.searchQuery.trim()) {
                return this.meals;
            }
            const query = this.searchQuery.toLowerCase();
            return this.meals.filter((meal) =>
                meal.meal_name.toLowerCase().includes(query)
            );
        },
    },
    methods: {
        async fetchCategories() {
            try {
                const res = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/categories`);
                this.allCategories = res.data || [];
            } catch {
                this.error = "Failed to load categories.";
            }
        },
        async fetchMeals() {
            this.loading = true;
            this.error = null;
            try {
                const [sort_by, sort_dir] = this.sortOption.split("-");

                const url = this.selectedCategory
                    ? `${import.meta.env.VITE_API_BASE_URL}/category/${this.selectedCategory}`
                    : `${import.meta.env.VITE_API_BASE_URL}/public-meals`;

                const res = await axios.get(url, {
                    params: { sort_by, sort_dir },
                });

                this.meals = res.data.meals || res.data || [];
            } catch {
                this.error = "Failed to load meals.";
                this.meals = [];
            } finally {
                this.loading = false;
            }
        },
        mealDescriptionPreview(description) {
            if (!description) return "No description available";
            return description.length > 150
                ? `${description.substring(0, 150)}...`
                : description;
        },
        onSearchInput() {
            // Debounced search handled reactively
        },

    },
    async mounted() {
        await this.fetchCategories();
        await this.fetchMeals();
    },
    watch: {
        categoryName(newVal) {
            this.selectedCategory = newVal || "";
            this.fetchMeals();
        },
    },
};
</script>

<style scoped>
.category-meals-layout {
    padding-top: 70px; /* Navbar height */
    display: flex;
    min-height: calc(100vh - 70px); /* Subtract navbar height */
}


.main-content {
    flex: 1;
    padding: 30px;
    max-width: 1200px;
    margin: 0 auto;
}

.category-header {
    margin-bottom: 24px;
    text-align: center;
}

.category-header h1 {
    font-size: 2.4rem;
    margin-bottom: 8px;
}

.category-header h1 span {
    color: #3b82f6;
}

.category-description {
    font-style: italic;
    color: #666;
    margin-bottom: 24px;
}

.filter-controls {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    margin-bottom: 30px;
}

.filter-group {
    flex: 1;
    min-width: 200px;
    max-width: 300px;
}

.filter-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #444;
    text-align: left;
}

.select-wrapper, .search-wrapper {
    position: relative;
}

.select-wrapper select, .search-wrapper input {
    width: 100%;
    padding: 10px 15px;
    border-radius: 8px;
    border: 1px solid #ddd;
    font-size: 1rem;
    background-color: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: border-color 0.2s, box-shadow 0.2s;
}

.select-wrapper select {
    appearance: none;
    padding-right: 35px;
}

.select-wrapper select:focus, .search-wrapper input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

.select-arrow {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: #666;
    font-size: 0.8rem;
}

.search-wrapper input {
    padding-left: 40px;
}

.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
}

.loading,
.error,
.empty {
    text-align: center;
    font-weight: 600;
    margin: 30px 0;
    color: #555;
}

.meals-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 10px;
}

.meal-card {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    background-color: white;
    box-shadow: 0 4px 12px rgb(0 0 0 / 0.08);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    cursor: pointer;
    display: flex;
    flex-direction: column;
}

.meal-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 16px rgb(0 0 0 / 0.12);
}

.meal-image {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 14px;
    user-select: none;
}

.meal-card h2 {
    font-size: 1.25rem;
    margin-bottom: 8px;
    color: #111;
}

.meal-card p {
    flex-grow: 1;
    font-size: 0.9rem;
    color: #444;
    white-space: pre-line;
}

.view-btn {
    align-self: flex-start;
    margin-top: 12px;
    padding: 8px 16px;
    background-color: #3b82f6;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: background-color 0.2s ease;
}

.view-btn:hover {
    background-color: #2563eb;
}

@media (max-width: 768px) {
    .category-meals-layout {
        flex-direction: column;
    }


    .filter-controls {
        flex-direction: column;
        align-items: stretch;
    }

    .filter-group {
        max-width: 100%;
    }
}
</style>
