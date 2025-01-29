<!-- <template>
  <q-card>
    <q-card-section>
      <h2 class="q-mb-md">Reset Password</h2>
      <q-form @submit.prevent="resetPassword">
        <q-input
          filled
          v-model="email"
          label="Email"
          type="email"
          required
          lazy-rules
          :rules="[val => !!val || 'Email is required']"
          disable
        />
        <q-input
          filled
          v-model="token"
          label="Token"
          required
          lazy-rules
          :rules="[val => !!val || 'Token is required']"
          disable
        />
        <div style="display: flex; width: 100%; ">
        <q-input
        style="width: 100%;"
          filled
          v-model="password"
          label="New Password"
          :type="passwordVisible ? 'text' : 'password'"
          required
          :rules="[val => !!val || 'Password is required', passwordStrength]"
        />
        <q-btn  style=" background-color: #f2f2f2;  height: 55px;" dense  :icon="passwordVisible ? 'visibility_off' : 'visibility'" size="15px" class="q-pa-md q-ml-sm" @click="togglePasswordVisibility" />
        </div>
        <q-input
          filled
          v-model="password_confirmation"
          label="Confirm Password"
          :type="passwordVisible ? 'text' : 'password'"
          required
          :rules="[val => !!val || 'Confirmation is required', val => val === password || 'Passwords do not match']"
        />
        <q-btn type="submit" @click="resetPassword()" label="Reset Password" color="primary" class="full-width" />
      </q-form>
      <div v-if="message" class="q-mt-md">
        <q-banner v-if="success" color="green">{{ message }}</q-banner>
        <q-banner v-else color="red">{{ message }}</q-banner>
      </div>
      <div v-if="password" class="password-strength q-mt-md" :style="{ color: strengthColor }">
        <p>Password Strength: {{ strengthMessage }}</p>
      </div>
    </q-card-section>
  </q-card>
</template> -->


<template>
  <q-layout>
    <q-page-container>
      <q-page class="flex bg-image flex-center" >
        <q-card  :style="$q.screen.lt.sm ? { width: '80%' } : { width: '30%' }">

          <q-card>
            <q-card-section>
            <div class="text-center q-pt-lg">
              <img src="logo.png" width="200" />
            </div>
          </q-card-section>

    <q-card-section>
      <div class="q-mb-md text-h4" style="font-weight: 500; color: #293041;">Reset Password</div>
      <q-form @submit.prevent="resetPassword">
        <q-input
          filled
          v-model="email"
          label="Email"
          type="email"
          required
          lazy-rules
          :rules="[val => !!val || 'Email is required']"
          disable
        />
        <q-input
          filled
          v-model="token"
          label="Token"
          required
          lazy-rules
          :rules="[val => !!val || 'Token is required']"
          disable
        />
        <div style="display: flex; width: 100%; ">
        <q-input
        style="width: 100%;"
          filled
          v-model="password"
          label="New Password"
          :type="passwordVisible ? 'text' : 'password'"
          required
          :rules="[val => !!val || 'Password is required', passwordStrength]"
        />
        <q-btn  style=" background-color: #f2f2f2;  height: 55px;" dense  :icon="passwordVisible ? 'visibility_off' : 'visibility'" size="15px" class="q-pa-md q-ml-sm" @click="togglePasswordVisibility" />
        </div>
        <q-input
          filled
          v-model="password_confirmation"
          label="Confirm Password"
          :type="passwordVisible ? 'text' : 'password'"
          required
          :rules="[val => !!val || 'Confirmation is required', val => val === password || 'Passwords do not match']"
        />
        <q-btn type="submit" @click="resetPassword()" label="Reset Password" color="primary" class="full-width" />
      </q-form>
      <div v-if="message" class="q-mt-md">
        <q-banner v-if="success" color="green">{{ message }}</q-banner>
        <q-banner v-else color="red">{{ message }}</q-banner>
      </div>
      <div v-if="password" class="password-strength q-mt-md" :style="{ color: strengthColor }">
        <p>Password Strength: {{ strengthMessage }}</p>
      </div>
    </q-card-section>
  </q-card>
          </q-card>
      
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script>
import { defineComponent, ref } from 'vue';
import axios from 'axios';

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

export default defineComponent({
  name: 'ResetPasswordPage',
  data() {
    return {
      email: '',
      token: '',
      password: '',
      password_confirmation: '',
      message: '',
      success: false,
      passwordVisible: false,
    };
  },
  computed: {
    strengthMessage() {
      return this.passwordStrength(this.password);
    },
    strengthColor() {
      const strength = this.strengthMessage;
      if (strength === 'Strong') {
        return 'green';
      } else if (strength === 'Weak') {
        return 'orange';
      } else {
        return 'red';
      }
    },
  },
  mounted() {
    // Automatically retrieve email and token from the URL
    const urlParams = new URLSearchParams(window.location.search);
    this.email = urlParams.get('email') || ''; // Set email from URL query param
    this.token = urlParams.get('token') || ''; // Set token from URL query param
  },
  methods: {
    async resetPassword() {
      try {
        const response = await axios.post(`${apiBaseUrl}/reset-password`, {
          email: this.email,
          token: this.token,
          password: this.password,
          password_confirmation: this.password_confirmation,
        });

        this.message = response.data.message; // Display success message
        this.success = true; // Set success state
        alert(this.message);
        alert("Please Login Again");
        this.$router.push('/login');
      } catch (error) {
        // Handle errors
        if (error.response) {
          this.message = error.response.data.message; // Display error message from server
          this.success = false; // Set error state
        } else {
          this.message = 'An error occurred. Please try again.';
          this.success = false; // Set error state
        }
      }
    },
    togglePasswordVisibility() {
      this.passwordVisible = !this.passwordVisible;
    },
    passwordStrength(password) {
      const strongPasswordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*]).{8,}$/;
      const weakPasswordPattern = /^(?=.*[a-z]).{6,}$/;

      if (strongPasswordPattern.test(password)) {
        return 'Strong';
      } else if (weakPasswordPattern.test(password)) {
        return 'Weak';
      } else {
        return 'Very Weak';
      }
    },
  },
});
</script>

<style scoped>
.password-strength {
  font-weight: bold;
}

.bg-image {
  background-color: #7f1416;
}
</style>
