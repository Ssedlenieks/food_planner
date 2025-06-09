<template>
    <div class="profile-container">
        <button @click="goBack" class="back-button">← Go Back</button>

        <h2 class="profile-title">My Profile</h2>

        <p v-if="profileMessage" :class="profileSuccess ? 'success-message' : 'error-message'">
            {{ profileMessage }}
        </p>

        <form @submit.prevent="updateProfile">
            <div class="form-group">
                <label>Name:</label>
                <input v-model="form.name" class="form-input" />
                <small v-if="nameError" class="error-message">{{ nameError }}</small>
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input v-model="form.email" type="email" class="form-input" />
                <small v-if="emailError" class="error-message">{{ emailError }}</small>
            </div>

            <button class="button-blue" type="submit" :disabled="isUpdating">
                {{ isUpdating ? 'Updating...' : 'Update Profile' }}
            </button>
        </form>

        <hr class="separator" />

        <p v-if="passwordMessage" :class="passwordSuccess ? 'success-message' : 'error-message'">
            {{ passwordMessage }}
        </p>

        <form @submit.prevent="changePassword">
            <h3 class="password-title">Change Password</h3>

            <div class="form-group">
                <label>Current Password:</label>
                <input v-model="passwordForm.current_password" type="password" class="form-input" required />
            </div>

            <div class="form-group">
                <label>New Password:</label>
                <input v-model="passwordForm.password" type="password" class="form-input" required />
                <small v-if="passwordError" class="error-message">{{ passwordError }}</small>
            </div>

            <div class="form-group">
                <label>Confirm New Password:</label>
                <input v-model="passwordForm.password_confirmation" type="password" class="form-input" required />
            </div>

            <button class="button-green" type="submit" :disabled="isChangingPassword">
                {{ isChangingPassword ? 'Updating...' : 'Change Password' }}
            </button>
        </form>

        <hr class="separator" />

        <button class="button-red" @click="logout">Logout</button>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const form = ref({ name: '', email: '' })
const passwordForm = ref({
    current_password: '',
    password: '',
    password_confirmation: ''
})

const profileMessage = ref('')
const profileSuccess = ref(false)
const isUpdating = ref(false)

const passwordMessage = ref('')
const passwordSuccess = ref(false)
const isChangingPassword = ref(false)

const redirecting = ref(false)

// Your regexes:
const nameRegex = /^[a-zA-Z]{2,15}$/
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
const passwordRegex = /^(?=.*\d)(?=.*[\W_]).{6,}$/

const nameError = ref('')
const emailError = ref('')
const passwordError = ref('')

const goBack = () => {
    router.push('/')
}

const clearMessage = (msgRef) => {
    setTimeout(() => {
        msgRef.value = ''
    }, 5000)
}

onMounted(async () => {
    if (redirecting.value) return

    const token = localStorage.getItem('token')
    if (!token) {
        redirecting.value = true
        router.push('/login')
        return
    }

    try {
        const response = await axios.get('/api/user', {
            headers: {
                Authorization: `Bearer ${token}`
            },
            validateStatus: (status) => status < 500
        })

        form.value.name = response.data.name
        form.value.email = response.data.email
    } catch (error) {
        if (error.response?.status === 401) {
            localStorage.removeItem('token')
            if (!redirecting.value) {
                redirecting.value = true
                router.push('/login')
            }
        } else {
            profileMessage.value = 'Failed to load user profile. Please refresh the page.'
            profileSuccess.value = false
            clearMessage(profileMessage)
        }
    }
})

const updateProfile = async () => {
    nameError.value = ''
    emailError.value = ''
    profileMessage.value = ''

    if (!nameRegex.test(form.value.name)) {
        nameError.value = 'Name must be 2-15 letters only (no spaces or special characters).'
        return
    }

    if (!emailRegex.test(form.value.email)) {
        emailError.value = 'Invalid email format.'
        return
    }

    isUpdating.value = true

    try {
        const token = localStorage.getItem('token')
        await axios.patch('/profile', form.value, {
            headers: { Authorization: `Bearer ${token}` }
        })
        profileMessage.value = 'Profile updated successfully!'
        profileSuccess.value = true
    } catch (err) {
        if (err.response?.status === 401) {
            localStorage.removeItem('token')
            if (!redirecting.value) {
                redirecting.value = true
                router.push('/login')
            }
        } else if (err.response) {
            profileMessage.value =
                err.response.data?.message ||
                err.response.data?.errors?.name?.[0] ||
                err.response.data?.errors?.email?.[0] ||
                'Failed to update profile.'
        } else {
            profileMessage.value = err.message || 'Network error occurred'
        }
        profileSuccess.value = false
    } finally {
        isUpdating.value = false
        clearMessage(profileMessage)
    }
}

const changePassword = async () => {
    passwordError.value = ''
    passwordMessage.value = ''

    if (!passwordRegex.test(passwordForm.value.password)) {
        passwordError.value =
            'Password must be at least 6 characters and include at least one digit and one special character.'
        return
    }

    if (passwordForm.value.password !== passwordForm.value.password_confirmation) {
        passwordError.value = 'Passwords do not match.'
        return
    }

    isChangingPassword.value = true

    try {
        const token = localStorage.getItem('token')
        const response = await axios.patch('/user/password', passwordForm.value, {
            headers: { Authorization: `Bearer ${token}` }
        })
        passwordMessage.value = response.data.message || 'Password changed successfully!'
        passwordSuccess.value = true

        passwordForm.value = {
            current_password: '',
            password: '',
            password_confirmation: ''
        }
    } catch (err) {
        if (err.response?.status === 401) {
            localStorage.removeItem('token')
            if (!redirecting.value) {
                redirecting.value = true
                router.push('/login')
            }
        } else if (err.response) {
            passwordMessage.value =
                err.response.data?.message ||
                err.response.data?.errors?.current_password?.[0] ||
                err.response.data?.errors?.password?.[0] ||
                'Failed to change password.'
        } else {
            passwordMessage.value = err.message || 'Network error occurred'
        }
        passwordSuccess.value = false
    } finally {
        isChangingPassword.value = false
        clearMessage(passwordMessage)
    }
}

const logout = async () => {
    try {
        const token = localStorage.getItem('token')
        await axios.post('/logout', {}, {
            headers: { Authorization: `Bearer ${token}` }
        })
    } catch (err) {
        console.error('Logout error:', err)
    } finally {
        localStorage.removeItem('token')
        if (!redirecting.value) {
            redirecting.value = true
            router.push('/')
        }
    }
}
</script>

<style scoped>
/* same styles as before */
.profile-container {
    max-width: 500px;
    margin: 50px auto;
    padding: 20px;
    position: relative;
}

.back-button {
    position: absolute;
    top: 10px;
    left: 20px;
    background: none;
    border: none;
    color: #1e40af;
    font-size: 1rem;
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.back-button:hover {
    background-color: #f0f0f0;
}

.profile-title {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
    text-align: center;
}

.password-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
}

.form-group {
    margin-bottom: 15px;
}

.form-input {
    width: 100%;
    padding: 8px;
    border: 1px solid #aaa;
    border-radius: 4px;
}

.button-blue {
    background-color: #007BFF;
    color: white;
    padding: 10px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
    margin-top: 10px;
}

.button-blue:hover:not(:disabled) {
    background-color: #0056b3;
}

.button-blue:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.button-green {
    background-color: #28a745;
    color: white;
    padding: 10px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
    margin-top: 10px;
}

.button-green:hover:not(:disabled) {
    background-color: #1e7e34;
}

.button-green:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.button-red {
    background-color: #e53935;
    color: white;
    padding: 10px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
    margin-top: 10px;
}

.button-red:hover {
    background-color: #c62828;
}

.separator {
    margin: 30px 0;
    border: none;
    border-top: 1px solid #ccc;
}

.success-message {
    color: green;
    font-weight: bold;
    margin-bottom: 15px;
    text-align: center;
}

.error-message {
    color: red;
    font-weight: bold;
    font-size: 0.875rem;
    margin-top: 5px;
}
</style>
