<template>
  <div>
    <!-- Your dashboard template content -->
    <h1>Welcome to your Dashboard</h1>
  </div>
</template>

<script>
export default {
  created() {
    const userRole = localStorage.getItem('role');  // Assuming role is stored in local storage
    if (userRole === 'admin') {
      this.$router.push('/dashboard/admin'); // Route to admin dashboard
    } else if (userRole === 'donor') {
      this.$router.push('/dashboard/donor'); // Route to donor dashboard
    } else if (userRole === 'foodbank') {
      this.$router.push('/dashboard/foodbank'); // Route to foodbank dashboard
    } else {
      this.$router.push('/login'); // Route to login page if no role is found
    }
  }
};
</script>

<style scoped>
/* Add any specific styling for your dashboard */
</style>
