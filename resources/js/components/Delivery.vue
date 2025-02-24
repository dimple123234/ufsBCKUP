<template>
  <div class="main-container">
    <div class="left-container">
      <button class="back-button" @click="goBack">
        <i class="fas fa-arrow-left"></i> Back
      </button>
      <div class="container">
        <img src="images/drive1.gif" alt="">
        <div class="progress-container">
          <div class="progress-bar" :style="{ width: progress + '%' }"></div>
        </div>
        <h1 id="timer">{{ formattedTimer }}</h1>
        <p class="message">{{ storeMessage }}</p>
        <button v-if="timeRemaining >= 540" class="cancel-button" @click="cancelOrder">
          Cancel Order
        </button>
      </div>
    </div>
    <div class="right-container">
      <div class="order-details">
        <h2>Order Details</h2>
        <div class="details">
          <p><strong>Order Number:</strong> {{ orderNumber }}</p>
          <p><strong>Total Amount:</strong> ₱{{ formattedAmount }}</p>
          <p><strong>Payment Method:</strong> {{ paymentMethod }}</p>
        </div>
      </div>
      <button class="receipt-button" @click="showPopup">
        View Receipt
      </button>
    </div>
  </div>

  <!-- Popup -->
  <div class="popup" :class="{ active: popupActive }">
    <div class="popup-content">
      <h2>Receipt</h2>
      <p>Your order has been confirmed!</p>
      <button @click="downloadReceipt">Download Receipt</button>
      <button @click="showPopup">Close</button>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex';
import axios from 'axios';

export default {
  name: 'Delivery',
  data() {
    return {
      messages: [
        "Your placed an order, you have 1 minute to cancel if you change your mind.",
        "Preparing your order",
        "The rider got your order and is on his way to deliver it to you",
        "The rider is just around the corner, please wait for his call",
        "Thank you for waiting, you have received your order."
      ],
      currentMessageIndex: 0,
      interval: null,
      orderNumber: '',
      totalAmount: '0.00',
      paymentMethod: '',
      popupActive: false,
      orderDetails: null
    };
  },
  computed: {
    ...mapState({
      deliveryStatus: state => state.deliveryStatus,
      storeMessage: state => state.deliveryMessage,
      timeRemaining: state => state.timeRemaining,
      isActive: state => state.isActive
    }),
    progress() {
      return (this.timeRemaining / 600) * 100;
    },
    formattedTimer() {
      const minutes = Math.floor(this.timeRemaining / 60);
      const seconds = this.timeRemaining % 60;
      return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    },
    currentMessage() {
      if (this.timeRemaining > 540) return this.messages[0];
      if (this.timeRemaining > 300) return this.messages[1];
      if (this.timeRemaining > 180) return this.messages[2];
      if (this.timeRemaining > 0) return this.messages[3];
      return this.messages[4];
    },
    formattedAmount() {
      return parseFloat(this.totalAmount).toFixed(2);
    }
  },
  methods: {
    ...mapActions(['updateDeliveryStatus', 'updateTimeRemaining']),
    async fetchOrderDetails() {
      try {
        const orderId = sessionStorage.getItem('orderId');
        if (!orderId) return;

        const response = await axios.get(`/api/orders/${orderId}`);
        const orderData = response.data;
        
        this.orderNumber = orderData.id;
        this.totalAmount = orderData.total;
        this.paymentMethod = orderData.customer.payment;
        this.orderDetails = orderData;
      } catch (error) {
        console.error('Error fetching order details:', error);
      }
    },
    startCountdown() {
      // Only initialize if not already active
      if (!this.isActive) {
        this.updateDeliveryStatus({
          status: true,
          message: this.messages[0],
          isActive: true
        });
        this.updateTimeRemaining(600);
      } else {
        // If returning to delivery page, sync the message with current time
        this.updateDeliveryStatus({
          status: true,
          message: this.currentMessage,
          isActive: true
        });
      }

      this.interval = setInterval(() => {
        if (this.timeRemaining > 0) {
          this.updateTimeRemaining(this.timeRemaining - 1);
          
          // Update message based on specific time intervals
          if (this.timeRemaining === 540) { // 9 minutes
            this.updateDeliveryStatus({
              status: true,
              message: this.messages[1],
              isActive: true
            });
          } else if (this.timeRemaining === 300) { // 5 minutes
            this.updateDeliveryStatus({
              status: true,
              message: this.messages[2],
              isActive: true
            });
          } else if (this.timeRemaining === 180) { // 3 minutes
            this.updateDeliveryStatus({
              status: true,
              message: this.messages[3],
              isActive: true
            });
          }
        } else {
          clearInterval(this.interval);
          this.updateDeliveryStatus({
            status: false,
            message: this.messages[4],
            isActive: false
          });
        }
      }, 1000);
    },
    goBack() {
      this.$router.push('/order');
    },
    showPopup() {
      this.popupActive = !this.popupActive;
    },
    downloadReceipt() {
      window.open('/receipt', '_blank');
    },
    async cancelOrder() {
      try {
        const orderId = sessionStorage.getItem('orderId');
        if (!orderId) return;

        // Call the cancel endpoint
        await axios.delete(`/api/orders/${orderId}/cancel`);

        // Clear the countdown interval
        clearInterval(this.interval);
        
        // Update the delivery status
        this.updateDeliveryStatus({
          status: false,
          message: "Order cancelled",
          isActive: false
        });

        // Remove the order ID from session storage
        sessionStorage.removeItem('orderId');

        // Redirect back to order page
        this.$router.push('/order');
      } catch (error) {
        console.error('Error cancelling order:', error);
        alert('Failed to cancel order: ' + (error.response?.data?.message || error.message));
      }
    }
  },
  async mounted() {
    await this.fetchOrderDetails();
    this.startCountdown();
  },
  beforeUnmount() {
    if (this.interval) {
      clearInterval(this.interval);
    }
  }
};
</script>

<style scoped>
.main-container {
  display: flex;
  height: 100vh;
  background-color: #f5f5f5;
}

.left-container {
  flex: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px;
  position: relative;
}

.right-container {
  flex: 1;
  background-color: white;
  padding: 20px;
  box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
}

.container {
  text-align: center;
  padding: 20px;
}

.container img {
  width: 300px;
  margin-bottom: 20px;
}

.progress-container {
  width: 80%;
  height: 20px;
  background-color: #f0f0f0;
  border-radius: 10px;
  margin: 20px auto;
  overflow: hidden;
}

.progress-bar {
  height: 100%;
  background: linear-gradient(90deg, #4CAF50, #8BC34A);
  transition: width 1s ease-in-out;
}

#timer {
  font-size: 48px;
  margin: 20px 0;
  color: #333;
}

.message {
  font-size: 24px;
  color: #666;
  margin: 20px 0;
}

.back-button {
  position: absolute;
  top: 20px;
  left: 20px;
  padding: 10px 20px;
  background-color: #4CAF50;
  color: white;
  border: none;
  outline: none;
  border-radius: 20px;
  font-size: 16px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: background-color 0.3s;
}

.back-button:hover {
  background-color: #45a049;
}

.order-details {
  background-color: #f9f9f9;
  padding: 20px;
  border-radius: 10px;
  margin-bottom: 20px;
}

.order-details h2 {
  color: #333;
  margin-bottom: 15px;
}

.details p {
  margin: 10px 0;
  color: #666;
}

.receipt-button {
  padding: 15px 30px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 25px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s;
  margin-top: auto;
}

.receipt-button:hover {
  background-color: #45a049;
}

.cancel-button {
  margin-top: 20px;
  padding: 10px 20px;
  background-color: #dc3545;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s;
}

.cancel-button:hover {
  background-color: #c82333;
}

.popup {
  position: fixed;
  top: 150%;
  left: 50%;
  transform: translate(-50%, -50%) scale(0.7);
  width: 80%;
  max-width: 500px;
  background-color: white;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  opacity: 0;
  transition: top 0.3s ease-in-out,
              opacity 0.3s ease-in-out,
              transform 0.3s ease-in-out;
  z-index: 1000;
}

.popup.active {
  top: 50%;
  opacity: 1;
  transform: translate(-50%, -50%) scale(1);
}

.popup-content {
  text-align: center;
}

.popup-content h2 {
  color: #333;
  margin-bottom: 15px;
}

.popup-content button {
  margin: 10px;
  padding: 10px 20px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.popup-content button:hover {
  background-color: #45a049;
}
</style>