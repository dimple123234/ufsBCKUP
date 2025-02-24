<template>
  <div class="seller-applications-container">
    <h2>Seller Applications</h2>
    <div v-if="message" class="message" :class="messageType">{{ message }}</div>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="applications.length === 0">
            <td colspan="6" style="text-align:center;">No pending applications</td>
          </tr>
          <tr v-for="application in applications" :key="application.id">
            <td>{{ application.name }}</td>
            <td>{{ application.email }}</td>
            <td>{{ application.phone }}</td>
            <td>{{ application.address }}</td>
            <td>
              <span :class="['status-badge', application.status]">
                {{ application.status }}
              </span>
            </td>
            <td>
              <template v-if="application.status === 'pending'">
                <button 
                  @click="approveApplication(application.id)" 
                  class="approve-btn"
                >
                  Approve
                </button>
                <button 
                  @click="rejectApplication(application.id)" 
                  class="reject-btn"
                >
                  Reject
                </button>
              </template>
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
  name: 'SellerApplications',
  data() {
    return {
      applications: [],
      message: '',
      messageType: ''
    };
  },
  methods: {
    async fetchApplications() {
      try {
        const response = await axios.get('/admin/seller-applications');
        this.applications = response.data;
      } catch (error) {
        console.error('Error fetching applications:', error);
        this.showMessage('Error fetching applications', 'error');
      }
    },
    async approveApplication(id) {
      try {
        await axios.post(`/admin/seller-applications/${id}/approve`, {}, {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
        this.showMessage('Application approved successfully', 'success');
        await this.fetchApplications(); // Refresh the applications list
      } catch (error) {
        console.error('Error approving application:', error);
        this.showMessage(error.response?.data?.message || 'Error approving application', 'error');
      }
    },
    async rejectApplication(id) {
      if (!confirm('Are you sure you want to reject this application?')) return;
      
      try {
        await axios.post(`/admin/seller-applications/${id}/reject`, {}, {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
        this.showMessage('Application rejected successfully', 'success');
        await this.fetchApplications();
      } catch (error) {
        console.error('Error rejecting application:', error);
        this.showMessage(
          error.response?.data?.message || 'Error rejecting application', 
          'error'
        );
      }
    },
    showMessage(text, type) {
      this.message = text;
      this.messageType = type;
      setTimeout(() => {
        this.message = '';
        this.messageType = '';
      }, 3000);
    }
  },
  mounted() {
    this.fetchApplications();
  }
};
</script>

<style scoped>
.seller-applications-container {
  padding: 20px;
}

h2 {
  color: rgb(202, 6, 6);
  margin-bottom: 20px;
}

.message {
  padding: 10px;
  margin-bottom: 15px;
  border-radius: 4px;
}

.message.success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.message.error {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
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
  text-transform: none;
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

.approve-btn {
  background-color: #28a745;
  color: white;
  border: none;
  padding: 5px 10px;
  border-radius: 4px;
  margin-right: 5px;
  cursor: pointer;
}

.reject-btn {
  background-color: #dc3545;
  color: white;
  border: none;
  padding: 5px 10px;
  border-radius: 4px;
  cursor: pointer;
}

.status-badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-weight: bold;
  text-transform: capitalize;
}

.status-badge.pending {
  background-color: #ffc107;
  color: #000;
}

.status-badge.approved {
  background-color: #28a745;
  color: #fff;
}

.status-badge.rejected {
  background-color: #dc3545;
  color: #fff;
}
</style>