import { createRouter, createWebHistory } from 'vue-router';

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('../components/HomePage.vue'),
  },
  {
    path: '/favorites',
    name: 'Favourites',
    component: () => import('../components/FavRecipePage.vue'),
  },
  {
    path: '/meal-planning',
    name : 'MealPlanning',
    component: () => import('../components/MealPlanPage.vue'),
 },
 {
   path: '/recipe/:id',
   name : 'RecipeDetail',
   component: () => import('../components/RecipePage.vue'),
 },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
