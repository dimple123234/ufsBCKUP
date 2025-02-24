<template>
  <header class="header">
  <a href ="/" id="logo"> <img src="images/logo1.png" alt=""></a>

  <div id="menu-bars" class="fas fa-bars"></div>

  
</header>
  
  <div>
    <!-- Background Image -->
    <div class="background-image"></div>

    <!-- Main Container -->
    <div class="container">
      <!-- Cover Image and Text -->
      <div class="cover">
        <div class="front">
          <img src="images/orderlogo.jpg" alt="">
          <div class="text">
            <span class="text-1">Flavors for Every Palate, <br>  Speed for Every Craving!</span>
            <span class="text-2">Buy your meal now!</span>
          </div>
        </div>
        <div class="back">
          <div class="text">
            <span class="text-1">Complete miles of journey <br> with one step</span>
            <span class="text-2">Let's get started</span>
          </div>
        </div>
      </div>

      <!-- Forms Container -->
      <div class="forms">
        <div class="form-content">
          <!-- Login Form -->
          <div class="login-form">
            <div class="title">Login</div>
            <form @submit.prevent="handleLogin">
              <div class="input-boxes">
                <div class="input-box">
                  <i class="fa fa-envelope"></i>
                  <input 
                    v-model="form.email" 
                    type="email" 
                    placeholder="Enter your email" 
                    required
                  />
                </div>
                <div class="input-box">
                  <i class="fa fa-lock"></i>
                  <input
                    v-model="form.password"
                    :type="showPassword ? 'text' : 'password'"
                    placeholder="Enter your password"
                    required
                  />
                  <i 
                    class="fa-solid eye-icon" 
                    :class="showPassword ? 'fa fa-eye-slash' : 'fa fa-eye'"
                    @click="togglePassword"
                  ></i>
                </div>
                <div v-if="error" class="error-message">
                  {{ error }}
                </div>
                <div class="button input-box">
                  <input type="submit" value="Login" />
                </div>
                <div class="text sign-up-text">
                  Don't have an account? <router-link to="/register" style="color: rgb(202, 6, 6);">Register now</router-link>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      form: {
        email: '',
        password: ''
      },
      error: null,
      showPassword: false
    };
  },
  methods: {
    async handleLogin() {
      try {
        const response = await axios.post('/login', this.form);
        
        if (response.data.status === 'success') {
          const user = response.data.user;
          
          // Store user data based on role
          if (user.role === 'seller') {
            localStorage.setItem('sellerUser', JSON.stringify(user));
            localStorage.setItem('isSeller', 'true');
          } else {
            localStorage.setItem('customerUser', JSON.stringify(user));
            localStorage.setItem('isCustomer', 'true');
          }
          
          // Show success message
          alert('Login successful!');
          
          // Redirect to order page for both roles
          this.$router.push('/order');
        }
      } catch (error) {
        if (error.response) {
          if (error.response.data.errors) {
            this.error = Object.values(error.response.data.errors)[0][0];
          } else if (error.response.data.message) {
            this.error = error.response.data.message;
          }
        } else {
          this.error = 'An error occurred. Please try again.';
        }
      }
    },
    togglePassword() {
      this.showPassword = !this.showPassword;
    }
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');
@import url('https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
/* 
  CSS Styling for the Login Page
*/

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Poppins", sans-serif;
}

body {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 30px;
}
.header {
  position: fixed;
  left: 0;
  right: 0;
  top: 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 2rem 9%;
  background-color: rgb(202, 6, 6);
  box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .1);
  z-index: 100;
}
.background-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-image: url(images/gallery/3jollibee.jpg);
  background-size: cover;
  background-position: center;
  opacity: 0.5;
  z-index: -1;
}

.container {
    max-width: 800px;
    width: 90%;
    height: auto;
    min-height: 600px;
    background: #fff;
    padding: 40px 30px;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    perspective: 2700px;
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    margin-top: 50px;
}

.container .cover {
  position: absolute;
  top: 0;
  left: 50%;
  height: 100%;
  width: 50%;
  z-index: 98;
  transition: all 1s ease;
  transform-origin: left;
  transform-style: preserve-3d;
}

.container #flip:checked ~ .cover {
  transform: rotateY(-180deg);
}

.container .cover .front,
.container .cover .back {
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
}

.cover .back {
  transform: rotateY(180deg);
  backface-visibility: hidden;
}

.container .cover::before,
.container .cover::after {
  content: '';
  position: absolute;
  height: 100%;
  width: 100%;
  background: rgb(202, 6, 6);
  opacity: 0.5;
  z-index: 12;
}

.container .cover::after {
  opacity: 0.3;
  transform: rotateY(180deg);
  backface-visibility: hidden;
}

.container .cover img {
  position: absolute;
  height: 100%;
  width: 100%;
  object-fit: cover;
  z-index: 10;
}

.container .cover .text {
  position: absolute;
  z-index: 130;
  height: 100%;
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.cover .text .text-1,
.cover .text .text-2 {
  font-size: 26px;
  font-weight: 599;
  color: #fff;
  text-align: center;
}

.cover .text .text-2 {
  font-size: 15px;
  font-weight: 500;
}

.container .forms {
  height: 100%;
  width: 100%;
  background: #fff;
}

.container .form-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.form-content .login-form,
.form-content .signup-form {
  width: calc(100% / 2 - 25px);
}

.forms .form-content .title {
  position: relative;
  font-size: 24px;
  font-weight: 500;
  color: #333;
}

.forms .form-content .title:before {
  content: '';
  position: absolute;
  left: 0;
  bottom: 0;
  height: 3px;
  width: 25px;
  background: rgb(202, 6, 6);
}

.forms .signup-form .title:before {
  width: 20px;
}

.forms .form-content .input-boxes {
  margin-top: 30px;
}

.forms .form-content .input-box {
  display: flex;
  align-items: center;
  height: 50px;
  width: 100%;
  margin: 10px 0;
  position: relative;
}

.form-content .input-box input {
  height: 100%;
  width: 100%;
  outline: none;
  border: none;
  padding: 0 30px;
  font-size: 16px;
  font-weight: 500;
  border-bottom: 2px solid rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease;
  text-transform: none;
}

.form-content .input-box input:focus,
.form-content .input-box input:valid {
  border-color: rgb(202, 6, 6);
}

.form-content .input-box i {
  position: absolute;
  color: rgb(202, 6, 6);
  font-size: 17px;
  top: 50%;
  transform: translateY(-50%);
}

.form-content .input-box .eye-icon {
  right: 10px;
  cursor: pointer;
  top: 45%;
}

.forms .form-content .text {
  font-size: 14px;
  font-weight: 500;
  color: #333;
}

.forms .form-content .text a {
  text-decoration: none;
}

.forms .form-content .text a:hover {
  text-decoration: underline;
}

.forms .form-content .button {
  color: #fff;
  margin-top: 40px;
}

.forms .form-content .button input {
  color: #fff;
  background: rgb(202, 6, 6);
  border-radius: 6px;
  padding: 0;
  cursor: pointer;
  transition: all 0.4s ease;
}

.forms .form-content .button input:hover {
  background: black;
  border: red;
}

.forms .form-content label {
  color: rgb(202, 6, 6);
  cursor: pointer;
}

.forms .form-content label:hover {
  text-decoration: underline;
}

.forms .form-content .login-text,
.forms .form-content .sign-up-text {
  text-align: center;
  margin-top: 25px;
}

.container #flip {
  display: none;
}

@media (max-width: 730px) {
  .container .cover {
    display: none;
  }

  .form-content .login-form,
  .form-content .signup-form {
    width: 100%;
  }

  .form-content .signup-form {
    display: none;
  }

  .container #flip:checked ~ .forms .form-content .signup-form {
    display: block;
  }

  .container #flip:checked ~ .forms .form-content .login-form {
    display: none;
  }
}

.error-message {
  color: red;
  text-align: center;
  margin: 10px 0;
  font-size: 0.9em;
}

.toggle-password {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: #666;
}

.toggle-password:hover {
  color: #333;
}

.input-box {
  position: relative;
}
</style>
