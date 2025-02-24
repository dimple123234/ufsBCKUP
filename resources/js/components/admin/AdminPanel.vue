<template>
  <div class="admin-panel">
    <header>
      <img src="/images/admin1.png" alt="Admin Image">
      <h1>Hello Admin!</h1>
    </header>

    <nav>
      <ul>
        <li><router-link to="/admin/users">Users</router-link></li>
        <li><router-link to="/admin/employees">Employees</router-link></li>
        <li><router-link to="/admin/customers">Customers</router-link></li>
        <li><router-link to="/admin/orders">Orders</router-link></li>
        <li><router-link to="/admin/products">Products</router-link></li>
        <li><router-link to="/admin/order-details">Order Details</router-link></li>
        <li><router-link to="/admin/seller-applications">Seller Applications</router-link></li>
        <li>
          <button @click="handleLogout" class="logout-btn">Logout</button>
        </li>
      </ul>
    </nav>

    <div class="container">
      <router-view></router-view>
    </div>

    <footer>
      <p>&copy; Design by Kenny Fazbear. (2023)</p>
    </footer>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminPanel',
  methods: {
    async handleLogout() {
      try {
        // Get the CSRF token from the meta tag
        const token = document.querySelector('meta[name="csrf-token"]').content;
        
        const response = await axios.post('/admin/logout', {}, {
          headers: {
            'X-CSRF-TOKEN': token
          }
        });
        
        // Clear admin state and redirect
        localStorage.removeItem('isAdmin');
        localStorage.removeItem('adminUser');
        window.location.href = '/admin/login';
      } catch (error) {
        console.error('Logout failed:', error);
        // Still try to redirect even if the logout fails
        window.location.href = '/admin/login';
      }
    }
  }
}
</script>

<style scoped>
.admin-panel {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

header {
  background-color: rgb(202, 6, 6);
  color: #fff;
  padding: 20px;
  text-align: left;
  display: flex;
  align-items: center;
  justify-content: center;
}

header h1 {
  font-size: 2rem;
  font-weight: bold;
  margin-left: 10px;
}

header img {
  width: 50px;
  height: auto;
  margin-right: 10px;
}

nav {
  background-color: #f4f4f4;
  padding: 10px;
}

nav ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-wrap: wrap;
  font-size: 16px;
  padding: 5px;
  
}

nav ul li {
  margin: 0 10px;
  margin-top: 10px;
}

nav ul li a {
  text-decoration: none;
  color: #333;
  padding: 5px;
  border-radius: 5px;
  transition: background-color 0.3s ease;
}

nav ul li a:hover {
  background-color: #ddd;
}

.container {
  padding: 20px;
  flex: 1;
}

.container h2 {
  font-size: 2rem;
  font-weight: bold;
}
.container p {
  font-size: 1.5rem;
  margin-top: 10px;
}

.logout-btn {
  background: none;
  border: none;
  color: red;
  cursor: pointer;
  font-size: 16px;
  padding: 5px;
  margin-top: -100px;
}

.logout-btn:hover {
  background-color: #ddd;
  border-radius: 5px;
}

footer {
  background-color: rgb(202, 6, 6);
  color: #fff;
  padding: 30px;
  text-align: center;
  margin-top: auto;
}

</style> 