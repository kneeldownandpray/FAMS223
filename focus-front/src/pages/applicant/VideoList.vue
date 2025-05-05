<template>
  <q-page padding>
    <h3 class="is-NotMobile-test" style="font-weight: 600;">My Videos</h3>
    <div v-if="videos.length === 0">No videos available</div>
    <div v-if="!isVideoEnlarged" class="video-wrapper">
      <q-btn
        class="prev-button q-mt-md"
        v-if="!isMobile && scrollAmount > 0"
        icon="arrow_back"
        @click="slidePrev"
        round
        dense
        flat
        size="1.5em"
        color="#353535;"
      />
      <div class="video-list" ref="videoList">
        <div class="video-container" v-for="(video, index) in videos" :key="video.id">
          <iframe
            :id="'video-' + video.id"
            :src="getYouTubeEmbedLink(video.youtube_link, isMobile && index === 0)"
            class="video-frame"
            frameborder="0"
            allow="autoplay; encrypted-media"
            allowfullscreen
          ></iframe>
          <div class="">
            <div class="video-info" v-if="!isMobile" style="display: flex;flex-direction: row; justify-content: space-between; align-items: center;">
              <div>
                <div class="video-title">{{ video.title }}</div>
                <div class="video-description">{{ video.description }}</div>
              </div>
              <div>
                <q-btn
                  v-if="!isMobile"
                  label="Play"
                  push
                  text-color="primary"
                  @click="enlargeVideo(video.id)"
                  color="white"
                  icon="play_circle"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
      <q-btn
        class="next-button q-mt-md"
        v-if="!isMobile"
        icon="arrow_forward"
        @click="slideNext"
        round
        dense
        flat
        color="primary"
        size="1.5em"
      />
    </div>
    <div v-if="isVideoEnlarged" class="enlarged-view">
      <q-btn
        class="back-button"
        icon="arrow_back"
        @click="isVideoEnlarged = false"
        label="Back"
        text-color="primary"
        color="white"
      />
      <iframe
        :src="getYouTubeEmbedLink(currentVideo.youtube_link, true)"
        class="enlarged-video-frame"
        frameborder="0"
        allow="autoplay; encrypted-media"
        allowfullscreen
      ></iframe>
    </div>
  </q-page>
</template>

<script>
export default {
  data() {
    return {
      videos: [],
      isMobile: false,
      scrollAmount: 0,
      isVideoEnlarged: false,
      currentVideo: null
    };
  },
  mounted() {
    this.fetchVideos();
    this.checkMobile();
    if (this.isMobile) {
      this.$refs.videoList.addEventListener('scroll', this.onScroll);
    }
  },
  beforeDestroy() {
    if (this.isMobile) {
      this.$refs.videoList.removeEventListener('scroll', this.onScroll);
    }
  },
  methods: {
    fetchVideos() {
      const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;
      this.$axios
        .get(`${apiBaseUrl}/user-videos`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('access_token_applicant')}`
          }
        })
        .then(response => {
          this.videos = response.data;
          if (!this.isMobile && this.videos.length > 0) {
            this.autoplayFirstVideo();
          }
        })
        .catch(err => {
          console.error('Error fetching videos:', err);
        });
    },
    getYouTubeEmbedLink(youtubeLink, autoplay = false) {
      const videoId = this.extractYouTubeVideoId(youtubeLink);
      const url = videoId ? `https://www.youtube.com/embed/${videoId}${autoplay ? '?autoplay=1' : ''}` : '';
      return url;
    },
    extractYouTubeVideoId(url) {
      if (!url) return null;
      const match = url.match(/[?&]v=([^&]+)/);
      return match ? match[1] : null;
    },
    autoplayFirstVideo() {
      if (this.videos.length > 0) {
        const firstVideoId = this.videos[0].id;
        const iframe = document.getElementById(`video-${firstVideoId}`);
        iframe.src += '?autoplay=1';
      }
    },
    checkMobile() {
      this.isMobile = /Mobi|Android/i.test(navigator.userAgent);
    },
    slideNext() {
      const videoList = this.$refs.videoList;
      const videoContainer = videoList.children[0];
      const containerWidth = videoContainer.offsetWidth;
      const newScrollAmount = this.scrollAmount + containerWidth;
      if (newScrollAmount < videoList.scrollWidth) {
        this.scrollAmount = newScrollAmount;
        videoList.scrollTo({
          left: this.scrollAmount,
          behavior: 'smooth'
        });
      }
    },
    slidePrev() {
      const videoList = this.$refs.videoList;
      const videoContainer = videoList.children[0];
      const containerWidth = videoContainer.offsetWidth;
      const newScrollAmount = this.scrollAmount - containerWidth;
      if (newScrollAmount >= 0) {
        this.scrollAmount = newScrollAmount;
        videoList.scrollTo({
          left: this.scrollAmount,
          behavior: 'smooth'
        });
      }
    },
    enlargeVideo(videoId) {
      this.currentVideo = this.videos.find(video => video.id === videoId);
      if (this.currentVideo) {
        this.isVideoEnlarged = true;
      }
    },
    pauseAllVideos() {
      const iframes = document.querySelectorAll('iframe');
      iframes.forEach(iframe => {
        const src = iframe.src;
        iframe.src = ''; // Remove the src to pause the video
        iframe.src = src; // Reset the src to restore the video
      });
    },
    onScroll() {
      this.pauseAllVideos();
    }
  }
};
</script>

<style scoped>
.video-wrapper {
  position: relative;
}

.video-list {
  display: flex;
  flex-direction: column;
  scroll-snap-type: y mandatory;
  overflow-y: auto;
  height: 100vh;
}

.video-container {
  margin-bottom: 20px;
  scroll-snap-align: start;
  display: flex;
  flex-direction: column; /* Ensure column direction for the title and description */

}

.video-frame {
  width: 100%;
  max-width: 560px;
  height: 315px;
  cursor: pointer;
}

.video-info-wrapper {
  text-align: center; /* Center align text for desktop */
}

@media (max-width: 600px) {
  .q-page {
    padding: 0;
  }

  .video-list {
    height: 100vh;
    display: flex;
    flex-direction: column;
  }

  .video-container {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative; /* Position relative for absolute positioning of video-info */
  }

  .video-frame {
    width: 100vw;
    height: 100vh;
    max-height: none;
    border: none;
  }

  .video-info-wrapper {
    position: absolute;
    bottom: 0;
    width: 100%;
    background: #353535;
    color: white;
    padding: 10px;
    text-align: center;
    
  }
  .is-NotMobile-test{
    display: none !important;
  }
}

@media (min-width: 601px) {
  .video-wrapper {
    display: flex;
    align-items: center;
  }

  .video-list {
    display: flex;
    flex-direction: row;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    white-space: nowrap;
  }

  .video-container {
    display: inline-flex;
    margin-right: 20px;
    scroll-snap-align: start;
    flex-direction: column; /* Stack video frame and info vertically on desktop */
  }

  .video-frame {
    width: 560px;
    height: 315px;
  }

  .next-button,
  .prev-button {
    position: absolute;
    z-index: 1111;
  }

  .prev-button {
    left: 20px;
  }

  .next-button {
    right: 20px;
  }

  .enlarged-view {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: #353535;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
  }

  .enlarged-video-frame {
    width: 80%;
    height: 80%;
  }

  .back-button {
    position: absolute;
    top: 20px;
    left: 20px;
    background: #353535;
    color: white;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
  }
  .video-info{ padding: 10px;  background-color:#353535;}
  .video-description{ font-size: 20px; color: #fff; }
  .video-title{ font-size: 27px;  color: #fff;  font-weight: 600;}
  
}
</style>
