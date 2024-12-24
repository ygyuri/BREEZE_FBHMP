<template>
    <div id="app">
      <!-- App Layout -->
      <header>
        <nav>
          <!-- Example navigation, customize as needed -->
          <router-link to="/">Home</router-link>
          <router-link to="/about">About</router-link>
        </nav>
      </header>

      <!-- Main Content -->
      <main>
        <router-view />
      </main>

      <!-- Footer -->
      <footer>
        <p>&copy; {{ currentYear }} My Laravel Vue App. All rights reserved.</p>
      </footer>
    </div>
  </template>

  <script>
  export default {
    name: 'App',
    data() {
      return {
        currentYear: new Date().getFullYear(),
      };
    },
  };
  </script>

  <style scoped>
  /* Basic Styling */
  #app {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  header {
    background-color: #4f46e5; /* Example primary color */
    color: white;
    padding: 1rem;
  }

  nav {
    display: flex;
    gap: 1rem;
  }

  nav a {
    color: white;
    text-decoration: none;
  }

  nav a:hover {
    text-decoration: underline;
  }

  main {
    flex: 1;
    padding: 2rem;
  }

  footer {
    background-color: #1e293b; /* Example secondary color */
    color: white;
    text-align: center;
    padding: 1rem;
  }
  </style>
