import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';

// Import your components
import HomeView from '../Pages/HomePage.vue';
import RegisterView from '../Components/Register.vue';
import LoginView from '../Components/Login.vue';
import ViewRecipe from '../Pages/ViewRecipe.vue';
import AddRecipe from '../Pages/AddRecipe.vue';
import CategoryMeals from '../Pages/CategoryMeals.vue';
import EditRecipe from '../Pages/EditRecipes.vue';
import MyRecipes from '../Pages/MyRecipes.vue';

// Enable sending cookies with requests (required for Sanctum)
axios.defaults.withCredentials = true;

// Define routes
const routes = [
    {
        path: '/',
        name: 'Home',
        component: HomeView
    },
    {
        path: '/register',
        name: 'Register',
        component: RegisterView
    },
    {
        path: '/login',
        name: 'Login',
        component: LoginView
    },
    {
        path: '/recipe/:id',
        name: 'view-recipe',
        component: ViewRecipe,
        props: true
    },
    {
        path: '/add-recipe',
        name: 'AddRecipe',
        component: AddRecipe,
        meta: { requiresAuth: true }
    },
    {
        path: '/my-recipes',
        name: 'MyRecipes',
        component: MyRecipes,
        meta: { requiresAuth: true }
    },
    {
        path: '/category/:categoryName',
        name: 'CategoryMeals',
        component: CategoryMeals,
        props: true
    },
    {
        path: '/edit-recipe/:id',
        name: 'EditRecipe',
        component: EditRecipe,
        props: true,
        meta: { requiresAuth: true }
    },
    {
        path: '/profile',
        name: 'Profile',
        component: () => import('../Pages/Profile.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/mealplan',
        name: 'MealPlan',
        component: () => import('../Pages/MealPlan.vue')
    }

];

// Create router instance
const router = createRouter({
    history: createWebHistory(),
    routes
});

// Global navigation guard for auth-protected routes
router.beforeEach(async (to, from, next) => {
    if (to.meta.requiresAuth) {
        try {
            const isAuthenticated = localStorage.getItem('authenticated') === 'true';
            if (!isAuthenticated) throw new Error('Not authenticated');

            // Check session with Laravel backend
            await axios.get('/api/user');
            next(); // User is logged in
        } catch (error) {
            // If failed, clear local data and redirect to login
            localStorage.removeItem('authenticated');
            localStorage.removeItem('user');
            next('/login');
        }
    } else {
        next(); // Route doesn't require auth
    }
});

export default router;
