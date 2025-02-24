<template>
  <div class="create-product-page">
    <nav class="profile-nav">
      <router-link to="/my-products" class="nav-link">
        <i class="fas fa-arrow-left"></i> Back to Products
      </router-link>
    </nav>

    <div class="form-container">
      <h2>Add New Product</h2>
      <form @submit.prevent="createProduct" class="product-form">
        <div class="form-group">
          <label for="productName">Product Name:</label>
          <input 
            type="text" 
            id="productName"
            v-model="product.name" 
            required
          />
        </div>

        <div class="form-group">
          <label for="productPrice">Price:</label>
          <input 
            type="number" 
            id="productPrice"
            v-model="product.price" 
            step="0.01"
            required
          />
        </div>

        <div class="form-group">
          <label for="productImage">Product Image:</label>
          <input 
            type="file" 
            id="productImage"
            @change="handleImageUpload"
            accept="image/*"
            required
          />
        </div>

        <div class="button-group">
          <button type="submit" class="submit-btn">Create Product</button>
          <router-link to="/my-products" class="cancel-btn">Cancel</router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'CreateProduct',
  data() {
    return {
      product: {
        name: '',
        price: '',
        image: null
      }
    }
  },
  methods: {
    handleImageUpload(event) {
      this.product.image = event.target.files[0];
    },
    async createProduct() {
      try {
        const formData = new FormData();
        formData.append('name', this.product.name);
        formData.append('price', this.product.price);
        formData.append('image', this.product.image);

        const response = await axios.post('/products', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'Accept': 'application/json'
          }
        });

        if (response.data.success) {
          alert('Product created successfully!');
          this.$router.push('/my-products');
        }
      } catch (error) {
        console.error('Error creating product:', error);
        alert(error.response?.data?.message || 'Error creating product');
      }
    }
  }
}
</script>

<style scoped>
.create-product-page {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.profile-nav {
  margin-bottom: 30px;
}

.nav-link {
  text-decoration: none;
  color: #333;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border-radius: 4px;
  background: #f5f5f5;
}

.form-container {
  background: white;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.product-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-weight: bold;
}

.form-group input {
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.button-group {
  display: flex;
  gap: 10px;
  margin-top: 20px;
}

.submit-btn, .cancel-btn {
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  text-decoration: none;
  text-align: center;
}

.submit-btn {
  background: #4CAF50;
  color: white;
}

.cancel-btn {
  background: #666;
  color: white;
}

.submit-btn:hover {
  background: #45a049;
}

.cancel-btn:hover {
  background: #555;
}
</style> 