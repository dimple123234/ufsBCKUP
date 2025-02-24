
<template>
  <div class="order-history">
    <div class="header">
      <router-link to="/profile" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back to Profile
      </router-link>
      <h2>Order History</h2>
    </div>
    
    <div class="orders-container">
      <div v-if="loading" class="loading">
        <i class="fas fa-spinner fa-spin"></i>
        <p>Loading your orders...</p>
      </div>
      
      <div v-else-if="error" class="error">
        <i class="fas fa-exclamation-circle"></i>
        <p>{{ error }}</p>
        <button @click="fetchOrders" class="retry-btn">Try Again</button>
      </div>
      
      <div v-else-if="!orders.length" class="no-orders">
        <i class="fas fa-shopping-bag"></i>
        <p>No orders found</p>
        <router-link to="/order" class="order-now-btn">
          Order Now
        </router-link>
      </div>
      
      <div v-else class="orders-list">
        <div v-for="order in orders" :key="order.id" class="order-card">
          <div class="order-header">
            <div class="order-info">
              <span class="order-id">#{{ order.id }}</span>
              <span class="order-date">{{ formatDate(order.created_at) }}</span>
            </div>
            <span class="order-status" :class="order.status.toLowerCase()">
              {{ order.status }}
            </span>
          </div>
          
          <div class="order-items">
            <div v-for="item in order.items" :key="item.id" class="order-item">
              <img 
                :src="item.product.image_url" 
                :alt="item.product.name"
                @error="handleImageError"
                class="item-image">
              <div class="item-details">
                <h4>{{ item.product.name }}</h4>
                <p>Quantity: {{ item.quantity }}</p>
                <p class="item-price">₱{{ formatPrice(item.price) }}</p>
              </div>
            </div>
          </div>
          
          <div class="order-footer">
            <div class="order-total">
              <span>Total:</span>
              <span class="total-amount">₱{{ formatPrice(order.total) }}</span>
            </div>
            <button @click="viewOrderDetails(order.id)" class="view-details-btn">
              View Details
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
  name: 'OrderHistory',
  data() {
    return {
      orders: [],
      loading: true,
      error: null,
      defaultImageUrl: '/images/default-product.png'
    }
  },
  methods: {
    async fetchOrders() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get('/api/orders/history');
        console.log('Orders response:', response.data);
        
        if (response.data.error) {
          throw new Error(response.data.error);
        }
        
        if (response.data.message) {
          throw new Error(response.data.message);
        }
        
        if (!Array.isArray(response.data)) {
          console.error('Unexpected response format:', response.data);
          throw new Error('Invalid response format from server');
        }

        this.orders = response.data.filter(order => {
          if (!order || !order.items || !Array.isArray(order.items)) {
            console.warn('Invalid order format:', order);
            return false;
          }
          return order.items.length > 0;
        });

        if (this.orders.length === 0) {
          console.log('No orders found or all orders were filtered out');
          this.error = 'No orders found in your history.';
        }
      } catch (error) {
        console.error('Error fetching orders:', error);
        this.error = error.response?.data?.message || error.message || 'Failed to load orders. Please try again.';
      } finally {
        this.loading = false;
      }
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    formatPrice(price) {
      // Convert price to number and format with 2 decimal places
      return Number(price).toFixed(2);
    },
    handleImageError(e) {
      e.target.src = this.defaultImageUrl;
    },
    viewOrderDetails(orderId) {
      this.$router.push(`/order-details/${orderId}`);
    }
  },
  mounted() {
    this.fetchOrders();
  }
}
</script>

<style scoped>
.order-history {
  padding: 15rem;
  max-width: 1200px;
  margin: 0 auto;
}
.order-history .h2 {
  color: white;
}
.header {
  display: flex;
  align-items: center;
  margin-bottom: 2rem;
  gap: 2rem;
}

.back-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.8rem 1.5rem;
  background: #e58f00;
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-size: 1.4rem;
  transition: background-color 0.3s;
}

.back-btn:hover {
  background: #d48200;
}

.back-btn i {
  font-size: 1.6rem;
}

.header h2 {
  font-size: 2.4rem;
  color: white;
  margin: 0;
}

.loading, .error, .no-orders {
  text-align: center;
  padding: 4rem;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.loading i, .error i, .no-orders i {
  font-size: 4rem;
  color: #ccc;
  margin-bottom: 1rem;
}

.loading p, .error p, .no-orders p {
  font-size: 1.6rem;
  color: #666;
  margin: 1rem 0;
}

.retry-btn, .order-now-btn {
  display: inline-block;
  padding: 0.8rem 1.5rem;
  background: #e58f00;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1.4rem;
  cursor: pointer;
  text-decoration: none;
  margin-top: 1rem;
  transition: background-color 0.3s;
}

.retry-btn:hover, .order-now-btn:hover {
  background: #d48200;
}

.order-card {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  padding: 2rem;
  margin-bottom: 2rem;
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #eee;
}

.order-info {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.order-id {
  font-size: 1.6rem;
  font-weight: bold;
  color: #333;
}

.order-date {
  font-size: 1.4rem;
  color: #666;
}

.order-status {
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 1.4rem;
  font-weight: 600;
}

.order-status.pending {
  background: #fff3cd;
  color: #856404;
}

.order-status.processing {
  background: #cce5ff;
  color: #004085;
}

.order-status.completed {
  background: #d4edda;
  color: #155724;
}

.order-status.cancelled {
  background: #f8d7da;
  color: #721c24;
}

.order-items {
  margin: 1.5rem 0;
}

.order-item {
  display: flex;
  gap: 1.5rem;
  padding: 1rem 0;
  border-bottom: 1px solid #eee;
}

.order-item:last-child {
  border-bottom: none;
}

.item-image {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 4px;
}

.item-details {
  flex: 1;
}

.item-details h4 {
  font-size: 1.6rem;
  color: #333;
  margin-bottom: 0.5rem;
}

.item-details p {
  font-size: 1.4rem;
  color: #666;
  margin: 0.3rem 0;
}

.item-price {
  font-weight: 600;
  color: #e58f00;
}

.order-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #eee;
}

.order-total {
  font-size: 1.6rem;
}

.total-amount {
  font-weight: 600;
  color: #e58f00;
  margin-left: 1rem;
}

.view-details-btn {
  background: #e58f00;
  color: white;
  border: none;
  padding: 0.8rem 1.5rem;
  border-radius: 4px;
  font-size: 1.4rem;
  cursor: pointer;
  transition: background-color 0.3s;
}

.view-details-btn:hover {
  background: #d48200;
}
</style>
