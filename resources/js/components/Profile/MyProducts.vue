<template>
  <div class="products-page">
    <!-- Navigation Bar -->
    <div class="nav-bar">
      <div class="nav-links">
        <router-link to="/order" class="nav-link">
          <i class="fas fa-home"></i> Home
        </router-link>
        <router-link to="/profile" class="nav-link">
          <i class="fas fa-user"></i> Profile
        </router-link>
        <router-link to="/profile-settings" class="nav-link">
          <i class="fas fa-cog"></i> Settings
        </router-link>
      </div>
    </div>

    <!-- Main Content -->
    <div class="content">
      <div class="header">
        <h1>My Products</h1>
        
        <router-link to="/order" class="nav-link">
          <i class="fas fa-home"></i> Home
        </router-link>
        <router-link to="/profile" class="nav-link">
          <i class="fas fa-user"></i> Profile
        </router-link>
        <router-link to="/profile-settings" class="nav-link">
          <i class="fas fa-cog"></i> Settings
        </router-link>

        <button @click="$router.push('/create-product')" class="add-button">
          <i class="fas fa-plus"></i> Add Product
        </button>
        



      </div>

      <!-- Products Grid -->
      <div class="products-grid">
        <div v-for="product in products" :key="product.id" class="product-card">
          <img :src="'/storage/' + product.image" :alt="product.name">
          <div class="product-details">
            <h3>{{ product.name }}</h3>
            <p class="price">₱{{ product.price }}</p>
            <button @click="deleteProduct(product.id)" class="delete-button">
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'MyProducts',
  data() {
    return {
      products: []
    }
  },
  created() {
    this.fetchProducts();
  },
  methods: {
    async fetchProducts() {
      try {
        // First, log the user data to verify authentication
        const userData = localStorage.getItem('user');
        console.log('User data:', userData);

        const response = await axios.get('/my-products');
        console.log('Products response:', response.data); // Add this log
        this.products = response.data;
      } catch (error) {
        console.error('Error fetching products:', error);
        if (error.response) {
          console.log('Error response:', error.response.data); // Add this log
        }
      }
    },
    async deleteProduct(id) {
      if (confirm('Are you sure you want to delete this product?')) {
        try {
          const token = JSON.parse(localStorage.getItem('user'))?.token;
          await axios.delete(`/products/${id}`, {
            headers: {
              'Authorization': `Bearer ${token}`
            }
          });
          this.fetchProducts();
        } catch (error) {
          console.error('Error deleting product:', error);
        }
      }
    }
  }
}
</script>

<style scoped>
.products-page {
  min-height: 100vh;
  background-color: #f5f5f5;
  position: relative;
}

.nav-bar {
  background-color: #c41e3a;
  padding: 15px 0;
  margin-bottom: 20px;
  position: relative;
  z-index: 10;
}

.nav-links {
  display: flex;
  gap: 20px;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  justify-content: flex-start;
  position: relative;
  z-index: 11;
}

.nav-link {
  color: white;
  text-decoration: none;
  padding: 8px 16px;
  border-radius: 4px;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 8px;
  position: relative;
  z-index: 12;
  background-color: transparent;
}

.nav-link:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.nav-link i {
  font-size: 1.1em;
}

.content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}
.header h1{
  color:white;
}

.add-button {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: background-color 0.3s;
}

.add-button:hover {
  background-color: #45a049;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
}

.product-card {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  transition: transform 0.3s;
}

.product-card:hover {
  transform: translateY(-5px);
}

.product-card img {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.product-details {
  padding: 15px;
}

.product-details h3 {
  margin: 0 0 10px 0;
  color: #333;
}

.price {
  color: #4CAF50;
  font-weight: bold;
  font-size: 1.2em;
  margin: 10px 0;
}

.delete-button {
  width: 100%;
  background-color: #ff4444;
  color: white;
  border: none;
  padding: 8px;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.delete-button:hover {
  background-color: #ff0000;
}
</style> 