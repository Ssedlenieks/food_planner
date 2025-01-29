<template>
  <ul :class="{'dark-mode': isDarkMode}">
    <li><router-link to="/"><i class="fas fa-home"></i> Home</router-link></li>
    <li><router-link to="/favorites"><i class="fas fa-heart"></i> Favorite Recipes</router-link></li>
    <li><router-link to="/meal-planning"><i class="fas fa-calendar-day"></i> Meal Planning</router-link></li>
    <li><router-link to="/recipe/:id"><i class="fas fa-info-circle"></i> Recipe Details</router-link></li>

    <!-- Search Icon and Search Bar -->
    <li class="search">
      <input type="text" id="searchbar" placeholder="Search Recipes" v-model="searchQuery" @input="search_bar" />
    </li>

    <!-- Dark Mode Toggle -->
    <li class="dark-mode-toggle" @click="toggleDarkMode">
      <i :class="isDarkMode ? 'fas fa-sun' : 'fas fa-moon'"></i>
    </li>
  </ul>
</template>

<script>
export default {
  name: "NavBar",
  data() {
    return {
      isDarkMode: false, // Controls dark mode state
      searchQuery: "", // Search query
    };
  },
  methods: {
    toggleDarkMode() {
      this.isDarkMode = !this.isDarkMode;
      document.body.classList.toggle("dark", this.isDarkMode); // Toggle global dark mode class
    },
    search_bar() {
      this.$emit("search", this.searchQuery); // Emit search event with the query
    },
  },
};
</script>

<style>
@import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css");

/* Default navbar styles */
ul {
  list-style-type: none;
  position: sticky;
  top: 0;
  margin: 0;
  padding: 0;
  overflow: hidden;
  background-color: #333;
  display: flex;
  justify-content: space-around;
  align-items: center;
  z-index: 1000; /* Add a higher z-index */
}

li {
  display: flex;
  align-items: center;
}

li a {
  display: block;
  color: white;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  font-size: 16px;
}

/* Dark mode toggle styles */
.dark-mode-toggle {
  cursor: pointer;
  color: white;
  font-size: 20px;
  padding: 10px;
}
</style>