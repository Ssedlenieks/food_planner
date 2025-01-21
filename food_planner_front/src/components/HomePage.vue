<template>
  <NavBar />
  <!-- Nav Bar Code -->
  <!---<ul :class="{'dark-mode': isDarkMode}">
    <li><router-link to="/"><i class="fas fa-home"></i> Home</router-link></li>
    <li><router-link to="/favorites"><i class="fas fa-heart"></i> Favorite Recipes</router-link></li>
    <li><router-link to="/meal-planning"><i class="fas fa-calendar-day"></i> Meal Planning</router-link></li>
    <li><router-link to="/recipe/:id"><i class="fas fa-info-circle"></i> Recipe Details</router-link></li>

     Search Icon and Search Bar 
    <li class="search">
      <i class="fas fa-search"></i>
      <input type="text" placeholder="Search Recipes" />
    </li>

    Dark Mode Toggle
    <li class="dark-mode-toggle" @click="toggleDarkMode">
      <i :class="isDarkMode ? 'fas fa-sun' : 'fas fa-moon'"></i>
    </li>
  </ul>-->
</template>

<script>
import NavBar from "@/components/navbar.vue";

export default {
  
  name: "Homepage",
  components: {
    NavBar,
  },
};

</script>

<style scoped>
@import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css");


</style>
