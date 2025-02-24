<template>
  <div class="order-details-container">
    <h2>Order Details</h2>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Order Detail ID</th>
            <th>Order ID</th>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Total Price</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="orderDetails.length === 0">
            <td colspan="7">No order details found</td>
          </tr>
          <tr v-for="detail in orderDetails" :key="detail.id">
            <td>{{ detail.id }}</td>
            <td>{{ detail.OrderID }}</td>
            <td>{{ detail.product_id }}</td>
            <td>{{ detail.product_name }}</td>
            <td>{{ detail.quantity }}</td>
            <td>₱{{ formatPrice(detail.price) }}</td>
            <td>₱{{ formatPrice(detail.price * detail.quantity) }}</td>
          </tr>
        </tbody>
      </table>
      <div class="grand-total">
        Grand Total: ₱{{ formatPrice(grandTotal) }}
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
      orderDetails: []
    };
  },
  computed: {
    grandTotal() {
      return this.orderDetails.reduce((total, detail) => {
        return total + (detail.price * detail.quantity);
      }, 0);
    }
  },
  methods: {
    async fetchOrderDetails() {
      try {
        const response = await axios.get('/admin/order-details');
        this.orderDetails = response.data;
      } catch (error) {
        console.error('Error fetching order details:', error);
      }
    },
    formatPrice(price) {
      return parseFloat(price).toFixed(2);
    }
  },
  mounted() {
    this.fetchOrderDetails();
  }
};
</script>

<style scoped>
.order-details-container {
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

.grand-total {
  text-align: right;
  margin-top: 20px;
  font-weight: bold;
  font-size: 1.1em;
}
</style> 