<template>
  <div id="app">
    <router-view></router-view> <!-- Main content based on routing -->
  </div>
</template>

<script>
export default {
  created() {
    const userRole = localStorage.getItem('role');  // Assuming role is stored in local storage
    if (userRole === 'admin') {
      this.$router.push('/dashboard/admin');
    } else if (userRole === 'donor') {
      this.$router.push('/dashboard/donor');
    } else if (userRole === 'foodbank') {
      this.$router.push('/dashboard/foodbank');
    } else {
      this.$router.push('/login'); // Redirect to login page if no valid role
    }
  }
};
</script>
