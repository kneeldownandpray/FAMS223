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
          <q-btn round dense flat color="white" icon="notifications">
            <q-badge color="red" text-color="white" floating>5</q-badge>
            <q-menu>
              <q-list style="min-width: 100px">
                <messages />
                <q-card class="text-center no-shadow no-border">
                  <q-btn
                    label="View All"
                    style="max-width: 120px !important;"
                    flat
                    dense
                    class="text-indigo-8"
                  />
                </q-card>
              </q-list>
            </q-menu>
          </q-btn>
          <q-btn round flat>
            <q-avatar size="26px">
              <img v-if="imageDisplay" :src="imgsourcelink" />
              <q-icon v-else name="person" />
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
      class="bg-dark text-white"
    >
      <q-list pointers>
        <div style="padding-left:60px;padding-right:70px; padding-top: 5px;padding-bottom: 15px;">
          <q-img
            src="../assets/logowhtie.png"
          width="100"
          />
        </div>
        <q-item  to="/global-chat" active-class="q-item-no-link-highlighting">
       <q-item-section avatar>
         <q-icon name="chat" />
       </q-item-section>
       <q-item-section>
         <q-item-label>Global Chat</q-item-label>
       </q-item-section>
     </q-item>

        <!-- Display based on user.account_type safely -->
        <q-item v-if="user?.account_type === 6" to="/applicant/Resume" active-class="q-item-no-link-highlighting">
          <q-item-section avatar>
            <q-icon name="person" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Resume</q-item-label>
          </q-item-section>
        </q-item>
        
        <q-item v-if="user?.account_type === 6" to="/applicant/AddVideo" active-class="q-item-no-link-highlighting">
          <q-item-section avatar>
            <q-icon name="add" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Add Video</q-item-label>
          </q-item-section>
        </q-item>
        <q-item v-if="user?.account_type === 6" to="/applicant/Videos" active-class="q-item-no-link-highlighting">
          <q-item-section avatar>
            <q-icon name="videocam" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Videos</q-item-label>
          </q-item-section>
        </q-item>

        <q-item v-if="user?.account_type === 6" to="/applicant/requirements" active-class="q-item-no-link-highlighting">
          <q-item-section avatar>
            <q-icon name="task" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Requirements</q-item-label>
          </q-item-section>
        </q-item>
        <q-item v-if="user?.account_type === 6"  @click="this.VisaStatusDialog = true" active-class="q-item-no-link-highlighting" style="cursor: pointer;">
          <q-item-section avatar @click="this.VisaStatusDialog = true"  >
            <q-icon name="fa-solid fa-plane" size="20px" />
          </q-item-section>
          <q-item-section @click="this.VisaStatusDialog = true" style="cursor: pointer;">
            <q-item-label >Visa Status</q-item-label>
          </q-item-section>
        </q-item>

        <q-item v-if="user?.account_type === 6" to="/applicant/VisaTransaction" active-class="q-item-no-link-highlighting">
          <q-item-section avatar>
            <q-icon name="history" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Visa Transaction History</q-item-label>
          </q-item-section>
        </q-item>
        
        <q-item v-if="user?.account_type === 5" to="/employer/profile" active-class="q-item-no-link-highlighting">
       <q-item-section avatar>
         <q-icon name="account_circle" />
       </q-item-section>
       <q-item-section>
         <q-item-label>Employer Profile</q-item-label>
       </q-item-section>
     </q-item>

     <q-item v-if="user?.account_type === 5" to="/employer/reels" active-class="q-item-no-link-highlighting">
       <q-item-section avatar>
         <q-icon name="play_circle_filled" />
       </q-item-section>
       <q-item-section>
         <q-item-label>Video Reels</q-item-label>
       </q-item-section>
     </q-item>

     <q-item v-if="user?.account_type === 5" to="/employer/applicants-status" active-class="q-item-no-link-highlighting">
       <q-item-section avatar>
         <q-icon name="group" />
       </q-item-section>
       <q-item-section>
         <q-item-label>Applicants</q-item-label>
       </q-item-section>
     </q-item>

      </q-list>
    </q-drawer>

    <q-page-container class="bg-grey-2">
    <router-view />
  </q-page-container>

  <q-dialog v-model="VisaStatusDialog">
    <q-card class="q-pa-lg">
      <q-btn 
  label="Exit" 
  color="negative" 
  @click="VisaStatusDialog = false" 
  style="width: 70px;"
  class="q-mt-sm"
  rounded
/>
      <!-- VisaStatus content goes here -->
      <VisaStatus class="q-mt-lg" />

      <!-- Exit button -->

    </q-card>
  </q-dialog>



  </q-layout>
</template>
<script>
import EssentialLink from 'components/EssentialLink.vue';
import VisaStatus from 'components/VisaStatus.vue';
import Messages from './Messages.vue';
import axios from 'axios';
import router from 'src/router';
import { io } from 'socket.io-client';
const ImageBaseUrl = import.meta.env.VITE_IMG_DP;


const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

export default {
  name: 'MainLayout',

  components: {
    EssentialLink,
    Messages,
    VisaStatus
  },

  data() {
    return {
      imagelink:ImageBaseUrl,
      leftDrawerOpen: false,
      first_name: '',
      user: null,
      pollingInterval: null,
      isLoading: true, // Add a loading state
      VisaStatusDialog:false,
      imageDisplay:false,
    };
  },

  computed: {
    drawerStatus() {
      return this.leftDrawerOpen ? 'Open' : 'Closed';
    }
  },
  created(){
    this.fetchUserData();
    const socketBaseUrl = import.meta.env.VITE_SOCKET_BASE_URL;
    this.socket = io(socketBaseUrl);
    this.socket.on('receiverTriggerness', (action, id) => {
      if(action == "AutomaticLogoutWorkerApplicant"){
        this.handleLogout();
      }
      
    });
  },

  methods: {
    getresumeLink(get){
      this.imgsourcelink = this.imagelink + get.replace('profile_pictures/', '');
      console.log(this.imgsourcelink)
      this.displayimg2();
    },
    displayimg2(){
      this.imageDisplay = true;

    },

    refreshrouter() {
      this.$router.go(); // Refresh the current route
    },

    toggleLeftDrawer() {
      this.leftDrawerOpen = !this.leftDrawerOpen;
    },

    async handleLogout() {
    try {
      const user = JSON.parse(localStorage.getItem('user'));
      let token = null;

      // Determine the correct token based on the account type
      if (user && user.account_type === 5) {
        token = localStorage.getItem('access_token_employer');
      } else if (user && user.account_type === 6) {
        token = localStorage.getItem('access_token_applicant');
      }

      // If token exists, send the logout request
      if (token) {
        const response = await axios.post(`${apiBaseUrl}/logout`, null, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });

        // Check if the response contains the success message
        if (response.data.message === "Successfully logged out") {
          // Remove tokens and user data from local storage
          localStorage.removeItem('access_token_employer');
          localStorage.removeItem('access_token_applicant');
          localStorage.removeItem('user_data');
          localStorage.removeItem('user');

          // Redirect to the login page
          this.$router.push('/login');
        } else {
          console.error('Unexpected logout response:', response.data);
        }
      }
    } catch (error) {
      console.error('Logout failed:', error);
    }
  },
    async fetchUserData() {
      try {
        const user = JSON.parse(localStorage.getItem('user'));
        let token = null;

        if (user && user.account_type === 5) {
          localStorage.removeItem('access_token_applicant');
          token = localStorage.getItem('access_token_employer');
        } else if (user && user.account_type === 6) {
          localStorage.removeItem('access_token_employer');
          token = localStorage.getItem('access_token_applicant');
        }

        if (token) {
          const response = await axios.get(`${apiBaseUrl}/shortpolling`, {
            headers: {
              Authorization: `Bearer ${token}`
            }
          });

          this.user = response.data;
          this.first_name = this.user.first_name;
          console.log(this.user.profile_picture);
          if(this.user.profile_picture){
          this.getresumeLink(this.user.profile_picture);
        }
        }
      } catch (error) {
        console.error('Failed to fetch user data:', error);
        this.$router.push('/login');
      } finally {
        this.isLoading = false; // Set loading to false after data is fetched
      }
    }
  },



  beforeUnmount() {
    if (this.pollingInterval) {
      clearInterval(this.pollingInterval);
    }
  }
};
</script>
