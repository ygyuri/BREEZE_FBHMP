import { defineStore } from 'pinia';
import axios from '../services/axios'; // Use a dedicated Axios instance.

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('auth_token') || null,
    loading: false,
    error: null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
  actions: {
    setAuthToken(token) {
      this.token = token;
      localStorage.setItem('auth_token', token);
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    },
    clearAuthToken() {
      this.token = null;
      localStorage.removeItem('auth_token');
      delete axios.defaults.headers.common['Authorization'];
    },
    async login(credentials) {
      this.loading = true;
      this.error = null;
      try {
        const { data } = await axios.post('/api/login', credentials);
        this.setAuthToken(data.token);
        await this.fetchUser();
      } catch (error) {
        this.error = error.response?.data?.message || 'Login failed';
      } finally {
        this.loading = false;
      }
    },
    async register(userData) {
      this.loading = true;
      this.error = null;
      try {
        const { data } = await axios.post('/api/register', userData);
        this.setAuthToken(data.token);
        await this.fetchUser();
      } catch (error) {
        this.error = error.response?.data?.message || 'Registration failed';
      } finally {
        this.loading = false;
      }
    },
    async fetchUser() {
      if (!this.token) return;
      try {
        const { data } = await axios.get('/api/user');
        this.user = data;
      } catch (error) {
        this.logout(); // Clear session if the token is invalid
      }
    },
    logout() {
      this.clearAuthToken();
      this.user = null;
    },
  },
});
