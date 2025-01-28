<template>
    <q-page v-if="hiringpopup"   >
      <div  class="q-mt-md q-ml-md q-mr-md" style="display: flex; padding: 3px; justify-content: space-between">
        <q-btn label="back" @click="hiringpopup = false" />
        <!-- <div v-if="hiringStatusComputed == null"> -->
  <q-btn v-if="!hiringStatusComputed" label="Hire this applicant" class="zoom-animation-loop" color="green" @click="isHiringFunctionButton()" />
  <q-btn v-else label="Requested to admin" color="lightgray" disable />
</div>
      <!-- </div> -->

    <UserProfile :number="currentVideo.user_id" @HireStatus="HireStatus" />

  </q-page>
  <div class="video-wrapper" v-else>
    <div class="video-container">
      <div class="detail-container" ref="detailContainer" style="flex-direction: column">
        <div v-if="currentVideo" style="background-color: transparent; color: white; padding: 5px;">
          <div class="video-title">{{ currentVideo.title }}</div>
          <div class="video-description">{{ currentVideo.description }}</div>
        </div>
      </div>
      <iframe v-if="currentVideo" :src="formatUrl(currentVideo.link)" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>

    <div class="button-container" style="flex-direction: column">
      <div>
        <q-btn icon="chevron_left" @click="fetchPreviousVideo" class="q-mx-xs" round dense />
        <q-btn label="Hire now!" @click="handleHireClick" class="q-mx-xs zoom-animation-loop" rounded color="green" />
        <!-- <q-btn icon="play_arrow" label="Watch more" @click="handleHireClick" class="q-mx-xs " rounded color="blue" /> -->
        <q-btn icon="chevron_right" @click="fetchNextVideo" class="q-mx-xs" round dense />

      </div>
    </div>
  </div>

  <!-- <div class="show_this"></div> -->
</template>

<script>
import axios from 'axios';
import UserProfile from '../../components/UserProfile.vue';
import { fasLemon } from '@quasar/extras/fontawesome-v5';
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

export default {
  components: { UserProfile
 
  },
  data() {
    return {
      currentVideo: null, // Currently displayed video
      videoQueue: [], // Queue to store video links
      currentIndex: -1, // Index of the current video
      isFetching: false, // Flag to prevent multiple requests
      hiringpopup: false,
      hiringStatus: null,
    };
  },
  computed: {
  hiringStatusComputed() {
    return this.hiringStatus;
  }
},
  methods: {
    async isHiringFunctionButton() {
      try {
        const token = localStorage.getItem('access_token_employer');

        const response = await axios.post(
          `${apiBaseUrl}/hire`,
          { applicant_id: this.currentVideo.user_id }, // This is the data being sent
          {
            headers: {
              Authorization: `Bearer ${token}`, // This is the correct location for headers
            }
          }
        );
        
      } catch (error) {
        console.error('Error:', error);
        // Additional handling for error response
        if (error.response) {
          console.error('Response data:', error.response.data); // Log response data
        }
      }
      this.hiringStatus = true;
      // this.HireStatus();
    },

    HireStatus($status){

      this.hiringStatus = $status;
    },
    async fetchInitialVideo() {
      try {
        const token = localStorage.getItem('access_token_employer');
        const response = await axios.get(`${apiBaseUrl}/next-video`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        const video = response.data.video;
        if (video) {
          this.videoQueue.push(video);
          this.currentIndex = 0;
          this.currentVideo = video;
          this.updateVideo();
        }
      } catch (error) {
        console.error('Error fetching initial video:', error);
      }
    },

    async fetchNextVideo() {
      if (this.isFetching) return; // Prevent multiple requests

      this.isFetching = true; // Set fetching flag
      try {
        const token = localStorage.getItem('access_token_employer');
        const response = await axios.get(`${apiBaseUrl}/next-video`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        const video = response.data.video;

        if (video) {
          this.videoQueue.push(video);
          this.currentIndex++;
          this.updateVideo();
        }
      } catch (error) {
        console.error('Error fetching next video:', error);
      } finally {
        this.isFetching = false; // Reset fetching flag
      }
    },

    async fetchPreviousVideo() {
      if (this.isFetching) return; // Prevent multiple requests

      this.isFetching = true; 
      try {
        if (this.currentIndex > 0) {
          this.currentIndex--;
          this.updateVideo();
        } else {

        }
      } catch (error) {
        console.error('Error fetching previous video:', error);
      } finally {
        this.isFetching = false; // Reset fetching flag
      }
    },

    updateVideo() {
      if (this.currentIndex >= 0 && this.currentIndex < this.videoQueue.length) {
        this.currentVideo = this.videoQueue[this.currentIndex];
        this.$nextTick(() => {

        });
      }
    },

    formatUrl(url) {
      const urlObj = new URL(url);
      const videoId = urlObj.searchParams.get('v');
      return `https://www.youtube.com/embed/${videoId}`;
    },

    handleHireClick() {
      this.hiringpopup = true;
    }
  },
  watch: {
  hiringStatus(newValue) {
  }
},
  mounted() {
    this.fetchInitialVideo();
  }
};
</script>
<style scoped>
.video-wrapper {
  display: flex;
  flex-direction: column;
  height: 100vh; /* Full viewport height */
  overflow: hidden; /* Hide overflow */
  position: relative;
}

.video-container {
  flex: 1; /* Container takes up remaining space */
  width: 100%;
  height: 100%; /* Container fills the viewport */
  display: flex;
  flex-direction: column; /* Ensure child elements stack vertically */
  position: relative; /* Allow absolute positioning of children */
}

iframe {
  width: 100%;
  height: 100%;
  border: none;
}

.button-container {
  display: flex;
  justify-content: center;
  align-items: center;
  position: absolute;
  bottom: 0;
  width: 100%;
  background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
  padding: 20px;
  margin-bottom: 50px;
}

.detail-container {
  display: flex;
  justify-content: center;
  position: absolute;
  top: 0; /* Position at the top of the container */
  width: 100%;
  background-color: rgba(0, 0, 0, 0.233); /* Semi-transparent background */
  padding: 10px;
  margin-top: 50px;
  align-items: flex-start;
}

.q-btn {
  color: white;
  background-color: rgba(0, 0, 0, 0.7);
}

.q-btn.q-btn--dense {
  width: 40px;
  height: 40px;
}

.q-btn.q-btn--rounded {
  border-radius: 20px;
}

.video-info {
  text-align: center;
  margin: 20px;
  color: white;
}

/* Default font sizes for larger screens */
.video-description {
  font-size: 20px;
  color: #fff;
}

.video-title {
  font-size: 27px;
  color: #fff;
  font-weight: 600;
}

.zoom-animation-loop {
  animation: fa-beat 3s ease infinite;
}

@keyframes fa-beat {
  0% {
    transform: scale(1);
  }
  5% {
    transform: scale(1.1);
  }
  20% {
    transform: scale(1);
  }
  30% {
    transform: scale(1);
  }
  35% {
    transform: scale(1.1);
  }
  50% {
    transform: scale(1);
  }
  55% {
    transform: scale(1.1);
  }
  70% {
    transform: scale(1);
  }
}

/* Responsive font sizes for smaller screens */
@media (max-width: 768px) {
  .video-description {
    font-size: 18px;
  }

  .video-title {
    font-size: 24px;
  }
}

@media (max-width: 480px) {
  .video-description {
    font-size: 16px;
  }

  .video-title {
    font-size: 20px;
  }
}
</style>
