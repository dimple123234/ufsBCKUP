import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import store from './store/delivery';
import 'font-awesome/css/font-awesome.min.css';
import axios from 'axios';

// Configure axios defaults
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

// Get CSRF token from meta tag and set it as a default header
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}

// Add CSRF token to all requests
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

// Add withCredentials for authentication
axios.defaults.withCredentials = true;

router.beforeEach((to, from, next) => {
  // If trying to access admin routes
  if (to.path.startsWith('/admin')) {
    if (to.path === '/admin/login') {
      next();
      return;
    }
    const isAdmin = localStorage.getItem('isAdmin') === 'true';
    if (!isAdmin) {
      next('/admin/login');
      return;
    }
    next();
    return;
  }

  // For non-admin routes
  const publicPages = ['/login', '/register', '/', '/about-us'];
  const authRequired = !publicPages.includes(to.path);
  const loggedIn = localStorage.getItem('user');

  if (authRequired && !loggedIn) {
    next('/login');
    return;
  }

  next();
});

const app = createApp(App);
app.use(router);
app.use(store);

app.config.globalProperties.$axios = axios;

app.mount('#app');
