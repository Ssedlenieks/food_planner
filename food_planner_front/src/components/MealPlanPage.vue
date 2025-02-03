<template>
  <div>
    <!-- Include the NavBar component -->
    <NavBar />

    <!-- Calendar Header -->
    <div class="calendar">
      <div class="calendar-header">
        <button @click="goToPreviousMonth">&lt;</button>
        <h2>{{ currentMonth }} {{ currentYear }}</h2>
        <button @click="goToNextMonth">&gt;</button>
      </div>

      <!-- Calendar Grid -->
      <div class="calendar-grid">
        <!-- Weekday Headers -->
        <div class="weekday" v-for="day in weekdays" :key="day">{{ day }}</div>

        <!-- Days in the Month -->
        <div
          class="day"
          v-for="day in daysInMonth"
          :key="day.date"
          :class="{ today: day.isToday }"
          @click="openModal(day)"
        >
          {{ day.date }}
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div :class="['modal', { show: isModalOpen }]">
      <div class="modal-content">
        <span class="close" @click="closeModal">&times;</span>
        <h2>{{ selectedDay.date }}</h2>
        <p>{{ selectedDay.details }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import NavBar from "@/components/navbar.vue";

export default {
  name: "MealPlanPage",
  components: {
    NavBar,
  },
  data() {
    return {
      currentMonth: new Date().toLocaleString('default', { month: 'long' }),
      currentYear: new Date().getFullYear(),
      weekdays: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
      daysInMonth: this.getDaysInMonth(new Date().getFullYear(), new Date().getMonth()),
      isModalOpen: false,
      selectedDay: {},
    };
  },
  methods: {
    getDaysInMonth(year, month) {
      const date = new Date(year, month, 1);
      const days = [];
      while (date.getMonth() === month) {
        const day = {
          date: date.getDate(),
          isToday: date.toDateString() === new Date().toDateString(),
          details: `Sample details for ${date.toDateString()}`, // Example data
        };
        days.push(day);
        date.setDate(date.getDate() + 1);
      }
      // Adjust the start of the week to Monday
      const firstDayIndex = new Date(year, month, 1).getDay();
      const leadingDays = (firstDayIndex === 0 ? 6 : firstDayIndex - 1);
      for (let i = 0; i < leadingDays; i++) {
        days.unshift({ date: '', isToday: false, details: '' });
      }
      return days;
    },
    goToPreviousMonth() {
      const date = new Date(this.currentYear, new Date().getMonth() - 1, 1);
      this.currentMonth = date.toLocaleString('default', { month: 'long' });
      this.currentYear = date.getFullYear();
      this.daysInMonth = this.getDaysInMonth(date.getFullYear(), date.getMonth());
    },
    goToNextMonth() {
      const date = new Date(this.currentYear, new Date().getMonth() + 1, 1);
      this.currentMonth = date.toLocaleString('default', { month: 'long' });
      this.currentYear = date.getFullYear();
      this.daysInMonth = this.getDaysInMonth(date.getFullYear(), date.getMonth());
    },
    openModal(day) {
      this.selectedDay = day;
      this.isModalOpen = true;
    },
    closeModal() {
      this.isModalOpen = false;
    },
  },
};
</script>

<style scoped>
/* Calendar container */
.calendar {
  max-width: 600px;
  margin: 0 auto;
  text-align: center;
}

/* Calendar header */
.calendar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.calendar-header button {
  background: #007bff;
  color: #fff;
  border: none;
  border-radius: 6px;
  padding: 5px 10px;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s ease;
}

.calendar-header button:hover {
  background: #0056b3;
}

/* Weekday headers */
.weekday {
  font-weight: bold;
  text-transform: uppercase;
  color: #555;
  padding: 10px 0;
  background: #f3f3f3;
  border-radius: 6px;
  margin-bottom: 5px;
  text-align: center;
}

/* Calendar grid */
.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 10px;
}

/* Days */
.day {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  cursor: pointer;
}

.day.today {
  background-color: #007bff;
  color: white;
}

/* Modal styles */
.modal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  justify-content: center;
  align-items: center;
}

.modal.show {
  display: flex;
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 8px;
  position: relative;
}

.close {
  position: absolute;
  top: 10px;
  right: 10px;
  cursor: pointer;
}
</style>
