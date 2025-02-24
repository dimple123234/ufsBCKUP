<template>
  <div class="users-container">
    <h2>Registered Users</h2>
    <p>Component loaded: {{ componentLoaded }}</p>
    <p>Data status: {{ dataStatus }}</p>
    <div class="table-responsive" v-if="users.length > 0">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Role</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td>{{ user.id }}</td>
            <td>{{ user.name || 'N/A' }}</td>
            <td>{{ user.email || 'N/A' }}</td>
            <td>{{ user.phone || 'N/A' }}</td>
            <td>{{ user.address || 'N/A' }}</td>
            <td>{{ user.role || 'N/A' }}</td>
            <td>{{ formatDate(user.created_at) }}</td>
            <td>
              <button @click="deleteUser(user.id)" class="delete-btn">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-else>
      Loading users... (Count: {{ users.length }})
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Users',
  data() {
    return {
      users: [],
      componentLoaded: 'Yes',
      dataStatus: 'Initializing'
    };
  },
  methods: {
    async fetchUsers() {
      this.dataStatus = 'Fetching';
      try {
        console.log('Fetching users...');
        const response = await axios.get('/admin/users');
        console.log('Response:', response);
        this.users = response.data;
        this.dataStatus = 'Loaded';
      } catch (error) {
        console.error('Error fetching users:', error);
        this.dataStatus = 'Error: ' + error.message;
      }
    },
    formatDate(date) {
      if (!date) return 'N/A';
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
    async deleteUser(userId) {
      if (confirm('Are you sure you want to delete this user?')) {
        try {
          await axios.delete(`/admin/users/${userId}`);
          this.users = this.users.filter(user => user.id !== userId);
        } catch (error) {
          console.error('Error deleting user:', error);
        }
      }
    }
  },
  mounted() {
    console.log('Users component mounted');
    this.fetchUsers();
  }
};
</script>

<style scoped>
.users-container {
  padding: 20px;
}

h2 {
  color: rgb(202, 6, 6);
  margin-bottom: 20px;
}

.table-responsive {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

th, td {
  border: 1px solid #ddd;
  padding: 12px;
  text-align: left;
  text-transform: none;
}

th {
  background-color: #f2f2f2;
  color: rgb(202, 6, 6);
  font-weight: bold;
}

tr:nth-child(even) {
  background-color: #f9f9f9;
}

tr:hover {
  background-color: #f5f5f5;
}

.delete-btn {
  background-color: #ff6347;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 6px 12px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.delete-btn:hover {
  background-color: #cc4c39;
}
</style> 