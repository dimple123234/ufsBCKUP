<template>
  <div class="products-container">
    <h2>Products</h2>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Image</th>
            <th>Name</th>
            <th>ID</th>
            <th>Seller</th>
            <th>Price</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="products.length === 0">
            <td colspan="6" style="text-align:center;">No products available</td>
          </tr>
          <tr v-for="product in products" :key="product.id">
            <td>
              <img 
                :src="'/storage/' + product.image" 
                :alt="product.name"
                @error="handleImageError"
              >
            </td>
            <td>{{ product.name }}</td>
            <td>{{ product.id }}</td>
            <td>{{ product.seller_name }}</td>
            <td>₱{{ parseFloat(product.price).toFixed(2) }}</td>
            <td>
              <button @click="deleteProduct(product.id)" class="delete-btn">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Products',
  data() {
    return {
      products: []
    };
  },
  methods: {
    async fetchProducts() {
      try {
        const response = await axios.get('/admin/products');
        this.products = response.data;
      } catch (error) {
        console.error('Error fetching products:', error);
      }
    },
    async deleteProduct(id) {
      if (!confirm('Are you sure you want to delete this product?')) return;
      
      try {
        await axios.delete(`/admin/products/${id}`);
        this.products = this.products.filter(product => product.id !== id);
      } catch (error) {
        console.error('Error deleting product:', error);
      }
    },
    handleImageError(e) {
      e.target.src = '/images/default-product.png';
    }
  },
  mounted() {
    this.fetchProducts();
  }
};
</script>

<style scoped>
.products-container {
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

img {
  max-width: 100px;
  height: auto;
  object-fit: cover;
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