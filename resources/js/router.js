import { createRouter, createWebHistory } from 'vue-router';

// Import your Vue components
import HomePage from './components/HomePage.vue';
import AboutUs from './components/AboutUs.vue';
import Login from './components/Login.vue';
import Register from './components/Register.vue';
import Order from '@/components/Order.vue';
import Checkout from '@/components/Checkout.vue';
import Delivery from '@/components/Delivery.vue';
import Receipt from '@/components/Receipt.vue';
import MyProfile from './components/Profile/MyProfile.vue';
import ProfileSettings from './components/Profile/ProfileSettings.vue';
import OrderHistory from './components/Profile/OrderHistory.vue';
import CreateProduct from './components/Profile/CreateProduct.vue';
import OrderDetails from './components/Profile/OrderDetails.vue';
import MyProducts from './components/Profile/MyProducts.vue';
import AdminLogin from './components/admin/AdminLogin.vue';
import AdminPanel from './components/admin/AdminPanel.vue';
import AdminDashboard from './components/admin/AdminDashboard.vue';
import Users from './components/admin/Users.vue';
import Employees from './components/admin/Employees.vue';
import Customers from './components/admin/Customers.vue';
import Orders from './components/admin/Orders.vue';
import Products from './components/admin/Products.vue';
import OrderDetailsAdmin from './components/admin/OrderDetails.vue';
import SellerApplications from './components/admin/SellerApplications.vue';
import Pending from './components/Pending.vue';

import axios from 'axios';

axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const routes = [
  // Admin routes must come first
  {
    path: '/admin/login',
    name: 'AdminLogin',
    component: AdminLogin
  },
  {
    path: '/admin',
    component: AdminPanel,
    meta: { requiresAdmin: true },
    children: [
      {
        path: '',
        name: 'AdminDashboard',
        component: AdminDashboard
      },
      {
        path: 'users',
        name: 'AdminUsers',
        component: Users
      },
      {
        path: 'employees',
        name: 'AdminEmployees',
        component: Employees
      },
      {
        path: 'customers',
        name: 'AdminCustomers',
        component: Customers
      },
      {
        path: 'orders',
        name: 'AdminOrders',
        component: Orders
      },
      {
        path: 'products',
        name: 'AdminProducts',
        component: Products
      },
      {
        path: 'order-details',
        name: 'AdminOrderDetails',
        component: OrderDetailsAdmin
      },
      {
        path: 'seller-applications',
        name: 'AdminSellerApplications',
        component: SellerApplications
      }
    ]
  },
  // Regular routes
  {
    path: '/',
    name: 'Home',
    component: HomePage
  },
  {
    path: '/about-us',
    name: 'AboutUs',
    component: AboutUs
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/register',
    name: 'Register',
    component: Register
  },
  {
    path: '/order',
    name: 'Order',
    component: Order
  },
  {
    path: '/checkout',
    name: 'Checkout',
    component: Checkout
  },
  {
    path: '/delivery',
    name: 'Delivery',
    component: Delivery
  },
  {
    path: '/profile',
    name: 'profile',
    component: MyProfile,
    meta: { requiresAuth: true }
  },
  {
    path: '/profile-settings',
    name: 'profile-settings',
    component: ProfileSettings,
    meta: { requiresAuth: true }
  },
  {
    path: '/order-history',
    name: 'order-history',
    component: OrderHistory,
    meta: { requiresAuth: true }
  },
  {
    path: '/create-product',
    name: 'CreateProduct',
    component: CreateProduct,
    meta: { requiresAuth: true }
  },
  {
    path: '/my-products',
    name: 'MyProducts',
    component: MyProducts,
    meta: { requiresAuth: true }
  },
  {
    path: '/pending',
    name: 'Pending',
    component: Pending
  },
  {
    path: '/receipt',
    name: 'Receipt',
    component: Receipt
  },
  {
    path: '/order-details/:id',
    name: 'OrderDetails',
    component: OrderDetails,
    meta: { requiresAuth: true }
  }
];

const router = createRouter({
  history: createWebHistory('/'),
  routes
});

router.beforeEach((to, from, next) => {
  // Check if the route is admin login first
  if (to.name === 'AdminLogin') {
    next();
    return;
  }

  // Handle admin routes
  if (to.path.startsWith('/admin')) {
    const isAdmin = localStorage.getItem('isAdmin') === 'true';
    if (!isAdmin) {
      next({ name: 'AdminLogin' });
    } else {
      next();
    }
    return;
  }

  // Handle regular routes
  if (to.meta.requiresAuth) {
    const loggedIn = localStorage.getItem('user');
    if (!loggedIn) {
      next('/login');
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router;
