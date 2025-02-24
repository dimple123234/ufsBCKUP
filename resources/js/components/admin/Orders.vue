<template>
  <div class="orders-container">
    <h2>Orders</h2>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Customer ID</th>
            <th>Employee ID</th>
            <th>Assigned Employee</th>
            <th>Order Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="orders.length === 0">
            <td colspan="6">No orders found</td>
          </tr>
          <tr v-for="order in orders" :key="order.OrderID">
            <td>{{ order.OrderID }}</td>
            <td>{{ order.CustomerID }}</td>
            <td>{{ order.employee_id }}</td>
            <td>
              <form @submit.prevent="assignEmployee(order.OrderID, $event)" class="assign-employee-form">
                <select 
                  v-model="order.employee_id" 
                  @change="assignEmployee(order.OrderID, $event)"
                >
                  <option value="">Select Employee</option>
                  <option 
                    v-for="employee in employees" 
                    :key="employee.employee_id"
                    :value="employee.employee_id"
                  >
                    {{ employee.first_name }} {{ employee.last_name }}
                  </option>
                </select>
              </form>
            </td>
            <td>{{ formatDate(order.OrderDate) }}</td>
            <td>
              <span 
                class="status-badge" 
                :class="order.employee_id ? 'assigned' : 'unassigned'"
              >
                {{ order.employee_id ? 'Assigned' : 'Unassigned' }}
              </span>
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
  name: 'Orders',
  data() {
    return {
      orders: [],
      employees: []
    };
  },
  methods: {
    async fetchOrders() {
      try {
        const response = await axios.get('/admin/orders');
        this.orders = response.data;
      } catch (error) {
        console.error('Error fetching orders:', error);
      }
    },
    async fetchEmployees() {
      try {
        const response = await axios.get('/admin/employees');
        this.employees = response.data;
      } catch (error) {
        console.error('Error fetching employees:', error);
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
    async assignEmployee(orderId, event) {
      try {
        const employeeId = event.target.value;
        await axios.post('/admin/assign-employee', {
          order_id: orderId,
          employee_id: employeeId
        });
        await this.fetchOrders(); // Refresh orders after assignment
      } catch (error) {
        console.error('Error assigning employee:', error);
      }
    }
  },
  mounted() {
    this.fetchOrders();
    this.fetchEmployees();
  }
};
</script>

<style scoped>
.orders-container {
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

.assign-employee-form select {
  padding: 8px;
  border-radius: 4px;
  border: 1px solid #ddd;
  width: 200px;
}

.status-badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: bold;
}

.status-badge.unassigned {
  background-color: #ff6347;
  color: white;
}

.status-badge.assigned {
  background-color: #4CAF50;
  color: white;
}
</style> 