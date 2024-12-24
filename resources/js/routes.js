// resources/js/routes.js

import Login from './components/Login.vue';
import Dashboard from './components/Dashboard.vue';
import Welcome from './components/Welcome.vue';
import Profile from './components/Profile.vue';

const routes = [
  {
    path: '/',
    name: 'welcome',
    component: Welcome,
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }, // Protect this route with authentication middleware
  },
  {
    path: '/profile',
    name: 'profile',
    component: Profile,
    meta: { requiresAuth: true },
  },
];

export default routes;
