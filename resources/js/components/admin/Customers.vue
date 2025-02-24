<template>
  <div class="customers-container">
    <h2>Customers</h2>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Customer ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Payment Method</th>
            <th>Card</th>
            <th>Created at</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="customers.length === 0">
            <td colspan="8">No customers found</td>
          </tr>
          <tr v-for="customer in customers" :key="customer.customer_id">
            <td>{{ customer.customer_id }}</td>
            <td>{{ customer.name || 'N/A' }}</td>
            <td>{{ customer.phone || 'N/A' }}</td>
            <td>{{ customer.address || 'N/A' }}</td>
            <td>{{ customer.payment || 'N/A' }}</td>
            <td>{{ customer.card || 'N/A' }}</td>
            <td>{{ formatDate(customer.created_at) }}</td>
            <td>
              <button @click="deleteCustomer(customer.customer_id)" class="delete-btn">
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
  name: 'Customers',
  data() {
    return {
      customers: []
    };
  },
  methods: {
    async fetchCustomers() {
      try {
        const response = await axios.get('/admin/customers');
        this.customers = response.data;
      } catch (error) {
        console.error('Error fetching customers:', error);
      }
    },
    formatDate(date) {
      if (!date) return 'N/A';
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
    async deleteCustomer(customerId) {
      if (confirm('Are you sure you want to delete this customer?')) {
        try {
          await axios.delete(`/admin/customers/${customerId}`);
          // Remove from the local list
          this.customers = this.customers.filter(c => c.customer_id !== customerId);
          alert('Customer deleted successfully');
        } catch (error) {
          alert('Failed to delete customer');
          console.error(error);
        }
      }
    }
  },
  mounted() {
    this.fetchCustomers();
  }
};
</script>

<style scoped>
.customers-container {
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