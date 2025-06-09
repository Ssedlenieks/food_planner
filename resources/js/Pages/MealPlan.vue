<template>
    <div class="meal-plan-container">
        <button @click="goBack" class="back-button">
            ← Go Back
        </button>
        <h2>Plan Your Meals</h2>

        <form @submit.prevent="submitMealPlan" class="meal-plan-form">
            <label>
                Date:
                <input type="date" v-model="form.date" required />
            </label>

            <label>
                Meal Type:
                <select v-model="form.meal_type" required>
                    <option disabled value="">Select Type</option>
                    <option value="breakfast">Breakfast</option>
                    <option value="lunch">Lunch</option>
                    <option value="dinner">Dinner</option>
                    <option value="snack">Snack</option>
                </select>
            </label>

            <label>
                Selected Meal:
                <div class="selected-meal-box" @click="showModal = true">
                    <span v-if="selectedMeal">{{ selectedMeal.meal_name }}</span>
                    <span v-else>Select a Meal</span>
                </div>
            </label>

            <button type="submit" :disabled="!selectedMeal">Add to Plan</button>
        </form>

        <!-- MODAL -->
        <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
            <div class="modal">
                <input
                    v-model="search"
                    placeholder="Search meals..."
                    class="search-input"
                />

                <div
                    v-for="(group, category) in filteredMealsByCategory"
                    :key="category"
                    class="category-block"
                >
                    <h4 class="category-title">{{ category }}</h4>
                    <ul class="meal-list">
                        <li
                            v-for="meal in group"
                            :key="meal.id"
                            @click="selectMeal(meal)"
                            class="meal-item"
                        >
                            <img
                                :src="meal.meal_image || defaultImg"
                                alt="meal"
                                class="meal-thumb"
                            />
                            <span>{{ meal.meal_name }}</span>
                        </li>
                    </ul>
                </div>

                <button class="close-btn" @click="showModal = false">Close</button>
            </div>
        </div>

        <!-- ✅ Popup Notification -->
        <div v-if="popupMessage" class="popup-success">
            {{ popupMessage }}
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

axios.defaults.withCredentials = true

const router = useRouter()
const route = useRoute()

const getCsrfCookie = async () => {
    await axios.get('http://127.0.0.1:8001/sanctum/csrf-cookie')
}

const meals = ref([])
const search = ref('')
const selectedMeal = ref(null)
const showModal = ref(false)
const defaultImg = 'https://via.placeholder.com/50'
const popupMessage = ref('')

const form = ref({
    date: '',
    meal_type: '',
    meal_id: null,
})

const goBack = () => {
    router.push('/')
}

onMounted(async () => {
    try {
        const queryDate = route.query.date
        if (queryDate) {
            form.value.date = queryDate
        }

        const response = await axios.get('http://127.0.0.1:8001/public-meals')
        meals.value = response.data
    } catch (error) {
        console.error('Failed to fetch meals:', error)
    }
})

const filteredMealsByCategory = computed(() => {
    const grouped = {}
    const keyword = search.value.toLowerCase()

    meals.value.forEach((meal) => {
        if (keyword && !meal.meal_name.toLowerCase().includes(keyword)) return

        const category = meal.category?.name || 'Uncategorized'
        if (!grouped[category]) grouped[category] = []
        grouped[category].push(meal)
    })

    return grouped
})

function selectMeal(meal) {
    selectedMeal.value = meal
    form.value.meal_id = meal.id
    showModal.value = false
}

async function submitMealPlan() {
    try {
        await getCsrfCookie()

        const payload = {
            date: form.value.date,
            meal_type: form.value.meal_type,
            meal_id: form.value.meal_id,
        }

        await axios.post('http://127.0.0.1:8001/meal-plans', payload)

        popupMessage.value = `Meal "${selectedMeal.value.meal_name}" added for ${form.value.meal_type} on ${form.value.date}!`

        // Reset form
        form.value.date = ''
        form.value.meal_type = ''
        form.value.meal_id = null
        selectedMeal.value = null

        setTimeout(() => {
            popupMessage.value = ''
            router.push('/')
        }, 2000)
    } catch (error) {
        if (error.response?.data?.errors) {
            console.error('Validation errors:', error.response.data.errors)
            popupMessage.value = 'Validation error. Please check your form.'
        } else {
            console.error('Failed to save meal plan:', error)
            popupMessage.value = 'Something went wrong. Please try again.'
        }

        setTimeout(() => {
            popupMessage.value = ''
        }, 3000)
    }
}
</script>

<style scoped>
.meal-plan-container {
    max-width: 600px;
    margin: 2rem auto;
    padding: 1rem;
    position: relative;
}

.back-button {
    background: none;
    border: none;
    color: #3490dc;
    cursor: pointer;
    font-size: 1rem;
    margin-bottom: 1rem;
    padding: 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.back-button:hover {
    text-decoration: underline;
}

.meal-plan-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.selected-meal-box {
    padding: 0.5rem;
    border: 1px solid #ccc;
    cursor: pointer;
    background-color: #f9f9f9;
    border-radius: 6px;
}

button {
    padding: 0.5rem;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal {
    background: white;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    padding: 1rem;
    border-radius: 10px;
}

.search-input {
    width: 100%;
    padding: 0.5rem;
    margin-bottom: 1rem;
    font-size: 1rem;
}

.category-block {
    margin-bottom: 1.5rem;
}

.category-title {
    font-size: 1.2rem;
    font-weight: bold;
    background-color: #eee;
    padding: 0.5rem;
    border-radius: 6px;
    margin-bottom: 0.5rem;
}

.meal-list {
    list-style: none;
    padding-left: 0;
}

.meal-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.6rem;
    cursor: pointer;
    border-bottom: 1px solid #ddd;
    transition: background-color 0.2s;
}

.meal-item:hover {
    background-color: #f5f5f5;
}

.meal-thumb {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 6px;
}

.close-btn {
    margin-top: 1rem;
    background-color: #dc3545;
}

.popup-success {
    position: fixed;
    top: 1rem;
    right: 1rem;
    background-color: #28a745;
    color: white;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    z-index: 1000;
}
</style>
