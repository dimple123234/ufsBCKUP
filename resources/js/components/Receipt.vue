<template>
  <div class="receipt-container">
    <div class="print-button" @click="printReceipt">
      <button class="btn-print">
        <i class="fas fa-print"></i> Print Receipt
      </button>
    </div>
    
    <div v-if="error" class="error-message">
      {{ error }}
    </div>
    <div v-else-if="!orderDetails" class="loading">
      Loading receipt...
    </div>
    <div v-else class="receipt" ref="receiptContent">
      <div class="invoice-number">
        Invoice No. {{ invoiceNumber }}
      </div>

      <div class="company-info">
        <div class="company-header">
          <img src="/images/logo1.png" alt="Company Logo" class="company-logo">
          <div class="company-text">
            <h1>Universal Fast Food</h1>
            <p>Borromeo St. Surigao City</p>
          </div>
        </div>
      </div>

      <div class="divider"></div>

      <div class="client-info">
        <h2>Client</h2>
        <div class="info-grid">
          <p><strong>Name:</strong> {{ orderDetails.customer.name }}</p>
          <p><strong>Phone:</strong> {{ orderDetails.customer.phone }}</p>
          <p><strong>Address:</strong> {{ orderDetails.customer.address }}</p>
          <p><strong>Payment Method:</strong> {{ orderDetails.customer.payment }}</p>
        </div>
      </div>

      <div class="order-items">
        <table>
          <thead>
            <tr>
              <th>Item</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in orderDetails.items" :key="item.id">
              <td>{{ item.product.name }}</td>
              <td>₱{{ Number(item.price).toFixed(2) }}</td>
              <td>{{ item.quantity }}</td>
              <td>₱{{ (Number(item.price) * item.quantity).toFixed(2) }}</td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="2"><strong>Subtotal:</strong></td>
              <td><strong>{{ totalQuantity }}</strong></td>
              <td><strong>₱{{ totalPrice.toFixed(2) }}</strong></td>
            </tr>
            <tr>
              <td colspan="3"><strong>Delivery Fee:</strong></td>
              <td><strong>₱{{ deliveryFee.toFixed(2) }}</strong></td>
            </tr>
            <tr class="grand-total">
              <td colspan="3"><strong>Total Amount:</strong></td>
              <td><strong>₱{{ (totalPrice + deliveryFee).toFixed(2) }}</strong></td>
            </tr>
          </tfoot>
        </table>
      </div>

      <div class="signature-section">
        <img src="/images/signature2.png" alt="Director Signature" class="signature">
        <div class="director-name">
          <p>Director Name</p>
        </div>
      </div>

      <div class="footer">
        <div class="social-links">
          <a href="#" class="social-link"><i class="fab fa-facebook"></i> Ken Perandos</a>
          <a href="#" class="social-link"><i class="fab fa-twitter"></i> mendokusei699</a>
          <a href="#" class="social-link"><i class="fab fa-github"></i> Universal Fast Food</a>
          <a href="#" class="social-link"><i class="fab fa-instagram"></i> Uff Fast Food</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Receipt',
  data() {
    return {
      orderDetails: null,
      error: null,
      invoiceNumber: Math.floor(Math.random() * 900 + 100).toString(), // Random 3-digit number
      deliveryFee: 50 // Fixed delivery fee
    };
  },
  computed: {
    totalQuantity() {
      if (!this.orderDetails) return 0;
      return this.orderDetails.items.reduce((sum, item) => sum + item.quantity, 0);
    },
    totalPrice() {
      if (!this.orderDetails) return 0;
      return this.orderDetails.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    }
  },
  methods: {
    async fetchOrderDetails() {
      try {
        const orderId = sessionStorage.getItem('orderId');
        if (!orderId) {
          console.error('No order ID found');
          this.error = 'No order ID found. Please try again.';
          return;
        }

        const response = await axios.get(`/api/orders/${orderId}`);
        console.log('Order details:', response.data);
        this.orderDetails = response.data;
      } catch (error) {
        console.error('Error fetching order details:', error);
        this.error = 'Failed to load receipt. Please try again.';
      }
    },
    printReceipt() {
      window.print();
    }
  },
  async created() {
    await this.fetchOrderDetails();
  }
};
</script>

<style scoped>
.receipt-container {
  min-height: 100vh;
  padding: 2rem;
  background-color: #f5f5f5;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.print-button {
  margin-bottom: 2rem;
}

.btn-print {
  background-color: #4CAF50;
  color: white;
  padding: 0.8rem 1.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: background-color 0.3s;
}

.btn-print:hover {
  background-color: #45a049;
}

.loading {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  font-size: 1.2rem;
  color: #666;
}

.receipt {
  background-color: white;
  padding: 3rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  max-width: 800px;
  width: 100%;
}

.invoice-number {
  text-align: right;
  font-size: 1.2rem;
  color: #333;
  margin-bottom: 2rem;
}

.company-header {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1.5rem;
  margin-bottom: 1rem;
}

.company-logo {
  width: 80px;
  height: auto;
  object-fit: contain;
}

.company-text {
  text-align: left;
}

.company-text h1 {
  font-size: 2rem;
  color: #333;
  margin: 0;
}

.company-text p {
  color: #666;
  margin: 0.5rem 0 0 0;
}

.divider {
  height: 2px;
  background-color: #eee;
  margin: 2rem 0;
}

.client-info {
  margin: 2rem 0;
  padding: 1rem;
  background-color: #f9f9f9;
  border-radius: 4px;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
  margin-top: 1rem;
}

.order-items table {
  width: 100%;
  border-collapse: collapse;
  margin: 2rem 0;
}

.order-items th,
.order-items td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.order-items th {
  background-color: #f5f5f5;
  font-weight: bold;
}

.signature-section {
  margin: 3rem 0;
  text-align: center;
}

.signature {
  width: 150px;
  height: auto;
  margin-bottom: 0.5rem;
}

.director-name {
  font-size: 1.1rem;
}

.footer {
  margin-top: 3rem;
  padding-top: 2rem;
  border-top: 1px solid #eee;
}

.social-links {
  display: flex;
  justify-content: center;
  gap: 2rem;
}

.social-link {
  color: #666;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: color 0.3s;
}

.social-link:hover {
  color: #333;
}

.error-message {
  color: red;
  text-align: center;
  padding: 20px;
  font-weight: bold;
}

.grand-total {
  font-size: 1.2em;
  color: #000;
  background-color: #f8f9fa;
}

.grand-total td {
  padding: 15px 8px;
}

@media print {
  .receipt-container {
    padding: 0;
    background-color: white;
  }

  .receipt {
    box-shadow: none;
    max-width: none;
  }

  .loading {
    display: none;
  }

  .print-button {
    display: none;
  }
}
</style>
