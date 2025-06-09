import axios from 'axios';

// Set base URL and enable credentials (for Sanctum cookies)
axios.defaults.baseURL = 'http://127.0.0.1:8001'; // Laravel backend
axios.defaults.withCredentials = true;

export default {
    // Login function
    async login(credentials) {
        try {
            // 1. Get CSRF token
            await axios.get('/sanctum/csrf-cookie');

            // 2. Post credentials to login
            await axios.post('/login', credentials);

            // 3. Fetch and return user data
            const response = await axios.get('/api/user');
            return { user: response.data };
        } catch (error) {
            throw new Error(error.response?.data?.message || 'Login failed');
        }
    },

    // Logout function
    async logout() {
        try {
            await axios.post('/logout');
            localStorage.removeItem('user'); // Optional: remove user info
        } catch (error) {
            throw new Error(error.response?.data?.message || 'Logout failed');
        }
    },

    // Get current user
    async getUser() {
        try {
            const response = await axios.get('/api/user');
            return response.data;
        } catch (error) {
            throw new Error('User not authenticated');
        }
    }
};
