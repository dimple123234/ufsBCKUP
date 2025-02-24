<template>
  <div class="login-container">
    <div class="login-box">
      <h2>Admin Login</h2>
      <form @submit.prevent="handleLogin">
        <div class="input-group">
          <label for="username">Username</label>
          <input 
            type="text" 
            id="username" 
            v-model="credentials.username" 
            placeholder="Enter username"
            required
          >
        </div>
        <div class="input-group">
          <label for="password">Password</label>
          <input 
            type="password" 
            id="password" 
            v-model="credentials.password" 
            placeholder="Enter password"
            required
          >
        </div>
        <div class="error-message" v-if="error">{{ error }}</div>
        <button type="submit" :disabled="loading">
          {{ loading ? 'Logging in...' : 'Login' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminLogin',
  data() {
    return {
      credentials: {
        username: '',
        password: ''
      },
      error: null,
      loading: false
    };
  },
  methods: {
    async handleLogin() {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.post('/admin/login', this.credentials);
        console.log('Login response:', response.data);

        if (response.data.success) {
          localStorage.setItem('isAdmin', 'true');
          if (response.data.user) {
            localStorage.setItem('adminUser', JSON.stringify(response.data.user));
          }
          
          // Use router for navigation
          this.$router.push('/admin');
        } else {
          this.error = response.data.message || 'Login failed';
        }
      } catch (error) {
        console.error('Login error:', error);
        this.error = error.response?.data?.message || 'Login failed';
      } finally {
        this.loading = false;
      }
    }
  },
  created() {
    // If already logged in as admin, redirect to dashboard
    if (localStorage.getItem('isAdmin') === 'true') {
      this.$router.push({ name: 'AdminDashboard' });
    }
  }
};
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: rgb(202, 6, 6);
}

.login-box {
  background-color: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
}

h2 {
  text-align: center;
  color: rgb(202, 6, 6);
  margin-bottom: 2rem;
  font-size: 2rem;
  font-weight: bold;
  text-transform: none;
}

.input-group {
  margin-bottom: 1.5rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  color: #333;
  text-transform: none;
}

input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  text-transform: none;
}

button {
  width: 100%;
  padding: 0.75rem;
  background-color: rgb(202, 6, 6);
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.error-message {
  color: #dc3545;
  margin-bottom: 1rem;
  text-align: center;
}
</style>