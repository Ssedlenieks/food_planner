<template>
    <div style="height: 700px;">
        <vue-cal
            style="height: 100%;"
            :events="events"
            default-view="month"
            :time="false"
            :disable-views="['years', 'year', 'week']"
            @event-click="onEventClick"
            @cell-click="onDateClick"
        >
            <!-- Custom event template -->
            <template #event="{ event }">
                <div class="calendar-event">
                    <img :src="event.original.recipe.meal_image" class="meal-thumb" />
                    <div class="meal-info">
                        <strong>{{ event.original.recipe.meal_name }}</strong>
                        <small>🍽 {{ event.original.meal_type }}</small>
                    </div>
                </div>
            </template>
        </vue-cal>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import VueCal from 'vue-cal'
import 'vue-cal/dist/vuecal.css'

const router = useRouter()
const events = ref([])

const onEventClick = (event) => {
    const mealId = event.original?.recipe?.id
    if (mealId) {
        router.push({ name: 'view-recipe', params: { id: mealId } })
    }
}

const onDateClick = (date) => {
    const selected = date instanceof Date ? date.toISOString().split('T')[0] : date
    router.push({ name: 'MealPlan', query: { date: selected } })
}

onMounted(async () => {
    try {
        const token = localStorage.getItem('token')
        const response = await axios.get('http://127.0.0.1:8001/meal-plans', {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })

        events.value = response.data.map(plan => ({
            title: plan.recipe.meal_name,
            start: plan.date,
            end: plan.date,
            original: plan
        }))
    } catch (error) {
        console.error('Error loading meal plans:', error)
    }
})
</script>

<style scoped>
.calendar-event {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 6px;
    background-color: #fdfdfd;
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

.meal-thumb {
    width: 48px;
    height: 48px;
    object-fit: cover;
    border-radius: 6px;
    flex-shrink: 0;
}

.meal-info {
    display: flex;
    flex-direction: column;
    font-size: 1rem;
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
}

.meal-info strong {
    font-size: 1.1rem;
    font-weight: bold;
}

.meal-info small {
    font-size: 0.9rem;
    color: #666;
}

</style>
