<template>
    <nav class="sticky-navbar">
        <ul class="nav-links">
            <li><router-link to="/" class="nav-button">Home</router-link></li>
            <li><router-link to="/add-recipe" class="nav-button">Add Recipe</router-link></li>
            <li><router-link to="/my-recipes" class="nav-button">My Recipes</router-link></li>

            <!-- Profile Icon if Logged In -->
            <li v-if="isLoggedIn">
                <button @click="goToProfile" class="nav-button">
                    <i class="fas fa-user"></i> Profile
                </button>
            </li>

            <!-- Login and Register Links if Not Logged In -->
            <li v-if="!isLoggedIn">
                <router-link to="/login" class="nav-button">Login</router-link>
            </li>

            <li v-if="!isLoggedIn">
                <router-link to="/register" class="nav-button">Register</router-link>
            </li>
        </ul>
    </nav>
</template>

<script>
export default {
    data() {
        return {
            isLoggedIn: false, // initially false, will be set based on token
        };
    },
    methods: {
        // Check if the user is logged in based on token
        checkLoginStatus() {
            const token = localStorage.getItem('token');
            if (token) {
                this.isLoggedIn = true;
            } else {
                this.isLoggedIn = false;
            }
        },
        goToProfile() {
            if (this.isLoggedIn) {
                this.$router.push('/profile');
            } else {
                this.$router.push('/login');
            }
        },
    },
    mounted() {
        this.checkLoginStatus(); // Check login status when component mounts
    },
};
</script>

<style scoped>
.sticky-navbar {
    position: fixed;
    top: 0;
    width: 100%;
    background-color: #1e40af;
    z-index: 1000;
    height: 70px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.nav-links {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
    list-style: none;
    margin: 0;
    padding: 0;
}

.nav-links li {
    margin: 0 1rem;
}

.nav-button {
    color: white;
    background: none;
    border: none;
    text-decoration: none;
    font-size: 1rem;
    padding: 0.5rem;
    transition: color 0.3s, transform 0.2s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.nav-button:hover {
    color: #ffeb3b;
    transform: translateY(-3px);
}

.fa-user {
    font-size: 1.2rem;
}
</style>
