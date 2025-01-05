<template>
  <form @submit.prevent="submitRequest">
    <input v-model="form.food_type" placeholder="Food Type" />
    <input v-model="form.quantity" placeholder="Quantity" />
    <button type="submit">Submit Request</button>
  </form>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return { form: { food_type: '', quantity: '' } };
  },
  methods: {
    async submitRequest() {
      await axios.post('/api/foodbank/request', this.form);
      alert('Request submitted');
    },
  },
};
</script>
