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
.profile-container {
    max-width: 600px;
    margin: 60px auto;
    background: #ffffff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    position: relative;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.back-button {
    position: absolute;
    top: 20px;
    left: 20px;
    background: #f3f4f6;
    border: none;
    color: #374151;
    font-size: 1rem;
    cursor: pointer;
    padding: 6px 14px;
    border-radius: 6px;
    transition: all 0.3s;
}

.back-button:hover {
    background-color: #e5e7eb;
}

.profile-title {
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 25px;
    text-align: center;
    color: #1f2937;
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 10px;
}

.password-title {
    font-size: 22px;
    font-weight: 500;
    margin: 20px 0 10px;
    color: #374151;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 500;
    color: #374151;
}

.form-input {
    width: 100%;
    padding: 10px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.form-input:focus {
    border-color: #2563eb;
    outline: none;
}

.button-blue,
.button-green,
.button-red {
    display: block;
    width: 100%;
    padding: 12px;
    font-size: 1rem;
    font-weight: 600;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 10px;
    transition: background-color 0.3s;
}

.button-blue {
    background-color: #3b82f6;
    color: white;
}

.button-blue:hover:not(:disabled) {
    background-color: #2563eb;
}

.button-green {
    background-color: #10b981;
    color: white;
}

.button-green:hover:not(:disabled) {
    background-color: #059669;
}

.button-red {
    background-color: #ef4444;
    color: white;
}

.button-red:hover {
    background-color: #dc2626;
}

.separator {
    margin: 40px 0;
    border: none;
    border-top: 1px solid #e5e7eb;
}

.success-message {
    color: #16a34a;
    font-weight: 500;
    margin-bottom: 15px;
    text-align: center;
}

.error-message {
    color: #dc2626;
    font-weight: 500;
    font-size: 0.9rem;
    margin-top: 6px;
}

</style>
