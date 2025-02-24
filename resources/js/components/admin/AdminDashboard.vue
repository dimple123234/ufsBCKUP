<template>
  <div class="admin-dashboard">
    <div class="welcome-section">
      <h2>Welcome to the Admin Dashboard</h2>
      <p>Select a section from the navigation menu above to manage your restaurant.</p>
    </div>
    
    <div class="quick-stats">
      <div class="stat-card">
        <h3>Total Orders</h3>
        <div class="stat-value">{{ orderCount }}</div>
      </div>
      <div class="stat-card">
        <h3>Total Users</h3>
        <div class="stat-value">{{ userCount }}</div>
      </div>
      <div class="stat-card">
        <h3>Total Products</h3>
        <div class="stat-value">{{ productCount }}</div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminDashboard',
  data() {
    return {
      orderCount: 0,
      userCount: 0,
      productCount: 0
    }
  },
  async created() {
    try {
      const [orders, users, products] = await Promise.all([
        axios.get('/admin/orders'),
        axios.get('/admin/users'),
        axios.get('/admin/products')
      ]);
      
      this.orderCount = orders.data.length;
      this.userCount = users.data.length;
      this.productCount = products.data.length;
    } catch (error) {
      console.error('Error fetching dashboard data:', error);
    }
  }
}
</script>

<style scoped>
.admin-dashboard {
  padding: 20px;
}

.welcome-section {
  text-align: center;
  margin-bottom: 40px;
}

.welcome-section h2 {
  color: #333;
  margin-bottom: 10px;
}

.quick-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.stat-card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.stat-card h3 {
  color: #666;
  margin-bottom: 10px;
}

.stat-value {
  font-size: 2em;
  color: #ca0606;
  font-weight: bold;
}
</style>
