<template>
  <div class="order-details">
    <!-- Back button and header -->
    <div class="header">
      <router-link to="/order-history" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back to Orders
      </router-link>
      <h2>Order Details</h2>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="loading">
      <i class="fas fa-spinner fa-spin"></i>
      <p>Loading order details...</p>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="error">
      <i class="fas fa-exclamation-circle"></i>
      <p>{{ error }}</p>
      <button @click="fetchOrderDetails" class="retry-btn">Try Again</button>
    </div>

    <!-- Order details content -->
    <div v-else-if="order" class="order-content">
      <!-- Order status and tracking -->
      <div class="order-status-card">
        <div class="status-header">
          <h3>Order Status</h3>
          <span class="order-id">#{{ order.id }}</span>
        </div>
        <div class="status-timeline">
          <div class="status-step" :class="{ active: isStatusActive('Pending') }">
            <div class="step-icon"><i class="fas fa-clock"></i></div>
            <div class="step-label">Order Placed</div>
            <div class="step-time">{{ formatDate(order.created_at) }}</div>
          </div>
          <div class="status-step" :class="{ active: isStatusActive('Processing') }">
            <div class="step-icon"><i class="fas fa-kitchen-set"></i></div>
            <div class="step-label">Preparing</div>
          </div>
          <div class="status-step" :class="{ active: isStatusActive('Out for Delivery') }">
            <div class="step-icon"><i class="fas fa-motorcycle"></i></div>
            <div class="step-label">On the Way</div>
          </div>
          <div class="status-step" :class="{ active: isStatusActive('Completed') }">
            <div class="step-icon"><i class="fas fa-check-circle"></i></div>
            <div class="step-label">Delivered</div>
          </div>
        </div>
      </div>

      <!-- Customer and delivery information -->
      <div class="info-section">
        <div class="delivery-info">
          <h3>Delivery Information</h3>
          <div class="info-content">
            <p><strong>Name:</strong> {{ order.customer.name }}</p>
            <p><strong>Phone:</strong> {{ order.customer.phone }}</p>
            <p><strong>Address:</strong> {{ order.customer.address }}</p>
            <p><strong>Payment Method:</strong> {{ order.customer.payment }}</p>
          </div>
        </div>
      </div>

      <!-- Order items -->
      <div class="items-section">
        <h3>Ordered Items</h3>
        <div class="items-list">
          <div v-for="item in order.items" :key="item.id" class="order-item">
            <img 
              :src="item.product.image_url" 
              :alt="item.product.name"
              @error="handleImageError"
              class="item-image"
            >
            <div class="item-details">
              <h4>{{ item.product.name }}</h4>
              <p class="quantity">Quantity: {{ item.quantity }}</p>
              <p class="price">₱{{ formatPrice(item.price) }}</p>
            </div>
            <button 
              v-if="order.status === 'Completed'"
              @click="openReviewModal(item)"
              class="review-btn"
            >
              Leave Review
            </button>
          </div>
        </div>

        <!-- Order summary -->
        <div class="order-summary">
          <div class="summary-row">
            <span>Subtotal:</span>
            <span>₱{{ formatPrice(order.subtotal) }}</span>
          </div>
          <div class="summary-row">
            <span>Delivery Fee:</span>
            <span>₱{{ formatPrice(order.delivery_fee) }}</span>
          </div>
          <div class="summary-row total">
            <span>Total:</span>
            <span>₱{{ formatPrice(order.total) }}</span>
          </div>
        </div>
      </div>

      <!-- Action buttons -->
      <div class="action-buttons">
        <button @click="reorder" class="reorder-btn">
          <i class="fas fa-shopping-cart"></i> Order Again
        </button>
       <!-- <button v-if="canCancel" @click="cancelOrder" class="cancel-btn">
          <i class="fas fa-times"></i> Cancel Order
        </button> -->
      </div>
    </div>

    <!-- Review Modal -->
    <div v-if="showReviewModal" class="review-modal">
      <div class="modal-content">
        <h3>Write a Review</h3>
        <div class="review-form">
          <div class="rating">
            <i v-for="star in 5" 
               :key="star" 
               class="fas fa-star"
               :class="{ active: star <= rating }"
               @click="rating = star"
            ></i>
          </div>
          <textarea 
            v-model="reviewText" 
            placeholder="Write your review here..."
            rows="4"
          ></textarea>
          <div class="modal-buttons">
            <button @click="submitReview" class="submit-btn">Submit Review</button>
            <button @click="closeReviewModal" class="cancel-btn">Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'OrderDetails',
  data() {
    return {
      order: null,
      loading: true,
      error: null,
      defaultImageUrl: '/images/default-product.png',
      showReviewModal: false,
      selectedItem: null,
      rating: 0,
      reviewText: '',
    }
  },
  computed: {
    canCancel() {
      return this.order && ['Pending', 'Processing'].includes(this.order.status);
    }
  },
  methods: {
    async fetchOrderDetails() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(`/api/orders/${this.$route.params.id}`);
        console.log('Order details:', response.data);
        this.order = response.data;
      } catch (error) {
        console.error('Error fetching order details:', error);
        this.error = 'Failed to load order details. Please try again.';
      } finally {
        this.loading = false;
      }
    },
    formatDate(date) {
      if (!date) return 'N/A';
      
      // Parse the date string
      const orderDate = new Date(date);
      
      // Check if the date is valid
      if (isNaN(orderDate.getTime())) {
        console.error('Invalid date:', date);
        return 'Invalid Date';
      }
      
      // Format the date
      return orderDate.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
      });
    },
    formatPrice(price) {
      if (typeof price !== 'number') {
        price = Number(price);
      }
      return price.toFixed(2);
    },
    handleImageError(e) {
      e.target.src = this.defaultImageUrl;
    },
    isStatusActive(status) {
      const statusOrder = ['Pending', 'Processing', 'Out for Delivery', 'Completed'];
      const currentIndex = statusOrder.indexOf(this.order.status);
      const targetIndex = statusOrder.indexOf(status);
      return targetIndex <= currentIndex;
    },
    async reorder() {
      try {
        // Add items to cart
        const cartItems = this.order.items.map(item => ({
          id: item.product.id,
          name: item.product.name,
          price: item.price,
          quantity: item.quantity,
          image: item.product.image_url
        }));
        
        // You'll need to implement this in your store/cart management
        this.$store.commit('setCartItems', cartItems);
        
        // Navigate to order
        this.$router.push('/order');
      } catch (error) {
        console.error('Error reordering:', error);
      }
    },
    async cancelOrder() {
      if (!confirm('Are you sure you want to cancel this order?')) return;
      
      try {
        await axios.post(`/api/orders/${this.order.id}/cancel`);
        this.fetchOrderDetails(); // Refresh order details
      } catch (error) {
        console.error('Error canceling order:', error);
      }
    },
    openReviewModal(item) {
      this.selectedItem = item;
      this.showReviewModal = true;
      this.rating = 0;
      this.reviewText = '';
    },
    closeReviewModal() {
      this.showReviewModal = false;
      this.selectedItem = null;
      this.rating = 0;
      this.reviewText = '';
    },
    async submitReview() {
      if (!this.rating) {
        alert('Please select a rating');
        return;
      }
      
      try {
        await axios.post('/api/reviews', {
          product_id: this.selectedItem.product.id,
          rating: this.rating,
          comment: this.reviewText
        });
        
        this.closeReviewModal();
      } catch (error) {
        console.error('Error submitting review:', error);
      }
    }
  },
  mounted() {
    this.fetchOrderDetails();
  }
}
</script>

<style scoped>
.order-details {
  padding: 15rem;
  max-width: 1200px;
  margin: 0 auto;
  background: #1a1a1a;
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

.header h2 {
  font-size: 2.4rem;
  margin: 0;
}

.loading, .error {
  text-align: center;
  padding: 4rem;
  background: #2a2a2a;
  border-radius: 8px;
  margin-top: 2rem;
}

.order-content {
  display: grid;
  gap: 2rem;
}

.order-status-card {
  background: #2a2a2a;
  border-radius: 8px;
  padding: 2rem;
}

.status-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.status-timeline {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  position: relative;
  padding: 2rem 0;
}

.status-timeline::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 2px;
  background: #404040;
  z-index: 0;
}

.status-step {
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
  z-index: 1;
}

.step-icon {
  width: 40px;
  height: 40px;
  background: #404040;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 0.5rem;
  transition: background-color 0.3s;
}

.status-step.active .step-icon {
  background: #e58f00;
}

.step-label {
  font-size: 1.2rem;
  color: #999;
}

.status-step.active .step-label {
  color: white;
}

.info-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.delivery-info {
  background: #2a2a2a;
  border-radius: 8px;
  padding: 2rem;
}

.info-content p {
  margin: 0.5rem 0;
  color: #ccc;
}

.items-section {
  background: #2a2a2a;
  border-radius: 8px;
  padding: 2rem;
}

.order-item {
  display: flex;
  gap: 2rem;
  padding: 1.5rem;
  border-bottom: 1px solid #404040;
}

.item-image {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 8px;
}

.item-details {
  flex: 1;
}

.item-details h4 {
  margin: 0 0 0.5rem 0;
  font-size: 1.6rem;
}

.quantity, .price {
  color: #ccc;
  margin: 0.3rem 0;
}

.order-summary {
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid #404040;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin: 0.5rem 0;
  color: #ccc;
}

.summary-row.total {
  font-size: 1.8rem;
  font-weight: bold;
  color: white;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #404040;
}

.action-buttons {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
}

.reorder-btn, .cancel-btn, .review-btn {
  padding: 1rem 2rem;
  border: none;
  border-radius: 8px;
  font-size: 1.4rem;
  cursor: pointer;
  transition: background-color 0.3s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.reorder-btn {
  background: #e58f00;
  color: white;
}

.reorder-btn:hover {
  background: #d48200;
}

.cancel-btn {
  background: #dc3545;
  color: white;
}

.cancel-btn:hover {
  background: #c82333;
}

.review-btn {
  background: #28a745;
  color: white;
}

.review-btn:hover {
  background: #218838;
}

.review-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: #2a2a2a;
  padding: 2rem;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
}

.rating {
  display: flex;
  gap: 0.5rem;
  margin: 1rem 0;
}

.rating i {
  font-size: 2rem;
  color: #666;
  cursor: pointer;
}

.rating i.active {
  color: #ffc107;
}

textarea {
  width: 100%;
  padding: 1rem;
  border: 1px solid #404040;
  border-radius: 4px;
  background: #1a1a1a;
  color: white;
  margin: 1rem 0;
  resize: vertical;
}

.modal-buttons {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 1rem;
}

.submit-btn {
  background: #28a745;
  color: white;
  border: none;
  padding: 0.8rem 1.5rem;
  border-radius: 4px;
  cursor: pointer;
}

.submit-btn:hover {
  background: #218838;
}
</style>
