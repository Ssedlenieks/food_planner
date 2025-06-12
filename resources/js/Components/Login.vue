<template>
    <div class="login-container">
        <form class="login-form" @submit.prevent="loginUser">
            <h2>Login</h2>

            <input v-model="email" type="email" placeholder="Email" required />
            <input v-model="password" type="password" placeholder="Password" required />

            <button type="submit" :disabled="isLoading">
                Login <span v-if="isLoading">...</span>
            </button>

            <div v-if="error" class="error-message">
                <p>{{ error }}</p>
            </div>
        </form>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: "Login",
    data() {
        return {
            email: "",
            password: "",
            error: null,
            isLoading: false,
        };
    },
    methods: {
        async loginUser() {
            this.error = null;

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const passwordRegex = /^(?=.*\d)(?=.*[\W_]).{6,}$/;

            if (!emailRegex.test(this.email)) {
                this.error = "Invalid email format.";
                return;
            }
            if (!passwordRegex.test(this.password)) {
                this.error = "Password must be at least 6 characters long, include at least one digit and one special symbol.";
                return;
            }

            this.isLoading = true;
            try {
                await axios.get('/sanctum/csrf-cookie');

                const loginResponse = await axios.post('/login', {
                    email: this.email,
                    password: this.password
                });

                localStorage.setItem('token', loginResponse.data.token);

                const response = await axios.get('/user', {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });

                localStorage.setItem('authenticated', 'true');
                localStorage.setItem('user', JSON.stringify(response.data));

                // Redirect based on role field
                if (response.data.role === 'admin') {
                    this.$router.push('/admin');
                } else {
                    this.$router.push('/profile');
                }
            } catch (error) {
                this.error = error.response?.data?.message || "Login failed, please try again.";
                localStorage.removeItem('authenticated');
                localStorage.removeItem('token');
                localStorage.removeItem('user');
            } finally {
                this.isLoading = false;
            }
        }
    }
};
</script>

<style scoped>
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 5rem 1rem;
    background-color: #f0f4f8;
    min-height: 100vh;
}

.login-form {
    background-color: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
    display: flex;
    flex-direction: column;
}

.login-form h2 {
    margin-bottom: 1.5rem;
    text-align: center;
    color: #1e40af;
}

.login-form input {
    margin-bottom: 1rem;
    padding: 0.75rem;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.login-form button {
    background-color: #1e40af;
    color: white;
    padding: 0.75rem;
    font-size: 1rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.login-form button:hover {
    background-color: #15338c;
}

.error-message {
    color: red;
    margin-top: 0.5rem;
    text-align: center;
}
</style>
