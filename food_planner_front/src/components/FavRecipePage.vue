<template>
  <div>
    <NavBar @search="filterMeals" />
    <div class="fav-meals-page">
      <!-- Header Section -->
      <header class="header">
        <h1>Manas Iecienītās Maltītes</h1>
      </header>

      <!-- Favorite Meals List -->
      <section class="meals-container">
        <div 
          v-for="meal in filteredMeals" 
          :key="meal.id" 
          class="meal-card"
        >
          <img :src="meal.image" :alt="meal.name" class="meal-image" />
          <div class="meal-info">
            <h2>{{ meal.name }}</h2>
            <p>{{ meal.description }}</p>
          </div>
          <button @click="removeFavorite(meal.id)" class="remove-btn">
            Noņemt no iecienītajiem
          </button>
        </div>
      </section>

      <!-- No Favorites Message -->
      <p v-if="filteredMeals.length === 0" class="no-favorites-message">
        Jums vēl nav pievienotas iecienītās maltītes.
      </p>
    </div>
  </div>
</template>

<script>
import NavBar from "@/components/navbar.vue";

export default {
  name: "FavMealPage",
  data() {
    return {
      // Example data for favorite meals
      favoriteMeals: [
        {
          id: 1,
          name: "Cēzara salāti",
          description: "Svaigi lapu salāti ar vistas fileju un mērci.",
          image: "https://via.placeholder.com/150",
        },
        {
          id: 2,
          name: "Lazanja",
          description: "Garšīgs itāļu ēdiens ar mērci un sieru.",
          image: "https://via.placeholder.com/150",
        },
      ],
      searchQuery: "", // Search query
    };
  },
  computed: {
    filteredMeals() {
      return this.favoriteMeals.filter((meal) =>
        meal.name.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
  },
  methods: {
    removeFavorite(mealId) {
      // Remove a meal from favorites
      this.favoriteMeals = this.favoriteMeals.filter((meal) => meal.id !== mealId);
    },
    filterMeals(query) {
      this.searchQuery = query; // Update search query
    },
  },
  components: {
    NavBar,
  },
};
</script>

<style scoped>
/* Calendar container */
.fav-meals-page {
  max-width: 400px;
  margin: 20px auto;
  text-align: center;
  font-family: Arial, sans-serif;
}

/* Calendar header (month and year) */
.header {
  margin-bottom: 20px;
}

/* Meal card styles */
.meal-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  margin-bottom: 20px;
  padding: 10px;
  text-align: left;
}

.meal-image {
  width: 100%;
  height: auto;
  border-radius: 8px;
}

.meal-info {
  margin-top: 10px;
}

.remove-btn {
  background-color: #ff4d4d;
  color: white;
  border: none;
  padding: 10px;
  border-radius: 8px;
  cursor: pointer;
}

.no-favorites-message {
  color: #888;
  font-size: 16px;
}
</style>

