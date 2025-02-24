<template>
  <div class="employees-container">
    <h2>Add Employee</h2>
    <form @submit.prevent="addEmployee" class="add-employee-form">
      <div class="form-group">
        <label for="first_name">First Name:</label>
        <input 
          type="text" 
          id="first_name" 
          v-model="newEmployee.first_name" 
          required
        >
      </div>

      <div class="form-group">
        <label for="last_name">Last Name:</label>
        <input 
          type="text" 
          id="last_name" 
          v-model="newEmployee.last_name" 
          required
        >
      </div>

      <div class="form-group">
        <label for="email">Email:</label>
        <input 
          type="email" 
          id="email" 
          v-model="newEmployee.email" 
          required
        >
      </div>

      <div class="form-group">
        <label for="phone_number">Phone Number:</label>
        <input 
          type="text" 
          id="phone_number" 
          v-model="newEmployee.phone_number" 
          required
        >
      </div>

      <div class="form-group">
        <label for="notes">Note:</label>
        <input 
          type="text" 
          id="notes" 
          v-model="newEmployee.notes"
        >
      </div>

      <button type="submit" class="submit-btn">Add Employee</button>
    </form>

    <h2>Employee List</h2>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Employee ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Note</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="employees.length === 0">
            <td colspan="7">No employees found</td>
          </tr>
          <tr v-for="employee in employees" :key="employee.employee_id">
            <td>{{ employee.employee_id }}</td>
            <td>{{ employee.first_name }}</td>
            <td>{{ employee.last_name }}</td>
            <td>{{ employee.email }}</td>
            <td>{{ employee.phone_number }}</td>
            <td>{{ employee.notes || 'N/A' }}</td>
            <td>
              <button @click="deleteEmployee(employee.employee_id)" class="delete-btn">
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

// Add CSRF token to axios headers
if (document.querySelector('meta[name="csrf-token"]')) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
}

export default {
  name: 'Employees',
  data() {
    return {
      employees: [],
      newEmployee: {
        first_name: '',
        last_name: '',
        email: '',
        phone_number: '',
        notes: ''
      }
    };
  },
  methods: {
    async fetchEmployees() {
      try {
        const response = await axios.get('/admin/employees');
        console.log('Fetched employees:', response.data);
        this.employees = response.data;
      } catch (error) {
        console.error('Error fetching employees:', error.response?.data || error.message);
      }
    },
    async addEmployee() {
      try {
        console.log('Adding employee:', this.newEmployee);
        const response = await axios.post('/admin/employees', this.newEmployee);
        console.log('Response:', response.data);
        await this.fetchEmployees();
        // Reset form
        this.newEmployee = {
          first_name: '',
          last_name: '',
          email: '',
          phone_number: '',
          notes: ''
        };
      } catch (error) {
        console.error('Error adding employee:', error.response?.data || error.message);
        alert('Error adding employee: ' + (error.response?.data?.message || error.message));
      }
    },
    async deleteEmployee(employeeId) {
      if (confirm('Are you sure you want to delete this employee?')) {
        try {
          await axios.delete(`/admin/employees/${employeeId}`);
          this.employees = this.employees.filter(emp => emp.employee_id !== employeeId);
        } catch (error) {
          console.error('Error deleting employee:', error.response?.data || error.message);
          alert('Error deleting employee: ' + (error.response?.data?.message || error.message));
        }
      }
    }
  },
  mounted() {
    this.fetchEmployees();
  }
};
</script>

<style scoped>
.employees-container {
  padding: 20px;
}

h2 {
  color: rgb(202, 6, 6);
  margin-bottom: 20px;
}

.add-employee-form {
  background-color: #f9f9f9;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 30px;
}

.form-group {
  margin-bottom: 15px;
}

label {
  display: block;
  margin-bottom: 5px;
  color: #333;
}

input {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  text-transform: none;
}

input:focus {
  outline: none;
  border-color: rgb(202, 6, 6);
}

input[type="email"] {
  text-transform: none !important;
}

.submit-btn {
  background-color: rgb(202, 6, 6);
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
}

.submit-btn:hover {
  background-color: rgb(172, 6, 6);
}

.table-responsive {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

th, td {
  border: 1px solid #ddd;
  padding: 12px;
  text-align: left;
  text-transform: none;
}

th {
  background-color: #f2f2f2;
  color: rgb(202, 6, 6);
}

tr:nth-child(even) {
  background-color: #f9f9f9;
}

tr:hover {
  background-color: #f5f5f5;
}

td:nth-child(4),
th:nth-child(4) {
  text-transform: none !important;
}

.delete-btn {
  background-color: #ff6347;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 6px 12px;
  cursor: pointer;
}

.delete-btn:hover {
  background-color: #cc4c39;
}
</style>