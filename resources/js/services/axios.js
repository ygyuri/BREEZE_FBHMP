import axios from 'axios';

const instance = axios.create({
  baseURL: '/api', // Use Laravel Breeze's API prefix
  timeout: 5000,
});

// Add default Authorization header if token exists
const token = localStorage.getItem('auth_token');
if (token) {
  instance.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

export default instance;
