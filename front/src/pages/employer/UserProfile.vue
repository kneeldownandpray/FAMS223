<template>
  <div class="q-pa-md">
    <!-- Show loading spinner while fetching data -->
    <q-spinner v-if="loading" color="primary" />

    <!-- Error message -->
    <div v-if="errorMessage">
      <q-banner dense class="bg-negative text-white q-px-lg q-py-sm">
        {{ errorMessage }}
      </q-banner>
    </div>

    <!-- Resume display once data is fetched -->
    <q-card v-if="resumeData && !loading" class="q-ma-xs">
      <q-card-section>
        <q-avatar size="80" class="q-mb-md">
          <img src="https://via.placeholder.com/80" alt="Avatar" />
        </q-avatar>
        <h4 class="q-mb-xs">{{ resumeData.full_name }}</h4>
        <p><strong>Address:</strong> {{ resumeData.address }}</p>
        <p><strong>Birth Address:</strong> {{ resumeData.birth_address }}</p>
        <p><strong>Objectives:</strong> {{ resumeData.objectives }}</p>
        <p><strong>Nationality:</strong> {{ resumeData.nationality }}</p>
        <p><strong>Height:</strong> {{ resumeData.height }} cm</p>
        <p><strong>Weight:</strong> {{ resumeData.weight }} kg</p>
        <p><strong>Civil Status:</strong> {{ resumeData.civil_status }}</p>
        <p><strong>Religion:</strong> {{ resumeData.religion }}</p>
      </q-card-section>

      <!-- Educational Attainments -->
      <q-card-section>
        <h5>Educational Attainments</h5>
        <q-list>
          <q-item v-for="education in resumeData.educational_attainments" :key="education.id">
            <q-item-section>
              <p>
                <strong>{{ education.level }}:</strong> {{ education.institution }} <br>
                <strong>Period:</strong> {{ education.period }}
              </p>
              <p v-if="education.level === 'Tertiary'">
                <strong>Course:</strong> {{ education.course }} | <strong>Major:</strong> {{ education.major }}
              </p>
              <p v-if="education.level === 'Secondary'">
                <strong>Strand (optional for K12):</strong> {{ education.course }} | <strong>Major:</strong> {{ education.major }}
              </p>
            </q-item-section>
          </q-item>
        </q-list>
      </q-card-section>

      <!-- Work Experiences -->
      <q-card-section>
        <h5>Work Experiences</h5>
        <q-list>
          <q-item v-for="experience in resumeData.work_experiences" :key="experience.id">
            <q-item-section>
              <p><strong>{{ experience.company_name }}:</strong> {{ experience.position }}</p>
              <p>{{ experience.job_description }}</p>
              <p><strong>Start Date:</strong> {{ experience.start_date }} | <strong>End Date:</strong> {{ experience.end_date }}</p>
            </q-item-section>
          </q-item>
        </q-list>
      </q-card-section>

      <!-- User Videos -->
      <q-card-section v-if="resumeData.user_videos && resumeData.user_videos.length">
        <h5>User Videos</h5>
        <q-list>
          <q-item v-for="(video, index) in (showMoreVideos ? resumeData.user_videos : resumeData.user_videos.slice(0, 3))" :key="video.id">
            <q-item-section class="video-info-wrapper">
              <div class="video-container">
                <iframe
                  :src="`https://www.youtube.com/embed/${extractVideoId(video.youtube_link)}`"
                  frameborder="0"
                  allowfullscreen
                  class="video-iframe"
                ></iframe>
              </div>
              <div class="video-title"><strong>{{ video.title }}</strong></div>
                <div class="video-description">{{ video.description }}</div>
            </q-item-section>
          </q-item>
        </q-list>
        <!-- Show button if there are more than 5 videos -->
        <!-- <q-btn v-if="resumeData.user_videos.length > 3" @click="toggleShowMore" label="Watch More Videos" color="primary" /> -->
          <q-btn 
  v-if="resumeData.user_videos.length > 3 && !showMoreVideos" 
  @click="toggleShowMore" 
  label="Watch More Videos" 
  color="primary" 
/>
      </q-card-section>

      <!-- Action Buttons -->
      <q-card-actions>
        <q-btn color="primary" label="Download Resume" />
        <q-btn color="secondary" label="Edit Resume" />
      </q-card-actions>
    </q-card>
  </div>
</template>

<script>
import { defineComponent } from 'vue';
import axios from 'axios';

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

export default defineComponent({
  name: 'defaultpage',
  data() {
    return {
      resumeData: null,
      loading: true,
      errorMessage: '',
      showMoreVideos: false, // New property to control video visibility
    };
  },
  mounted() {
    this.fetchResume();
  },
  methods: {
    async fetchResume() {
      try {
        const token = localStorage.getItem('access_token_employer');
        if (!token) {
          this.errorMessage = 'No authentication token found';
          return;
        }

        const response = await axios.post(
          `${apiBaseUrl}/resumes/user/121`,
          {},
          {
            headers: {
              Authorization: `Bearer ${token}`
            }
          }
        );

        // Assign fetched data to resumeData
        this.resumeData = response.data;
        this.loading = false;
      } catch (error) {
        this.errorMessage = 'Failed to load resume data';
        this.loading = false;
        console.error('Error fetching resume:', error);
      }
    },
    extractVideoId(url) {
      const regExp = /(?:https?:\/\/)?(?:www\.)?youtube\.com\/(?:watch\?v=|embed\/|v\/|.*v=)?([^&\n?#]+)/;
      const match = url.match(regExp);
      return match ? match[1] : '';
    },
    toggleShowMore() {
  this.showMoreVideos = !this.showMoreVideos; // Toggle video visibility
  // Optional: Set a limit for how many videos to show
  if (this.showMoreVideos && this.resumeData.user_videos.length <= 5) {
    this.showMoreVideos = false; // Reset if less than or equal to 5
  }
}
  },
});
</script>

<style scoped>
.video-container {
  position: relative;
  padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
  height: 0;
  overflow: hidden;
  margin-bottom: 16px; /* Space between video and text */
}

.video-iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
.video-description{ font-size: 20px; color: #fff; }
.video-title{ font-size: 25px;  color: #fff;  font-weight: 600;}
  .video-info-wrapper {
    padding: 10px;  background-color: rgba(15, 23, 42, 0.89);
    
  }
</style>
