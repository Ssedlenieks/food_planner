<template>
  <div>
    <NavBar />

    <!-- Hero Section -->
    <div class="hero-section">
      <div class="hero-content">
        <h1>Time to Plan Your Food</h1>
        <button @click="openModal" class="register-button">Register Here</button>
      </div>
      <img 
        src="https://cdn.britannica.com/36/123536-050-95CB0C6E/Variety-fruits-vegetables.jpg" 
        alt="Delicious Food" 
        class="hero-image"
      />
    </div>

    <!-- Featured Recipes Section -->
    <div class="recipe-section">
      <h2 class="section-title">✨ Featured Recipes ✨</h2>
      <div class="recipe-grid">
        <div v-for="recipe in recipes" :key="recipe.id" class="recipe-card">
          <img :src="recipe.image" :alt="recipe.title" class="recipe-image" />
          <div class="recipe-info">
            <h3>{{ recipe.title }}</h3>
            <p>{{ recipe.description }}</p>
            <button class="view-recipe-button" @click="viewRecipe(recipe)">View Recipe</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal (Popup) -->
    <div v-if="isModalOpen" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <span class="close" @click="closeModal">&times;</span>
        <h2>{{ isLogin ? "Login" : "Register" }}</h2>
        <form @submit.prevent="submitForm">
          <input type="text" v-model="username" placeholder="Enter your username" required />
          <input type="password" v-model="password" placeholder="Enter your password" required />
          <button type="submit" class="submit-button">{{ isLogin ? "Login" : "Register" }}</button>
        </form>
        <p class="toggle-text" @click="toggleLogin">{{ isLogin ? "Don't have an account? Register" : "Already have an account? Login" }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import NavBar from "@/components/navbar.vue";

export default {
  name: "HomePage",
  components: {
    NavBar,
  },
  data() {
    return {
      isModalOpen: false,
      isLogin: true,
      username: "",
      password: "",
      recipes: [
        {
          id: 1,
          title: "Spaghetti Carbonara",
          description: "A classic Italian pasta dish made with eggs, cheese, pancetta, and pepper.",
          image: "https://via.placeholder.com/150"
        },
        {
          id: 2,
          title: "Chicken Alfredo",
          description: "Creamy Alfredo sauce with tender chicken served over fettuccine pasta.",
          image: "https://via.placeholder.com/150"
        },
        // Add more recipes as needed
      ],
    };
  },
  methods: {
    openModal() {
      this.isModalOpen = true;
    },
    closeModal() {
      this.isModalOpen = false;
    },
    toggleLogin() {
      this.isLogin = !this.isLogin;
    },
    submitForm() {
      console.log(this.isLogin ? "Logging in..." : "Registering...");
      console.log("Username:", this.username, "Password:", this.password);
      this.closeModal();
    },
    viewRecipe(recipe) {
      console.log("Viewing recipe:", recipe.title);
      // Implement the logic to view the recipe details
    },
  },
};
</script>

<style scoped>
.hero-section {
  position: relative;
  text-align: center;
  color: white;
  z-index: 1; /* Ensure it has a lower z-index than the navbar */
}

.hero-image {
  width: 100%;  /* Ensure it covers the full width */
  height: 60vh; /* Adjust the height as needed (e.g., 60% of the viewport height) */
  object-fit: cover; /* Crops the image vertically while preserving its aspect ratio */
  margin: 0 auto; /* Centers the image horizontally */
}

.hero-content {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.register-button {
  background-color: #ff4d4d;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px;
}

.recipe-section {
  padding: 20px;
}

.section-title {
  text-align: center;
  margin-bottom: 20px;
}

.recipe-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  justify-content: center;
}

.recipe-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  overflow: hidden;
  width: 200px;
  text-align: center;
}

.recipe-image {
  width: 100%;
  height: auto;
}

.recipe-info {
  padding: 10px;
}

.view-recipe-button {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 5px 10px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1001; /* Ensure the modal overlay has a higher z-index */
}

.modal-content {
  background: white;
  padding: 30px;
  border-radius: 8px;
  width: 350px; /* Increased width for a more spacious layout */
  position: relative;
  z-index: 1002; /* Ensure modal content stays above overlay */
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); /* Add a soft shadow for depth */
}

.close {
  position: absolute;
  top: 10px;
  right: 10px;
  font-size: 24px;
  cursor: pointer;
  color: #333;
}

h2 {
  text-align: center;
  margin-bottom: 20px;
  color: #333;
}

input {
  width: 100%;
  padding: 12px;
  margin-bottom: 15px;
  border-radius: 6px;
  border: 1px solid #ddd;
  box-sizing: border-box;
  font-size: 16px;
}

input:focus {
  border-color: #007bff; /* Highlight the input when focused */
  outline: none;
}

.submit-button {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 12px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 16px;
  width: 100%;
  transition: background-color 0.3s ease;
}

.submit-button:hover {
  background-color: #0056b3; /* Darker blue on hover */
}

.toggle-text {
  color: #007bff;
  cursor: pointer;
  text-align: center;
  display: block;
  margin-top: 10px;
}

.toggle-text:hover {
  text-decoration: underline;
}
</style>
