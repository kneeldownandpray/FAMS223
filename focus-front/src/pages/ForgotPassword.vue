<template>
  <q-card>
    <q-card-section>
      <q-card-section>
        <div class="text-center">
          <img src="logo.png" width="200" />
        </div>
      </q-card-section>

      <div>Forgot Password</div>
      <q-form @submit.prevent="sendResetCode">
        <q-input
          filled
          v-model="email"
          label="Email"
          type="email"
          required
          lazy-rules
          :rules="[val => !!val || 'Email is required']"
        />
        <q-btn type="submit" label="Send Reset Code" color="primary" class="full-width" :loading="loading" :disable="loading" />
      </q-form>
      <div v-if="message" class="q-mt-md" :class="success ? 'text-green' : 'text-red'">
        <q-banner v-if="success" color="green">{{ message }}</q-banner>
        <q-banner v-else color="red">{{ message }}</q-banner>
      </div>
    </q-card-section>
  </q-card>
</template>

<script>
import { defineComponent, ref } from 'vue';
import axios from 'axios';

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

export default defineComponent({
  name: 'ForgotPasswordPage',
  data() {
    return {
      email: '',
      message: '',
      success: false,
      loading: false, // Loading state
    };
  },
  methods: {
    async sendResetCode() {
      this.loading = true; // Set loading to true
      this.message = ''; // Reset message before sending the request
      try {
        const response = await axios.post(`${apiBaseUrl}/forgot-password`, {
          email: this.email,
        });

        this.message = response.data.message; // Display success message
        this.success = true; // Set success state
        this.$emit('SuccessEmailSent', this.success);
      } catch (error) {
        // Handle errors
        if (error.response) {
          this.message = error.response.data.message; // Display error message from server
          this.success = false; // Set error state
        } else {
          this.message = 'An error occurred. Please try again.';
          this.success = false; // Set error state
        }
      } finally {
        this.loading = false; // Set loading to false regardless of success or error
      }
    },
  },
});
</script>

<style scoped>
.text-green {
  color: green;
}
.text-red {
  color: red;
}
</style>
