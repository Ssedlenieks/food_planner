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
        <h3>Details for {{ selectedDay.date }}</h3>
        <p>{{ selectedDay.details }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import NavBar from "@/components/navbar.vue";

export default {
  name: "Calendar",
  data() {
    return {
      currentDate: new Date(),
      weekdays: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
      isModalOpen: false, // Modal state
      selectedDay: {}, // Selected day details
    };
  },
  components: {
    NavBar,
  },
  computed: {
    currentYear() {
      return this.currentDate.getFullYear();
    },
    currentMonth() {
      return this.currentDate.toLocaleString("default", { month: "long" });
    },
    daysInMonth() {
      const year = this.currentDate.getFullYear();
      const month = this.currentDate.getMonth();
      const totalDays = new Date(year, month + 1, 0).getDate();

      // Create an array of day objects
      const days = [];
      for (let date = 1; date <= totalDays; date++) {
        const current = new Date(year, month, date);
        days.push({
          date,
          isToday: current.toDateString() === this.currentDate.toDateString(),
          details: `Sample details for ${current.toDateString()}`, // Example data
        });
      }
      return days;
    },
  },
  methods: {
    openModal(day) {
      this.selectedDay = day; // Set the selected day details
      this.isModalOpen = true; // Open modal
    },
    closeModal() {
      this.isModalOpen = false; // Close modal
    },
    goToPreviousMonth() {
      this.currentDate.setMonth(this.currentDate.getMonth() - 1);
      this.currentDate = new Date(this.currentDate); // Trigger reactivity
    },
    goToNextMonth() {
      this.currentDate.setMonth(this.currentDate.getMonth() + 1);
      this.currentDate = new Date(this.currentDate); // Trigger reactivity
    },
  },
};
</script>

<style scoped>
/* Calendar container */
.calendar {
  max-width: 400px;
  margin: 20px auto;
  padding: 20px;
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  text-align: center;
  font-family: Arial, sans-serif;
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
  gap: 5px;
}

/* Days */
.day {
  padding: 10px 0;
  border: 1px solid #ddd;
  border-radius: 8px;
  background: #f9f9f9;
  color: #333;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  transition: background 0.2s ease, transform 0.2s ease;
}

.day:hover {
  background: #007bff;
  color: #fff;
  transform: scale(1.05);
}

.day:active {
  transform: scale(0.95);
}

.day.today {
  background: #007bff;
  color: #fff;
  font-weight: bold;
}

/* Modal styles */
.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.3s ease, visibility 0.3s ease;
  z-index: 1000;
}

.modal.show {
  opacity: 1;
  visibility: visible;
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 12px;
  width: 300px;
  text-align: center;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
  transform: scale(0.8);
  transition: transform 0.3s ease;
}

.modal.show .modal-content {
  transform: scale(1);
}

.modal-content h3 {
  margin-bottom: 10px;
}

.modal-content p {
  font-size: 14px;
  color: #555;
}

.close {
  position: absolute;
  top: 10px;
  right: 20px;
  font-size: 24px;
  cursor: pointer;
  color: #333;
}
</style>
