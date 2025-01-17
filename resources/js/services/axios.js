import axios from 'axios';

// Create Axios instance with default configurations
const instance = axios.create({
    baseURL: '/api', // Use Laravel Breeze's API prefix or your actual API backend URL
    timeout: 5000,
    withCredentials: true, // This ensures cookies (for stateful authentication) are sent with requests
});

// Add Authorization header if token exists in localStorage
const token = localStorage.getItem('auth_token');
if (token) {
    instance.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

// Optionally, set the CSRF token for requests (if it's stored in a meta tag or cookie)
const csrfToken = document.head.querySelector('meta[name="csrf-token"]');
if (csrfToken) {
    instance.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
}

export default instance;
