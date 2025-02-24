<template>
  <div class="main-content" :class="{ 'move-right': isCartOpen, 'move-left': isSidebarOpen }" id="main-content">
    <!-- Navigation Bar -->
    <nav>
      <div class="logo">Order Menu</div>
      <ul>
        <li><a href="#" @click="filterProducts('')">All Products</a></li>
        <li><a href="#" @click="filterProducts('McDonalds')">McDonalds</a></li>
        <li><a href="#" @click="filterProducts('Jollibee')">Jollibee</a></li>
        <li><a href="#" @click="filterProducts('Chowking')">Chowking</a></li>
        <li><a href="#" @click="filterProducts('Mang Inasal')">Mang Inasal</a></li>
        <li><a href="#" @click="filterProducts('Greenwich')">Greenwich</a></li>
        <li><a href="#" @click="filterProducts('Crispy King')">Crispy King</a></li>
      </ul>
    </nav>

    <div class="container">
      <header>
        <div class="profile">
          <img 
            :src="profilePictureUrl" 
            alt="Profile" 
            @click="toggleSidebar"
            @error="handleImageError"
          />
          <span class="profile-name">Welcome, {{ currentUser ? currentUser.name : 'Guest' }}</span>
        </div>
        <div v-if="isCustomer" class="right-actions">
          <div v-if="isActive" class="delivery-status" @click="goToDelivery">
            <div class="status-badge">
              <i class="fas fa-motorcycle"></i>
              <span class="badge-text">{{ deliveryMessage }}</span>
            </div>
          </div>
          <div v-else class="shopping" @click="toggleCart">
            <img src="images/shopping.svg" alt="Cart" />
            <span class="quantity">{{ cart.length }}</span>
          </div>
        </div>
      </header>

      <!-- Filter and Search Options -->
      <div class="filter-options">
        <input type="text" v-model="keyword" placeholder="Enter keyword" @keyup="searchProducts" />
        <p>ex. Jollibee, Mcdo, Mang Inasal, Crispy King, Greenwich, etc </p>
      </div>

      <!-- Product List -->
      <div class="list" id="product-list">
        <div v-for="product in filteredProducts" :key="product.id" class="item">
          <img :src="'/storage/' + product.image" :alt="product.name" />
          <h2>{{ product.name }}</h2>
          <p class="price">₱{{ parseFloat(product.price).toFixed(2) }}</p>
          <div class="seller-info">
            <div class="badge-container">
              <img 
                :src="product.seller_profile ? '/storage/profile_pictures/' + product.seller_profile : '/images/admin.png'"
                :alt="product.seller_name"
                class="seller-badge"
                @error="$event.target.src = '/images/admin.png'"
              />
            </div>
            <p class="seller">Seller: {{ product.seller_name }}</p>
          </div>
          <button v-if="isCustomer" @click="addToCart(product, $event)">Add to Cart</button>
        </div>
      </div>
    </div>

    <!-- Cart Sidebar - Only for customers -->
    <div v-if="isCustomer" class="cart" :class="{ active: isCartOpen }" id="cart">
  <h1>Cart</h1>
  <div class="listCart">
    <div v-for="(item, index) in cart" :key="index" class="item">
      <img :src="'/storage/' + item.image" :alt="item.name" />
      <div class="item-details">
        <div class="name">{{ item.name }}</div>
        <div class="price">₱{{ parseFloat(item.price).toFixed(2) }}</div>
      </div>
      <div class="quantity-controls">
        <button @click="decreaseQuantity(index)">-</button>
        <span>{{ item.quantity }}</span>
        <button @click="increaseQuantity(index)">+</button>
      </div>
       <!-- Total Price Section -->
  <div class="total-price">Total: ₱{{ (item.price * item.quantity).toFixed(2) }}</div>
    </div>
    <div class="total-price">Total: ₱{{ cartTotal }}</div>
  </div>
  <div class="buttons">
    <div class="close" @click="toggleCart">CLOSE</div>
    <div class="checkOut" @click="checkout">Checkout</div>
  </div>
</div>


    <!-- Profile Sidebar -->
    <div class="sidebar" :class="{ active: isSidebarOpen }" id="sidebar">
      <router-link to="/profile">My Profile</router-link>
      <router-link v-if="isSeller" to="/my-products">My Products</router-link>
      <router-link to="/profile-settings">Profile Settings</router-link>
      <a href="#" @click="logout" class="logout-btn">Logout</a>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { mapState } from 'vuex';

export default {
  data() {
    return {
      products: [],  // Products array initialized
      cart: [],
      keyword: '',
      isCartOpen: false,
      isSidebarOpen: false,
      selectedBrand: '',
      currentUser: null,
      profilePictureUrl: '/images/admin.png', // Add this default
    };
  },
  computed: {
    ...mapState({
      deliveryMessage: state => state.deliveryMessage,
      isActive: state => state.isActive
    }),
    filteredProducts() {
      const filterText = this.keyword.toUpperCase();
      return this.products.filter(product => {
        return (
          product.name.toUpperCase().includes(filterText) &&
          (this.selectedBrand === '' || product.brand === this.selectedBrand)
        );
      });
    },
    cartTotal() {
    return this.cart.reduce((total, item) => {
      return total + item.price * item.quantity;
    }, 0).toFixed(2);
  },
    isCustomer() {
      return this.currentUser && this.currentUser.role === 'customer';
    },
    isSeller() {
      return this.currentUser && this.currentUser.role === 'seller';
    }
},
  methods: {
    toggleCart() {
      if (this.isSidebarOpen) this.isSidebarOpen = false;
      this.isCartOpen = !this.isCartOpen;
    },
    toggleSidebar() {
      if(this.isCartOpen) this.isCartOpen = false;
      this.isSidebarOpen = !this.isSidebarOpen;
    },
    searchProducts() {
      this.filterProducts(this.selectedBrand);
    },
    filteredProducts() {
    const filterText = this.keyword.toUpperCase();
    return this.products.filter(product => {
      const matchesKeyword = product.name.toUpperCase().includes(filterText);
      const matchesBrand = this.selectedBrand === '' || product.brand === this.selectedBrand;
      return matchesKeyword && matchesBrand;
    });
  },
    addToCart(product, event) {
      // Create a clone of the clicked product image
      const productImg = event.target.parentElement.querySelector('img');
      const clone = productImg.cloneNode(true);
      const rect = productImg.getBoundingClientRect();
      const cartIcon = document.querySelector('.shopping');
      const cartRect = cartIcon.getBoundingClientRect();

      // Style the clone
      clone.style.position = 'fixed';
      clone.style.top = rect.top + 'px';
      clone.style.left = rect.left + 'px';
      clone.style.width = rect.width + 'px';
      clone.style.height = rect.height + 'px';
      clone.style.transition = 'all 0.8s ease-in-out';
      clone.style.zIndex = '9999';
      clone.classList.add('flying-image');

      document.body.appendChild(clone);

      // Trigger the animation
      setTimeout(() => {
        clone.style.top = cartRect.top + 'px';
        clone.style.left = cartRect.left + 'px';
        clone.style.width = '30px';
        clone.style.height = '30px';
        clone.style.opacity = '0.5';
        clone.style.transform = 'scale(0.5)';
      }, 0);

      // Remove the clone after animation
      setTimeout(() => {
        clone.remove();
        // Add the product to cart
        const existingProduct = this.cart.find(item => item.id === product.id);
        if (existingProduct) {
          existingProduct.quantity++;
        } else {
          this.cart.push({
            ...product,
            quantity: 1
          });
        }
      }, 800);
    },
    increaseQuantity(index) {
      this.cart[index].quantity++;
    },
    decreaseQuantity(index) {
      if (this.cart[index].quantity > 1) {
        this.cart[index].quantity--;
      } else {
        this.cart.splice(index, 1);
      }
    },
    checkout() {
      localStorage.setItem('cart', JSON.stringify(this.cart));
      this.$router.push({ name: 'Checkout' });
    },
    handleImageError(e) {
      console.error('Image failed to load:', e.target.src);
      e.target.src = '/images/admin.png';
    },

    async fetchUserProfile() {
      try {
        const response = await axios.get('/api/profile');
        if (response.data && response.data.user) {
          this.profilePictureUrl = response.data.user.profile_picture_url || '/images/admin.png';
        }
      } catch (error) {
        console.error('Error fetching user profile:', error);
        this.profilePictureUrl = '/images/admin.png';
      }
    },
    async logout() {
      try {
        await axios.post('/logout');
        // Clear storage based on user role
        if (localStorage.getItem('isCustomer')) {
          localStorage.removeItem('customerUser');
          localStorage.removeItem('isCustomer');
        } else if (localStorage.getItem('isSeller')) {
          localStorage.removeItem('sellerUser');
          localStorage.removeItem('isSeller');
        }
        localStorage.removeItem('cart');
        this.$router.push('/login');
      } catch (error) {
        console.error('Logout error:', error);
      }
    },
    async fetchProducts() {
      try {
        const response = await axios.get('/products');
        this.products = response.data.map(product => ({
          ...product,
          price: parseFloat(product.price)
        }));
      } catch (error) {
        console.error('Error fetching products:', error);
      }
    },
    updateDeliveryStatus(status) {
      this.deliveryStatus = status.status === 'pending';
      this.deliveryMessage = status.message;
    },
    goToDelivery() {
      this.$router.push('/delivery');
    }
  },
  mounted() {
    this.fetchProducts();
  },
  created() {
    // Check for either customer or seller user data
    const customerData = localStorage.getItem('customerUser');
    const sellerData = localStorage.getItem('sellerUser');
    
    if (customerData) {
      this.currentUser = JSON.parse(customerData);
      console.log('Current user (customer):', this.currentUser);
      this.fetchUserProfile();
    } else if (sellerData) {
      this.currentUser = JSON.parse(sellerData);
      console.log('Current user (seller):', this.currentUser);
      this.fetchUserProfile();
    } else {
      this.$router.push('/login');
    }
  }
};
</script>


<style scoped>
        body {
            margin: 0;
            padding: 0;
            background-color: #E3E7E8;
            font-family: system-ui;
            overflow-x: hidden;
        }
        .container {
            width: 1000px;
            margin: auto;
            transition: 0.5s;
        }

        /* Navigation Bar Styles */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: red;
            padding: 38px;
        }
        nav .logo {
            color: #fff;
            font-size: 18px;
            font-weight: bold;
        }
        nav ul {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }
        nav ul li {
            margin-left: 20px;
        }
        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 15px;
            transition: color 0.3s;
        }
        nav ul li a:hover {
            color: #E8BC0E;
        }

        .profile img {
            width:70px;
            border-radius: 40%;
            cursor: pointer;
        }
        .profile img:hover {
            transform: scale(1.05); /* Optional hover effect */
        }

        /* Existing Styles */
        header {
            display: grid;
            grid-template-columns: 1fr 50px;
            margin-top: 50px;
        }
        .profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .profile img {
            width: 40px;
            border-radius: 50%;
            cursor: pointer;
        }
        .profile-name {
            font-weight: bold;
        }
        .right-actions {
          margin-left: auto;
          display: flex;
          align-items: center;
        }
        .shopping {
            position: relative;
            text-align: right;
            cursor: pointer;
        }
        .shopping img {
            width: 40px;
            cursor: pointer;
        }
        .shopping img:hover { 
            opacity: 0.8; 
            transform: scale(1.05); /* Optional hover effect */
        }
        .shopping span {
            background: red;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            position: absolute;
            top: -5px;
            left: 80%;
            padding: 3px 10px;
        }
        .list {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            column-gap: 50px;
            row-gap: 20px;
            justify-content: center;
            margin-top: 100px;
        }
        
        .list .item {
            text-align: center;
            background-color: #DCE0E1;
            padding: 20px;
            box-shadow: 0 50px 50px #757676;
            letter-spacing: 1px;
            transition: transform 0.3s; /* Optional hover effect */
        }
        .list .item:hover {
            transform: scale(1.05); /* Optional hover effect */
        }
        .list .item img {
            width: 300px;
            height: 300px;
            object-fit: cover;
        }
        .list .item .title {
            font-weight: 6000px;
        }
        .list .item .price {
            margin: 10px;
        }
        .list .item button {
            background-color: #1C1F25;
            color: #fff;
            width: 100%;
            padding: 10px;
            cursor: pointer;
        }
        .list .item button:hover { 
            opacity: 0.8; 
        }
        .cart {
            color: #fff;
            position: fixed;
            top: 0;
            right: -100%; /* Initially hidden off-screen */
            width: 400px;
            height: 100vh;
            background-color: #0e0f11;
            display: grid;
            grid-template-rows: 50px 1fr 50px;
            gap: 20px;
            transition: right 0.3s ease; /* Matching transition duration with sidebar */
        }

        .cart.active {
            right: 0; /* Open cart */
        }

        .cart h1 {
            color: #E8BC0E;
            padding: 20px;
            margin: 0;
            flex-shrink: 0;         /* Prevents header from shrinking */
        }
        .cart .listCart {
            flex-grow: 1;           /* Allows list to take remaining space */
            overflow-y: auto;       /* Makes the list scrollable */
            padding: 0 20px;        /* Added padding for better look */
            margin-bottom: 10px;    /* Space before buttons */
        }
        .cart .listCart .item {
            display: grid;
            grid-template-columns: 80px 2fr 150px;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            background-color: #E8BC0E;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .cart .listCart img {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }

        .cart .listCart .item .name {
            font-weight: bold;
            font-size: 1.2em;
            color: black;
        }

        .cart .listCart .item .price {
            color: black;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
        }

        .quantity-controls button {
            padding: 5px 12px;
            font-size: 16px;
            cursor: pointer;
            background-color: #1C1F25;
            color: white;
            border: none;
            border-radius: 5px;
        }

        .quantity-controls button:hover {
            background-color: red;
        }

        .quantity-controls span {
            font-weight: bold;
            font-size: 16px;
            color: black;
        }

        .cart .total-price {
            font-weight: bold;
            font-size: 1.2em;
            color: black;
            padding: 20px;
            text-align: right;
            background-color: #E8BC0E;
            border-radius: 10px;
            margin-bottom: 10px;
          }
        .cart .buttons {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            text-align: center;
        }
        .cart .buttons div {
            background-color: #000;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            cursor: pointer;
        }
        .cart .buttons .checkOut {
            background-color: #E8BC0E;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            height: 100%;
            position: fixed;
            top: 0;
            left: -250px;
            background-color: #111;
            transition: left 0.10s ease; /* Smooth transition for sidebar */
            padding-top: 60px;
        }
        .sidebar a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
        }
        .sidebar a:hover {
            background-color: #575757;
        }
        .sidebar.active {
            left: 0;
        }
        .logout-btn {
            background-color: #ff0000;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
        }
        .logout-btn:hover {
            background-color: #cc0000;
        }
        .main-content {
            transition: transform 0.3s ease; /* Add transition for movement */
        }
       /* .main-content.move-left {
            transform: translateX(250px); /* Shift right to make space for the sidebar 
        } */

/* Transform the main content when the cart is open */
    /*    .main-content.move-right {
            transform: translateX(-400px); /* Shift left to make space for the cart 
        } */
        .filter-options {
            display: flex;
            flex-direction: column;
            gap: 10px; /* Space between input and description */
            max-width: 300px;
            margin-left: -20px;
            margin-top: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
 
          }

        .filter-options input {
          padding: 10px;
          font-size: 10px;
          border: 2px solid black;
          border-radius: 4px;
          outline: none;
          transition: border-color 0.3s ease;
        }

        .filter-options input:focus {
          border-color: red; /* Highlight the input on focus */
        }

        .filter-options p {
        font-size: 10px;
        color: black;
        margin-top: 5px;
        line-height: 1.5;
      }

      .filter-options input::placeholder {
        color: #aaa; /* Placeholder text color */
      }

.access-denied {
  text-align: center;
  padding: 50px;
  margin: 50px auto;
  max-width: 500px;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.access-denied h2 {
  color: #ff0000;
  margin-bottom: 20px;
}

.access-denied a {
  display: inline-block;
  margin-top: 20px;
  padding: 10px 20px;
  background: #E8BC0E;
  color: #fff;
  text-decoration: none;
  border-radius: 5px;
}

.item {
  background: white;
  border-radius: 8px;
  padding: 15px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  transition: transform 0.2s;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
}

.item img {
  width: 200px;
  height: 200px;
  object-fit: cover;
  border-radius: 8px;
}

.item h2 {
  margin: 10px 0;
  font-size: 2.2em;
  text-align: center;
}

.price {
  color: #4CAF50;
  font-weight: bold;
  font-size: 1.2em;
}

.seller {
  color: #666;
  font-size: 1.3em;
}

button {
  background: #4CAF50;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  transition: background 0.2s;
}

button:hover {
  background: #45a049;
}

.delivery-status {
  display: flex;
  align-items: center;
  margin: 0 20px;
  animation: slideIn 0.5s ease-out;
}

.status-badge {
  background-color: #ffc107;
  color: #000;
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 8px;
  animation: pulse 2s infinite;
}

.status-badge i {
  font-size: 18px;
}

@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.05); }
  100% { transform: scale(1); }
}

@keyframes slideIn {
  from { transform: translateX(100%); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

.delivery-status {
  cursor: pointer;
  transition: transform 0.2s ease;
}

.delivery-status:hover {
  transform: scale(1.05);
}

.status-badge {
  background-color: #ffc107;
  color: #000;
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.status-badge:hover {
  background-color: #ffb300;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.status-badge i {
  font-size: 18px;
}

.badge-text {
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

@keyframes slideIn {
  from { transform: translateX(100%); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

.delivery-status {
  animation: slideIn 0.5s ease-out;
}

.seller-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin: 5px 0;
}

.badge-container {
  margin-bottom: 5px;
  width: 45px;
  height: 45px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.seller-badge {
  width: 45px !important;
  height: 45px !important;
  border-radius: 50% !important;
  object-fit: cover !important;
  border: 1px solid rgb(202, 6, 6) !important;
  display: block !important;
  min-width: 45px !important;
  min-height: 45px !important;
  max-width: 45px !important;
  max-height: 45px !important;
  padding: 0 !important;
  margin: 0 auto !important;
}

.seller {
  margin: 0;
  font-size: 14px;
  color: #666;
}

.flying-image {
  pointer-events: none;
  border-radius: 50%;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
}

.item {
  position: relative;
  transition: transform 0.3s ease;
}

.item:hover {
  transform: translateY(-5px);
}

.item button {
  transition: transform 0.2s ease, background-color 0.2s ease;
}

.item button:hover {
  transform: scale(1.05);
}

.item button:active {
  transform: scale(0.95);
}
</style>