<template>
    <div>
        <NavBar @scroll-to-section="scrollToSection" />

        <!-- Login Status -->
        <div v-if="user">
            <p>Welcome, {{ user.name }}!</p>
        </div>
        <div v-else>
            <p>You are not logged in. Please log in to continue.</p>
        </div>

        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <div class="hero-text">
                    <p>#1 receptes tikai šeit</p>
                    <h2>
                        seko lidzi food <br />
                        <span class="highlight"> i mean it </span>
                    </h2>
                    <p>progrese w that food veido savu dietu </p>
                    <button class="start-btn">
                        START TODAY <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </section>

        <!-- Category List Section -->
        <section class="categories-section" v-if="categories.length > 0">
            <div class="category-header" @click="toggleCategories">
                <h2 class="categories-title">Categories</h2>
                <i class="fas fa-chevron-down" :class="{ 'rotate-180': !showCategories }"></i>
            </div>
            <transition name="fade">
                <ul v-show="showCategories" class="category-list">
                    <li v-for="category in categories" :key="category.id">
                        <router-link
                            :to="{ name: 'CategoryMeals', params: { categoryName: category.name } }"
                            class="category-link"
                        >
                            {{ category.name }}
                        </router-link>
                    </li>
                </ul>
            </transition>
        </section>

        <!-- Featured Recipes Section -->
        <section class="featured">
            <h2>Check out our recipes</h2>

            <div v-if="meals.length > 0" class="carousel-wrapper">
                <div class="arrow left" @click="scrollLeft">
                    <i class="fa-solid fa-arrow-left"></i>
                </div>

                <div class="carousel" ref="carousel">
                    <div v-for="(meal, index) in meals" :key="index" class="card">
                        <img :src="meal.meal_image" :alt="meal.meal_name || 'Recipe Image'" />
                        <div class="card-info">
                            <h3>{{ meal.meal_name || 'Untitled Recipe' }}</h3>
                            <p>{{ mealDescriptionPreview(meal.meal_description) }}</p>
                            <div class="card-actions">
                                <button @click="viewRecipe(meal.id)">View Recipe</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="arrow right" @click="scrollRight">
                    <i class="fa-solid fa-arrow-right"></i>
                </div>
            </div>

            <div v-else class="no-recipes">
                <p>No recipes available at the moment. Please try again later.</p>
                <button @click="fetchMeals" class="refresh-btn">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </section>

        <!-- Meal Plan Section -->
        <section class="meal-plan">
            <h2>Your Meal Plan</h2>
            <div v-if="meals.length > 0" class="meal-plan-grid">
                <MealCalendar />
            </div>
        </section>
    </div>
</template>

<script>
import NavBar from "@/Components/NavBar.vue";
import axios from "axios";
import MealCalendar from "@/Components/MealCalendar.vue";


export default {
    name: "HomePage",
    components: {
        NavBar,
        MealCalendar
    },
    data() {
        return {
            meals: [],
            categories: [],
            user: null,
            isLoading: false,
            error: null,
            showCategories: true,
        };
    },
    methods: {
        async fetchMeals() {
            this.isLoading = true;
            this.error = null;

            try {
                await axios.get('/sanctum/csrf-cookie');
                const response = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/public-meals`);
                this.meals = Array.isArray(response.data) ? response.data.map(meal => ({
                    id: meal.id,
                    meal_name: meal.meal_name || 'No name',
                    meal_description: meal.meal_description || 'No description',
                    meal_image: meal.meal_image || 'https://via.placeholder.com/300',
                })) : [];
            } catch (error) {
                console.error("Error fetching meals:", error);
                this.error = "Failed to load recipes.";
            } finally {
                this.isLoading = false;
            }
        },

        async fetchCategories() {
            try {
                const response = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/categories`);
                this.categories = response.data || [];
            } catch (error) {
                console.error("Error fetching categories:", error);
                this.categories = [];
            }
        },

        async fetchUser() {
            try {
                const response = await axios.get('/api/user');
                this.user = response.data || null;
            } catch {
                this.user = null;
            }
        },

        toggleCategories() {
            this.showCategories = !this.showCategories;
        },

        mealDescriptionPreview(description) {
            return description && description.length > 100
                ? `${description.substring(0, 100)}...`
                : description || 'No description available';
        },

        viewRecipe(mealId) {
            this.$router.push({ name: "view-recipe", params: { id: mealId } });
        },

        scrollToSection(sectionId) {
            const section = document.getElementById(sectionId);
            if (section) {
                window.scrollTo({ top: section.offsetTop - 70, behavior: "smooth" });
            }
        },

        scrollLeft() {
            const carousel = this.$refs.carousel;
            if (carousel) carousel.scrollBy({ left: -carousel.clientWidth, behavior: "smooth" });
        },

        scrollRight() {
            const carousel = this.$refs.carousel;
            if (carousel) carousel.scrollBy({ left: carousel.clientWidth, behavior: "smooth" });
        }
    },
    mounted() {
        this.fetchMeals();
        this.fetchCategories();
        this.fetchUser();
    }
};
</script>



<style scoped>
/* Login Status */
p {
    font-size: 18px;
    margin: 20px 0;
    color: #333;
}

/* Hero Section */
.hero {
    background: linear-gradient(to right, #3b82f6, #1e40af);
    color: white;
    text-align: center;
    padding: 60px 20px;
}
.hero-content {
    max-width: 1200px;
    margin: auto;
}
.hero-text p {
    font-size: 14px;
    margin-bottom: 10px;
}
.hero-text h2 {
    font-size: 36px;
    font-weight: bold;
    margin-bottom: 20px;
}
.hero-text .highlight {
    background-color: white;
    color: #1e40af;
    padding: 0 6px;
}
.start-btn {
    background-color: white;
    color: #1e40af;
    font-weight: bold;
    padding: 10px 20px;
    border-radius: 999px;
    border: none;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.3s ease;
}
.start-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
.start-btn i {
    margin-left: 8px;
}

/* Categories Section */
.categories-section {
    text-align: center;
    padding: 2rem;
    background-color: #f7f7f7;
    border-top: 2px solid #ddd;
    border-bottom: 2px solid #ddd;
}

.category-header {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    margin-bottom: 1rem;
}

.category-header:hover {
    opacity: 0.8;
}

.categories-title {
    font-size: 2rem;
    margin: 0;
    color: #333;
    transition: all 0.3s ease;
}

.fa-chevron-down {
    transition: transform 0.3s ease;
    color: #1e40af;
}

.rotate-180 {
    transform: rotate(180deg);
}

.category-list {
    list-style: none;
    padding: 0;
    margin: 0 auto;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 1rem;
}

.category-link {
    display: inline-block;
    padding: 0.75rem 1.25rem;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 30px;
    color: #333;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.category-link:hover {
    background-color: #4caf50;
    color: #fff;
    border-color: #4caf50;
    box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
}

/* Transition */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}

/* Featured Section */
.featured {
    padding: 50px 20px;
    text-align: center;
    background: #f9fafb;
}
.carousel-wrapper {
    position: relative;
    max-width: 1200px;
    margin: 0 auto;
}
.carousel {
    display: flex;
    overflow-x: auto;
    scroll-behavior: smooth;
    gap: 20px;
    padding: 20px 0;
    scrollbar-width: none;
}
.carousel::-webkit-scrollbar {
    display: none;
}
.card {
    flex: 0 0 auto;
    width: 280px;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}
.card:hover {
    transform: translateY(-5px);
}
.card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}
.card-info {
    padding: 15px;
}
.card-info h3 {
    margin: 0 0 8px;
    font-size: 18px;
    color: #333;
}
.card-info p {
    margin: 0 0 12px;
    font-size: 14px;
    color: #555;
    min-height: 42px;
}
.card-actions {
    display: flex;
    justify-content: flex-end;
}
.card-actions button {
    background: #3b82f6;
    border: none;
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.3s ease;
}
.card-actions button:hover {
    background: #2563eb;
}

/* Arrows */
.arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: #3b82f6;
    color: white;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    z-index: 10;
    user-select: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}
.arrow.left {
    left: -20px;
}
.arrow.right {
    right: -20px;
}
.arrow i {
    font-size: 18px;
}

/* No recipes / Refresh */
.no-recipes {
    text-align: center;
    margin-top: 20px;
    color: #666;
}
.refresh-btn {
    margin-top: 10px;
    background: #3b82f6;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.3s ease;
}
.refresh-btn:hover {
    background: #2563eb;
}
.refresh-btn i {
    margin-right: 6px;
}

/* Meal Plan Section */
.meal-plan {
    padding: 40px 20px;
    max-width: 1200px;
    margin: 0 auto;
    text-align: center;
}
.meal-plan h2 {
    font-size: 28px;
    color: #1e40af;
    margin-bottom: 20px;
}

/* Responsive */
@media (max-width: 768px) {
    .card {
        width: 220px;
    }

    .hero-text h2 {
        font-size: 28px;
    }

    .categories-title {
        font-size: 20px;
    }
}
</style>
