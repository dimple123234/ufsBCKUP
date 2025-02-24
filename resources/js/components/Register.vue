<template>
  <header class="header">
    <a href="/" id="logo"><img src="images/logo1.png" alt="" /></a>
    <div id="menu-bars" class="fas fa-bars"></div>
  </header>

  <div class="background-image"></div>

  <div class="container">
    <div class="cover">
      <div class="front">
        <img src="images/orderlogo.jpg" alt="" />
        <div class="text">
          <span class="text-1">Flavors for Every Palate, <br /> Speed for Every Craving!</span>
          <span class="text-2">Buy your meal now!</span>
        </div>
      </div>
      <div class="back">
        <div class="text">
          <span class="text-1">Complete miles of journey <br /> with one step</span>
          <span class="text-2">Let's get started</span>
        </div>
      </div>
    </div>

    <div class="forms">
      <div class="form-content">
        <div class="signup-form">
          <div class="title">Sign Up</div>

          <form @submit.prevent="handleSubmit">
            <div class="input-boxes">
              <div class="input-box">
                <i class="fa fa-user"></i>
                <input v-model="formData.name" type="text" placeholder="Enter your name" required />
              </div>

              <div class="input-box">
                <i class="fa fa-envelope"></i>
                <input v-model="formData.email" type="email" placeholder="Enter your email" required />
              </div>

              <div class="input-box">
                <i class="fa fa-lock"></i>
                <input
                  v-model="formData.password"
                  :type="passwordVisible ? 'text' : 'password'"
                  id="password"
                  placeholder="Enter your password"
                  required
                  @input="checkPasswordStrength"
                />
                <button type="button" @click="togglePassword" id="togglePassword">
                  <i class="fa fa-eye"></i>
                </button>
                <div v-if="passwordStrengthMessage" :style="{color: passwordStrengthColor, textAlign: 'left', marginLeft: '-140px'}">{{ passwordStrengthMessage }}</div>
              </div>

              <div class="input-box">
                <i class="fa fa-lock"></i>
                <input
                  v-model="formData.confirmPassword"
                  :type="confirmPasswordVisible ? 'text' : 'password'"
                  id="confirmPassword"
                  placeholder="Confirm password"
                  required
                  @input="checkPasswordsMatch"
                />
                <button type="button" @click="toggleConfirmPassword" id="toggleConfirmPassword">
                  <i class="fa fa-eye"></i>
                </button>
                <div v-if="passwordMatchMessage" :style="{color: passwordMatchColor, textAlign: 'left', marginLeft: '-160px'}">{{ passwordMatchMessage }}</div>
              </div>

              <div class="input-box">
                <i class="fa fa-phone"></i>
                <input 
                    v-model="formData.phone" 
                    type="tel" 
                    placeholder="Enter phone number" 
                    required 
                />
              </div>

              <div class="input-box">
                <i class="fa fa-address-card"></i>
                <input v-model="formData.address" type="text" placeholder="Enter address" required />
              </div>


              <div class="input-box">
                <i class="fa fa-user-tag"></i>
                <select 
                  v-model="formData.role" 
                  required
                  class="role-select"
                >
                  <option value="">Select Role</option>
                  <option value="customer">Customer</option>
                  <option value="seller">Seller</option>
                </select>
              </div>

              
              <div class="input-box">
                <label>
                  <input v-model="formData.agree" type="checkbox" required />
                </label>
                <span>I Agree To The <a href="#">Terms And Conditions</a></span>
              </div>

              <div class="button input-box">
                <input type="submit" value="Sign Up" />
              </div>
              <div class="text sign-up-text">
                Already have an account? <a href="/login" style="color: rgb(202, 6, 6);">Login now</a>
              </div>
            </div>
          </form>
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
            formData: {
                name: '',
                email: '',
                password: '',
                confirmPassword: '',
                phone: '',
                address: '',
                agree: false,
                role: '',
            },
            passwordVisible: false,
            confirmPasswordVisible: false,
            passwordStrengthMessage: '',
            passwordStrengthColor: 'red',
            passwordMatchMessage: '',
            passwordMatchColor: 'red',
        };
    },
    methods: {
        async handleSubmit() {
            // Validate passwords match
            if (this.formData.password !== this.formData.confirmPassword) {
                alert('Passwords do not match');
                return;
            }

            // Validate password strength
            if (this.passwordStrengthColor === 'red') {
                alert('Password is too weak');
                return;
            }

            // Validate terms agreement
            if (!this.formData.agree) {
                alert('Please agree to the terms and conditions');
                return;
            }

            try {
                if (this.formData.role === 'seller') {
                    // Handle seller registration
                    const response = await axios.post('/register/seller', this.formData);
                    if (response.data.success) {
                        alert('Seller registration successful! Please wait for admin approval.');
                        this.$router.push('/pending?fromRegistration=true');
                    }
                } else {
                    // Handle customer registration
                    const response = await axios.post('/register', this.formData);
                    if (response.data.success) {
                        alert('Registration successful! You can now login.');
                        this.$router.push('/login');
                    }
                }
            } catch (error) {
                console.error('Registration error:', error);
                alert(error.response?.data?.message || 'Registration failed. Please try again.');
            }
        },

        togglePassword() {
            this.passwordVisible = !this.passwordVisible;
        },
        toggleConfirmPassword() {
            this.confirmPasswordVisible = !this.confirmPasswordVisible;
        },
        checkPasswordStrength() {
            const password = this.formData.password;
            let strength = 0;
            const hasLowercase = /[a-z]/.test(password);
            const hasUppercase = /[A-Z]/.test(password);
            const hasNumbers = /\d/.test(password);
            const hasSpecialChars = /[!@#$%^&*(),.?":{}|<>]/.test(password);

            if (hasLowercase) strength += 1;
            if (hasUppercase) strength += 1;
            if (hasNumbers) strength += 1;
            if (hasSpecialChars) strength += 1;

            if (password.length >= 8 && strength >= 3) {
                this.passwordStrengthMessage = 'Strong password';
                this.passwordStrengthColor = 'green';
            } else if (password.length >= 6 && strength >= 2) {
                this.passwordStrengthMessage = 'Moderate password';
                this.passwordStrengthColor = 'orange';
            } else {
                this.passwordStrengthMessage = 'Weak password';
                this.passwordStrengthColor = 'red';
            }
        },
        checkPasswordsMatch() {
            if (this.formData.password === this.formData.confirmPassword && this.formData.password !== '') {
                this.passwordMatchMessage = 'Passwords match';
                this.passwordMatchColor = 'green';
            } else {
                this.passwordMatchMessage = 'Passwords do not match';
                this.passwordMatchColor = 'red';
            }
        },
    },
};
</script>
  
<style scoped>
 @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');
  *{
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Poppins" , sans-serif;
}
body{
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
  background-image: url(images/gallery/pizza.jpg);
  background-size: cover;
  background-position: center;
  opacity: 0.5; /* Adjust the opacity */
  z-index: -1; /* Make sure it stays behind the content */
}
.container {
    max-width: 800px;
    width: 100%;
    height: auto;
    min-height: 400px;
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


.container .cover{
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
.container #flip:checked ~ .cover{
  transform: rotateY(-180deg);
}
 .container .cover .front,
 .container .cover .back{
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
}
.cover .back{
  transform: rotateY(180deg);
  backface-visibility: hidden;
}
.container .cover::before,
.container .cover::after{
  content: '';
  position: absolute;
  height: 100%;
  width: 100%;
  background: rgb(202, 6, 6);
  opacity: 0.5;
  z-index: 12;
}
.container .cover::after{
  opacity: 0.3;
  transform: rotateY(180deg);
  backface-visibility: hidden;
}
.container .cover img{
  position: absolute;
  height: 100%;
  width: 100%;
  object-fit: cover;
  z-index: 10;
}
.container .cover .text{
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
.cover .text .text-2{
  font-size: 26px;
  font-weight: 600;
  color: #fff;
  text-align: center;
}
.cover .text .text-2{
  font-size: 15px;
  font-weight: 500;
}
.container .forms{
  height: 100%;
  width: 100%;
  background: #fff;
}
.container .form-content{
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.form-content .login-form,
.form-content .signup-form{
  width: calc(100% / 2 - 25px);
}
.forms .form-content .title{
  position: relative;
  font-size: 24px;
  font-weight: 500;
  color: #333;
}
.forms .form-content .title:before{
  content: '';
  position: absolute;
  left: 0;
  bottom: 0;
  height: 3px;
  width: 25px;
  background: rgb(202, 6, 6);
}
.forms .signup-form  .title:before{
  width: 20px;
}
.forms .form-content .input-boxes{
  margin-top: 30px;
}
.forms .form-content .input-box{
  display: flex;
  align-items: center;
  height: 50px;
  width: 100%;
  margin: 10px 0;
  position: relative;
}
.form-content .input-box input{
  height: 100%;
  width: 100%;
  outline: none;
  border: none;
  padding: 0 30px;
  font-size: 16px;
  font-weight: 500;
  border-bottom: 2px solid rgba(0,0,0,0.2);
  transition: all 0.3s ease;
  text-transform: none;
}
.form-content .input-box input:focus,
.form-content .input-box input:valid{
  border-color:  rgb(202, 6, 6);
}
.form-content .input-box i{
  position: absolute;
  color:  rgb(202, 6, 6);
  font-size: 17px;
}
.forms .form-content .text{
  font-size: 14px;
  font-weight: 500;
  color: #333;
}
.forms .form-content .text a{
  text-decoration: none;
  
}
.forms .form-content .text a:hover{
  text-decoration: underline;
}
.forms .form-content .button{
  color: #fff;
  margin-top: 40px;
}
.forms .form-content .button input{
  color: #fff;
  background:  rgb(202, 6, 6);
  border-radius: 6px;
  padding: 0;
  cursor: pointer;
  transition: all 0.4s ease;
}
.forms .form-content .button input:hover{
  background:  rgb(202, 6, 6);
}
.forms .form-content label{
  color:  rgb(202, 6, 6);
  cursor: pointer;
}
.forms .form-content label:hover{
  text-decoration: underline;
}
.forms .form-content .login-text,
.forms .form-content .sign-up-text{
  text-align: center;
  margin-top: 25px;
}
.container #flip{
  display: none;
}
@media (max-width: 730px) {
  .container .cover{
    display: none;
  }
  .form-content .login-form,
  .form-content .signup-form{
    width: 100%;
  }
  .form-content .signup-form{
    display: none;
  }
  .container #flip:checked ~ .forms .signup-form{
    display: block;
  }
  .container #flip:checked ~ .forms .login-form{
    display: none;
  }
}

.forms .form-content .input-box {
    display: flex;
    align-items: center;
    height: 50px;
    width: 100%;
    margin: 10px 0;
    position: relative;
}

.forms .form-content .input-box input[type="checkbox"] {
    margin-right: 20px;
    cursor: pointer;
}

.forms .form-content .input-box label {
    color: #333;
    font-size: 20px;
}

.forms .form-content .input-box label a {
    color: #333;
    text-decoration: underline;
}

.input-box label {
    display: flex;
    align-items: center;
}

.input-box span {
    font-size: 15px; /* Adjust the font size as needed */
 
}
.forms .form-content .button input:hover {
  background: BLACK; /* Change the background color on hover */
}
.input-box {
  position: relative;
}

#togglePassword {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  background-color: transparent;
  border: none;
  cursor: pointer;
}

#togglePassword:focus {
  outline: none;
}

#togglePassword i {
  color: #999;
}

#togglePassword:hover i {
  color: #555;
}

.input-box {
  position: relative;
}

#togglePassword,
#toggleConfirmPassword {
  position: absolute;
  right: 30px;
  top: 30%;
  transform: translateY(-50%);
  background-color: transparent;
  border: none;
  cursor: pointer;
}

#togglePassword:focus,
#toggleConfirmPassword:focus {
  outline: none;
}

#togglePassword i,
#toggleConfirmPassword i {
  color: #999;
}

#togglePassword:hover i,
#toggleConfirmPassword:hover i {
  color: #555;
}
#password-strength {
  position: absolute;
  left: 230px; /* Adjust this value to move the indicator more to the left */
  bottom: 200px; /* Adjust this value to change the vertical position if needed */
}
#password-match-message{
  position: absolute;
  left: 210px; /* Adjust this value to move the indicator more to the left */
  bottom: 20px; /* Adjust this value to change the vertical position if needed */
}
.submit-btn {
  width: 100%;
  height: 100%;
  color: #fff;
  background: rgb(202, 6, 6);
  border: none;
  border-radius: 6px;
  padding: 0;
  cursor: pointer;
  transition: all 0.4s ease;
  font-size: 16px;
  font-weight: 500;
}

.submit-btn:hover {
  background: BLACK;
}

.role-select {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  background-color: white;
  font-size: 16px;
}

.input-box select {
  height: 100%;
  width: 100%;
  outline: none;
  border: none;
  padding: 0 30px;
  font-size: 16px;
  font-weight: 500;
  border-bottom: 2px solid rgba(0,0,0,0.2);
  transition: all 0.3s ease;
}

.input-box select:focus {
  border-color: #E8BC0E;
}
  </style>
  