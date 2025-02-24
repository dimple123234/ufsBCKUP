import axios from 'axios';

// Add a request interceptor to include the CSRF token
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

// Set the base URL and credentials
axios.defaults.baseURL = '/';
axios.defaults.withCredentials = true;

export default axios;
