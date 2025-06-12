<template>
    <div class="admin-panel">
        <h1>Admin Panel</h1>

        <div class="tabs">
            <button
                :class="{ active: activeTab === 'meals' }"
                @click="activeTab = 'meals'"
                aria-label="Manage Meals"
            >
                Manage Meals
            </button>
            <button
                :class="{ active: activeTab === 'users' }"
                @click="activeTab = 'users'"
                aria-label="Manage Users"
            >
                Manage Users
            </button>
        </div>

        <div v-if="activeTab === 'meals'" class="tab-content">
            <h2>All Meals</h2>
            <table>
                <thead>
                <tr><th>ID</th><th>Name</th><th>User</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <tr v-for="meal in meals" :key="meal.id">
                    <td>{{ meal.id }}</td>
                    <td>{{ meal.meal_name }}</td>
                    <td>{{ meal.user?.name || 'Unknown' }}</td>
                    <td>
                        <button class="delete-btn" @click="deleteMeal(meal.id)">Delete</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div v-if="activeTab === 'users'" class="tab-content">
            <h2>All Users</h2>
            <table>
                <thead>
                <tr><th>ID</th><th>Name</th><th>Email</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <tr v-for="user in users" :key="user.id">
                    <td>{{ user.id }}</td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td>
                        <button class="delete-btn" @click="deleteUser(user.id)">Delete</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    data() {
        return {
            activeTab: 'meals',
            meals: [],
            users: [],
        }
    },
    mounted() {
        this.fetchMeals()
        this.fetchUsers()
    },
    methods: {
        async fetchMeals() {
            try {
                const res = await axios.get('/admin/meals')
                this.meals = res.data
            } catch (error) {
                console.error("Failed to fetch meals:", error)
            }
        },
        async fetchUsers() {
            try {
                const res = await axios.get('/admin/users')
                this.users = res.data
            } catch (error) {
                console.error("Failed to fetch users:", error)
            }
        },
        async deleteMeal(id) {
            if (confirm("Are you sure you want to delete this meal?")) {
                try {
                    await axios.delete(`/admin/meals/${id}`)
                    this.fetchMeals()
                } catch (error) {
                    console.error("Failed to delete meal:", error)
                }
            }
        },
        async deleteUser(id) {
            if (confirm("Are you sure you want to delete this user?")) {
                try {
                    await axios.delete(`/admin/users/${id}`)
                    this.fetchUsers()
                } catch (error) {
                    console.error("Failed to delete user:", error)
                }
            }
        }
    }
}
</script>

<style scoped>
.admin-panel {
    max-width: 900px;
    margin: 2rem auto;
    padding: 1.5rem 2rem;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgb(0 0 0 / 0.1);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
}

h1 {
    text-align: center;
    margin-bottom: 2rem;
    color: #2c3e50;
}

.tabs {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.tabs button {
    background-color: #f0f4f8;
    border: 2px solid transparent;
    padding: 0.6rem 1.4rem;
    font-weight: 600;
    font-size: 1rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #2c3e50;
}

.tabs button.active,
.tabs button:hover {
    background-color: #2c3e50;
    color: white;
    border-color: #1abc9c;
    box-shadow: 0 4px 10px rgb(26 188 156 / 0.4);
}

.tab-content h2 {
    margin-bottom: 1rem;
    color: #34495e;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95rem;
    box-shadow: 0 2px 6px rgb(0 0 0 / 0.05);
    border-radius: 10px;
    overflow: hidden;
}

thead tr {
    background: linear-gradient(90deg, #1abc9c, #16a085);
    color: white;
    text-align: left;
}

thead th,
tbody td {
    padding: 12px 15px;
}

tbody tr {
    border-bottom: 1px solid #ddd;
    transition: background-color 0.2s;
}

tbody tr:hover {
    background-color: #ecf0f1;
}

.delete-btn {
    background-color: #e74c3c;
    border: none;
    color: white;
    padding: 0.4rem 0.9rem;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: background-color 0.25s ease;
}

.delete-btn:hover {
    background-color: #c0392b;
}

@media (max-width: 600px) {
    .admin-panel {
        padding: 1rem;
    }
    table,
    thead,
    tbody,
    th,
    td,
    tr {
        display: block;
    }
    thead tr {
        position: absolute;
        top: -9999px;
        left: -9999px;
    }
    tbody tr {
        margin-bottom: 1rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 1rem;
    }
    tbody td {
        border: none;
        padding: 0.5rem 0;
        position: relative;
        padding-left: 50%;
    }
    tbody td::before {
        position: absolute;
        top: 0.5rem;
        left: 1rem;
        width: 45%;
        padding-right: 10px;
        white-space: nowrap;
        font-weight: 600;
        content: attr(data-label);
        color: #2c3e50;
    }
}
</style>
