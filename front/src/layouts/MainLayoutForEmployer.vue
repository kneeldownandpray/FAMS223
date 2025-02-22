<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          @click="toggleLeftDrawer"
          icon="menu"
          aria-label="Menu"
        />
        Asdasd
        <q-toolbar-title>
          {{ first_name }}
        </q-toolbar-title>
        <q-space />
        <div class="q-gutter-sm row items-center no-wrap">
          <q-btn
            round
            dense
            flat
            color="white"
            :icon="$q.fullscreen.isActive ? 'fullscreen_exit' : 'fullscreen'"
            @click="$q.fullscreen.toggle()"
            v-if="$q.screen.gt.sm"
          />
         
          <q-btn round flat>
            <q-avatar size="26px">
              <img src="https://cdn.quasar.dev/img/boy-avatar.png" />
            </q-avatar>
          </q-btn>
          <q-btn
            round
            dense
            flat
            color="white"
            icon="logout"
            @click="handleLogout"
          />
        </div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      class="bg-primary text-white"
    >
      <q-list pointers>
        <q-item to="/Resume"  active-class="q-item-no-link-highlighting" >
          <q-item-section avatar>
            <q-icon name="person" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Resume</q-item-label>
          </q-item-section>
        </q-item>
        <q-item to="/AddVideo" active-class="q-item-no-link-highlighting">
          <q-item-section avatar>
            <q-icon name="add" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Add Video</q-item-label>
          </q-item-section>
        </q-item>
        <q-item to="/Videos" active-class="q-item-no-link-highlighting">
          <q-item-section avatar>
            <q-icon name="videocam" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Videos</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <q-page-container class="bg-grey-2">
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import EssentialLink from 'components/EssentialLink.vue';
import Messages from './Messages.vue';
import axios from 'axios'; // Ensure you have axios or a similar HTTP client
import router from 'src/router';

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

export default {
  name: 'MainLayout',

  components: {
    EssentialLink,
    Messages
  },

  data() {
    return {
      leftDrawerOpen: false,
      first_name: '',
      user: null, // To store the user data
      pollingInterval: null // To hold the interval ID
    };
  },

  computed: {
    drawerStatus() {
      return this.leftDrawerOpen ? 'Open' : 'Closed';
    }
  },

  methods: {
    refreshrouter() {
      this.$router.go(); // Refresh the current route
    },

    toggleLeftDrawer() {
      this.leftDrawerOpen = !this.leftDrawerOpen;
    },

    handleLogout() {
      // Clear user session data
      localStorage.removeItem('access_token_applicant'); // Assuming you store the token in localStorage
      localStorage.removeItem('user_data'); // Remove any other user-related data

      // Redirect to the login page
      this.$router.push('/login');
    },

    async fetchUserData() {
      try {
        const token = localStorage.getItem('access_token_applicant');
        const response = await axios.get(`${apiBaseUrl}/shortpolling`, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });

        this.user = response.data;
        this.first_name = this.user.first_name;
      } catch (error) {
        console.error('Failed to fetch user data:', error);
      }
    }
  },

  mounted() {
    this.fetchUserData();
  },

  beforeUnmount() {
    if (this.pollingInterval) {
      clearInterval(this.pollingInterval);
    }
  }
};
</script>

<style>
/* FONT AWESOME GENERIC BEAT */
.fa-beat {
  animation: fa-beat 5s ease infinite;
}

@keyframes fa-beat {
  0% {
    transform: scale(1);
  }
  5% {
    transform: scale(1.25);
  }
  20% {
    transform: scale(1);
  }
  30% {
    transform: scale(1);
  }
  35% {
    transform: scale(1.25);
  }
  50% {
    transform: scale(1);
  }
  55% {
    transform: scale(1.25);
  }
  70% {
    transform: scale(1);
  }
}
</style>
