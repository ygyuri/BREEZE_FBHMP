// resources/js/routes.js

import Dashboard from './components/Dashboard.vue';
import Login from './components/Login.vue';
import Profile from './components/Profile.vue';
import Welcome from './components/Welcome.vue';

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
