<template>
    <div class="register-container">
        <form class="register-form" @submit.prevent="register">
            <h2>Create an Account</h2>

            <input v-model="name" placeholder="Name" />
            <input v-model="email" type="email" placeholder="Email" />

            <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Password"
            />
            <input
                v-model="password_confirmation"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Confirm Password"
            />

            <label class="show-password">
                <input type="checkbox" v-model="showPassword" />
                Show Password
            </label>

            <button type="submit">Register</button>
            <p class="error" v-if="error">{{ error }}</p>
        </form>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from '../axios.js' // Make sure this axios instance sets Authorization if token exists
import { useRouter } from 'vue-router'

const router = useRouter()

const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const showPassword = ref(false)
const error = ref('')

const register = async () => {
    // Regex definitions with comments:
    const nameRegex = /^[a-zA-Z]{2,15}$/; // 2-15 letters
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // simple email
    const passwordRegex = /^(?=.*\d)(?=.*[\W_]).{6,}$/; // min 6 chars, one digit, one special char

    // Frontend validation
    if (!nameRegex.test(name.value)) {
        error.value = "Name must be 2-15 letters only.";
        return;
    }
    if (!emailRegex.test(email.value)) {
        error.value = "Invalid email format.";
        return;
    }
    if (!passwordRegex.test(password.value)) {
        error.value = "Password must be at least 6 characters long, include at least one digit and one special symbol.";
        return;
    }
    if (password.value !== password_confirmation.value) {
        error.value = "Passwords do not match.";
        return;
    }

    try {
        await axios.get('/sanctum/csrf-cookie');

        const response = await axios.post('/register', {
            name: name.value,
            email: email.value,
            password: password.value,
            password_confirmation: password_confirmation.value
        });

        // Save token and user data
        localStorage.setItem('token', response.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.user));

        // Set token for future requests
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;

        // Redirect to home or dashboard
        router.push('/');
    } catch (err) {
        error.value = err.response?.data?.message || 'Error occurred';
    }
};
</script>

<style scoped>
.register-container {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 5rem 1rem;
    background-color: #f0f4f8;
    min-height: 100vh;
}

.register-form {
    background-color: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
    display: flex;
    flex-direction: column;
}

.register-form h2 {
    margin-bottom: 1.5rem;
    text-align: center;
    color: #1e40af;
}

.register-form input {
    margin-bottom: 1rem;
    padding: 0.75rem;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.register-form button {
    background-color: #1e40af;
    color: white;
    padding: 0.75rem;
    font-size: 1rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.register-form button:hover {
    background-color: #15338c;
}

.show-password {
    font-size: 0.9rem;
    color: #333;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.error {
    color: red;
    margin-top: 0.5rem;
    text-align: center;
}
</style>
