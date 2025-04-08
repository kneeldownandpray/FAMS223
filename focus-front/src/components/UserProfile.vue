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

   <div v-if="!this.showResumeStatus && this.DetailDisplayer">
      <DownloadResume v-if="resumeData" :resumeData2="resumeData"  />
    </div>
      <!-- {{ resumeData }} -->
      <div style="display: flex; justify-content:space-between;" v-if="!this.showResumeStatus && this.DetailDisplayer">

        <q-card-section v-if="!this.showResumeStatus && this.DetailDisplayer">
        <p class="b3-title q-mb-md q-mt-sm ">{{ resumeData.full_name }}</p>  
        <p><strong>Address:</strong> {{ resumeData.address }}</p>
        <p><strong>Birth Address:</strong> {{ resumeData.birth_address }}</p>
        <p><strong>Objectives:</strong> {{ resumeData.objectives }}</p>
        <p><strong>Nationality:</strong> {{ resumeData.nationality }}</p>
        <p><strong>Height:</strong> {{ resumeData.height }} cm</p>
        <p><strong>Weight:</strong> {{ resumeData.weight }} kg</p>
        <p><strong>Civil Status:</strong> {{ resumeData.civil_status }}</p>
        <p><strong>Religion:</strong> {{ resumeData.religion }}</p>
      </q-card-section>
      <q-card-section>
        <q-img
          src="https://fastly.picsum.photos/id/186/500/300.jpg?hmac=dYjINOiJPmekuXN_3Uf82u5IWRl257xOi2cFV_E8Fwk"
          spinner-color="white"
          style="height: 140px; max-width: 150px"
        ></q-img>

      </q-card-section>
      </div>


      <!-- Educational Attainments -->
      <q-card-section v-if="!this.showResumeStatus && this.DetailDisplayer">
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
     
      <q-card-section v-if="!this.showResumeStatus && this.DetailDisplayer"  >
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


      <q-card-section v-if="!this.showResumeStatus && this.DetailDisplayer" >
        <h5>Skills</h5>
        <q-list>
          <q-item v-for="experience in resumeData.skills" :key="experience.id">
            <q-item-section>
              <!-- <p><strong>{{ experience.skill_name }}:</strong> {{ experience.position }}</p> -->
              <!-- <p>{{ experience.skill_name }}</p> -->
              <div style="display: flex; gap: 10px;">
                <div style="    height: 20px;display: flex;align-content: center;justify-content: center; align-items: center;">
                  <div class="q-mr-sm" style="background-color: black; height: 6px; width: 6px; border-radius: 3px;"></div>
                </div>
                {{ experience.skill_name }} </div>
            </q-item-section>
          </q-item>
        </q-list>
      </q-card-section>

      
      <q-card-section v-if="!this.showResumeStatus && this.DetailDisplayer" >
        <h5>Certificates</h5>
        <q-list>
          <q-item v-for="experience in resumeData.skills" :key="experience.id">
            <q-item-section>
              <!-- <p><strong>{{ experience.skill_name }}:</strong> {{ experience.position }}</p> -->
              <!-- <p>{{ experience.skill_name }}</p> -->
              <div style="display: flex; gap: 10px;">
                <div style="    height: 20px;display: flex;align-content: center;justify-content: center; align-items: center;">
                  <div class="q-mr-sm" style="background-color: black; height: 6px; width: 6px; border-radius: 3px;"></div>
                </div>
                {{ experience.skill_name }} </div>
            </q-item-section>
          </q-item>
        </q-list>
      </q-card-section>



      <!-- User Videos -->
      <q-card-section v-if="resumeData.user_videos && resumeData.user_videos.length">
        <h5>Videos</h5>
       
          <div class="q-mb-lg" v-for="(video, index) in (showMoreVideos ? resumeData.user_videos : resumeData.user_videos.slice(0, 3))" :key="video.id">
            <q-item-section class="video-info-wrapper ">
              <div class="video-container responsive-video">
                <iframe
                  :src="`https://www.youtube.com/embed/${extractVideoId(video.youtube_link)}`"
                  frameborder="0"
                  allowfullscreen
                  class="video-iframe"
                ></iframe>
              </div>
              <div class="video-title q-mt-sm"><strong>{{ video.title }}</strong></div>
              <div class="video-description">{{ video.description }}</div>
            </q-item-section>
          </div>
    

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
      <!-- <q-card-actions>
        <q-btn color="primary" label="Download Resume" />
        <q-btn color="secondary" label="Edit Resume" />
      </q-card-actions> -->
    </q-card>
  </div>
</template>

<script>
import { defineComponent } from 'vue';
import axios from 'axios';
import DownloadResume from '../components/DownloadResume.vue';



const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

export default defineComponent({
  name: 'defaultpage',
  components: { DownloadResume},
  data() {
    return {
      resumeData: null,
      showResumeStatus:true,
      loading: true,
      DetailDisplayer:false,
      errorMessage: '',
      showMoreVideos: false, // New property to control video visibility
    };
  },
  mounted() {
    this.fetchStatus();
    if (!this.hired_statusb) {
    this.showResumeStatus = false; // Set HireStatus to true
    console.log('hired_statusb is:', this.hired_statusb); // Output the value
  }
  },
  methods: {
    async fetchStatus() {
  
  try {
    const token = localStorage.getItem('access_token_employer');

    const response3 = await axios.post(
      `${apiBaseUrl}/hire-checker/${this.number}`,
      {},
      {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    );
    // console.log(response3)
 
    this.HireStatus = response3.data.is_hired;
    if(!this.HireStatus){
      this.DetailDisplayer = false;
    }
    else {
      this.DetailDisplayer = true;
    }
    this.fetchResume();
 

    // Emit the value of is_hired
    this.$emit('HireStatus', this.HireStatus);
  } catch (error) {

    this.loading = false;
  }
},

    async fetchResume() {
      try {
        const token = localStorage.getItem('access_token_employer');
        if (!token) {
          this.errorMessage = 'No authentication token found';
          return;
        }
        
      if(this.HireStatus){
        
        const response = await axios.post(
          `${apiBaseUrl}/resumes/user/${this.number}`,
          {},
          {
            headers: {
              Authorization: `Bearer ${token}`
            }
          }
        );
        this.resumeData = response.data;
      }
      else
      {
        console.log("video");
        const response = await axios.post(
          `${apiBaseUrl}/resumes/video/${this.number}`,
          {},
          {
            headers: {
              Authorization: `Bearer ${token}`
            }
          }
        );
        this.resumeData = response.data;
      }
        // Assign fetched data to resumeData
       
        this.loading = false;
      } catch (error) {
        this.errorMessage = 'Failed to load resume data';
        this.loading = false;
        console.error('Error fetching resume:', error);
      }
      this.HireStatus = true;
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
},
test2(newValue){
console.log(newValue);
}
  },
  watch: {
  // Watch the prop 'hired_statusb' and update 'HireStatus' accordingly
  // hired_statusb(newValue) {
  //   this.showResumeStatus = newValue;
  //   test2(newValue);
  // }
},
  props: {
    number: {
      type: Number,
      required: true
    },
    hired_statusb: {
    type: Boolean,
    required: true
  }
  }
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
.b3-title{font-size: 30px;  color: #000000;  font-weight: 600;}
.video-description{ font-size: 20px; color: #fff; }
.video-title{ font-size: 25px;  color: #fff;  font-weight: 600;}
  .video-info-wrapper {
    padding: 10px;  background-color: rgba(15, 23, 42, 0.89);
  }
  .responsive-video {
  position: relative;
  padding-bottom: 56.25%; /* 16:9 aspect ratio */
  height: 0;
  overflow: hidden;
  max-width: 100%;
  background: #000;
}

.responsive-video iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border: 0;
}

</style>
