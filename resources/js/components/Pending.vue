<template>
  <div class="pending-container">
    <div class="content-wrapper">
      <div class="card">
        <div class="card-body d-flex flex-column align-items-center">
          <div class="text-center mb-4">
            <i class="fas fa-clock fa-4x text-warning"></i>
          </div>
          <h2 class="card-title text-center mb-3">Application Pending</h2>
          <p class="card-text text-center">
            Thank you for applying as a seller. Your application is currently under review.
          </p>
          <p class="text-muted text-center">
            Please wait while our administrators review your application. 
          </p>
          <p class="text-muted text-center"> 
            This process may take some time.
          </p>
          <p class="text-muted text-center">
            You will be notified once your application has been approved.
          </p>
          <div class="mt-4 d-flex justify-content-center">
            <router-link to="/" class="btn btn-primary">
              Return to Home
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Pending',
  mounted() {
    // Check if user was redirected here after registration
    if (!this.$route.query.fromRegistration) {
      // If user tries to access this page directly without registration
      this.$router.push('/');
    }
  }
}
</script>

<style scoped>
.pending-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  background-color: #f8f9fa;
}

.content-wrapper {
  width: 100%;
  max-width: 600px;
}

.card {
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  border: none;
  background-color: white;
}

.card-body {
  padding: 2.5rem;
  text-align: center;
}

.text-warning {
  color: #ffc107;
}

.btn-primary {
  color: white;
  background-color: #007bff;
  border-color: #007bff;
  padding: 10px 30px;
  border-radius: 8px;
  font-weight: 500;
  transition: all 0.3s ease;
  margin: 10px auto 0;
}

.btn-primary:hover {
  background-color: #0056b3;
  border-color: #0056b3;
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(0, 123, 255, 0.2);
}

h2 {
  font-weight: 600;
  margin: 10px auto 10px;
  color: #2c3e50;
  width: 100%;
  text-align: center;
}

p {
  color: #6c757d;
  line-height: 1.6;
  width: 100%;
  text-align: left;
  font-weight: 400;
  font-size: 12px;
}

.card-text, .text-muted {
  margin-left: auto;
  margin-right: auto;
  max-width: 500px;
}
</style>
