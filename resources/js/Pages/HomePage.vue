<template>
    <div>
        <NavBar @scroll-to-section="scrollToSection" />

        <!-- Login Status -->
        <div class="status-msg" v-if="user">
            <p>Welcome, {{ user.name }}!</p>
        </div>
        <div class="status-msg" v-else>
            <p>You are not logged in. Please log in to continue.</p>
        </div>

        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <div class="hero-text">
                    <p>#1 receptes tikai šeit</p>
                    <h2>
                        seko līdzi food <br />
                        <span class="highlight"> i mean it </span>
                    </h2>
                    <p>Progresē ar savu diētu — veido savu ēdienkarti.</p>
                    <button class="start-btn" @click="scrollToSection('categories-section')">
                        START TODAY <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </section>

        <!-- Category List Section -->
        <section id="categories-section" class="categories-section" v-if="categories.length">
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

            <div v-if="meals.length" class="carousel-wrapper">
                <div class="arrow left" @click="scrollLeft">
                    <i class="fa-solid fa-arrow-left"></i>
                </div>

                <div class="carousel" ref="carousel">
                    <div v-for="(meal, index) in meals" :key="index" class="card">
                        <img :src="meal.meal_image" :alt="meal.meal_name || 'Recipe Image'" />
                        <div class="card-info">
                            <h3>{{ meal.meal_name }}</h3>
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
            <div v-if="meals.length" class="meal-plan-grid">
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
    components: { NavBar, MealCalendar },
    data() {
        return {
            meals: [],
            categories: [],
            user: null,
            showCategories: true,
            error: null,
        };
    },
    methods: {
        async fetchData() {
            try {
                await axios.get('/sanctum/csrf-cookie');

                const [mealsRes, categoriesRes, userRes] = await Promise.all([
                    axios.get(`${import.meta.env.VITE_API_BASE_URL}/public-meals`),
                    axios.get(`${import.meta.env.VITE_API_BASE_URL}/categories`),
                    axios.get("/api/user")
                ]);

                this.meals = mealsRes.data.map(meal => ({
                    id: meal.id,
                    meal_name: meal.meal_name || 'No name',
                    meal_description: meal.meal_description || 'No description',
                    meal_image: meal.meal_image || 'https://via.placeholder.com/300',
                }));

                this.categories = categoriesRes.data || [];
                this.user = userRes.data || null;

            } catch (error) {
                console.error("Error fetching data:", error);
                this.error = "Failed to load data.";
            }
        },

        toggleCategories() {
            this.showCategories = !this.showCategories;
        },

        viewRecipe(mealId) {
            this.$router.push({ name: "view-recipe", params: { id: mealId } });
        },

        scrollToSection(sectionId) {
            const section = document.getElementById(sectionId);
            if (section) {
                section.scrollIntoView({ behavior: "smooth" });
            }
        },

        scrollLeft() {
            const carousel = this.$refs.carousel;
            if (carousel) carousel.scrollBy({ left: -carousel.clientWidth, behavior: "smooth" });
        },

        scrollRight() {
            const carousel = this.$refs.carousel;
            if (carousel) carousel.scrollBy({ left: carousel.clientWidth, behavior: "smooth" });
        },

        mealDescriptionPreview(description) {
            return description?.length > 100
                ? `${description.slice(0, 100)}...`
                : description || 'No description available';
        }
    },

    mounted() {
        this.fetchData();
    }
};
</script>

<style scoped>
/* Global */
.status-msg {
    text-align: center;
    font-size: 18px;
    margin: 20px 0;
    color: #1e3a8a;
    font-weight: 500;
}

/* Hero Section */
.hero {
    background: linear-gradient(to right, #3b82f6, #1e40af);
    color: white;
    text-align: center;
    padding: 80px 20px;
}
.hero-content {
    max-width: 1100px;
    margin: auto;
}
.hero-text p {
    font-size: 16px;
    margin-bottom: 12px;
}
.hero-text h2 {
    font-size: 40px;
    font-weight: bold;
    margin-bottom: 20px;
    line-height: 1.2;
}
.highlight {
    background-color: #ffffff;
    color: #1e3a8a;
    padding: 0 8px;
    border-radius: 4px;
}
.start-btn {
    background: #ffffff;
    color: #1e3a8a;
    font-weight: bold;
    padding: 12px 24px;
    border-radius: 999px;
    border: none;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.3s ease;
}
.start-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
}
.start-btn i {
    margin-left: 8px;
}

/* Categories Section */
.categories-section {
    background-color: #f9fafb;
    padding: 2.5rem 1rem;
    border-top: 2px solid #e5e7eb;
    border-bottom: 2px solid #e5e7eb;
    text-align: center;
}
.category-header {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    transition: 0.3s ease;
}
.category-header:hover {
    opacity: 0.85;
}
.categories-title {
    font-size: 2rem;
    font-weight: 600;
    color: #1e3a8a;
}
.fa-chevron-down {
    color: #1e40af;
    transition: transform 0.3s ease;
}
.rotate-180 {
    transform: rotate(180deg);
}
.category-list {
    list-style: none;
    padding: 0;
    margin-top: 1rem;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 1rem;
}
.category-link {
    background: #fff;
    border: 2px solid #e5e7eb;
    border-radius: 30px;
    padding: 0.75rem 1.5rem;
    color: #1f2937;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s ease;
}
.category-link:hover {
    background: #4caf50;
    color: white;
    border-color: #4caf50;
    box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
}

/* Fade Animation */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}

/* Featured Recipes */
.featured {
    padding: 60px 20px;
    background: #ffffff;
    text-align: center;
}
.featured h2 {
    font-size: 28px;
    font-weight: bold;
    color: #1f2937;
    margin-bottom: 20px;
}
.carousel-wrapper {
    position: relative;
    max-width: 1200px;
    margin: auto;
}
.carousel {
    display: flex;
    overflow-x: auto;
    scroll-behavior: smooth;
    gap: 24px;
    padding: 20px 0;
}
.carousel::-webkit-scrollbar {
    display: none;
}
.card {
    flex: 0 0 auto;
    width: 280px;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
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
    padding: 16px;
}
.card-info h3 {
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
}
.card-info p {
    font-size: 14px;
    color: #4b5563;
    min-height: 42px;
    margin-top: 8px;
}
.card-actions {
    text-align: right;
    margin-top: 10px;
}
.card-actions button {
    background: #3b82f6;
    border: none;
    color: white;
    padding: 8px 14px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s ease;
}
.card-actions button:hover {
    background: #2563eb;
}

/* Arrows */
.arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 50%;
    padding: 10px;
    cursor: pointer;
    z-index: 10;
    transition: 0.2s ease;
}
.arrow:hover {
    background: #e0e7ff;
}
.arrow.left {
    left: -25px;
}
.arrow.right {
    right: -25px;
}

/* No Recipes */
.no-recipes {
    padding: 40px 0;
}
.refresh-btn {
    background: #10b981;
    color: white;
    padding: 10px 20px;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s ease;
}
.refresh-btn:hover {
    background: #059669;
}

/* Meal Plan */
.meal-plan {
    padding: 50px 20px;
    text-align: center;
    background-color: #f3f4f6;
}
.meal-plan h2 {
    font-size: 24px;
    font-weight: bold;
    color: #1f2937;
    margin-bottom: 20px;
}
.meal-plan-grid {
    max-width: 1100px;
    margin: auto;
}

</style>
