<template>
  <div>
   <span style="color: red;"> Maximum of 1.8MB Only</span> 
    <q-uploader
      color="primary"
      label="Select Resume or Profile Picture"
      url=""
      :factory="uploadFile"
      field-name="profile_picture"
      :headers="uploadHeaders"
      accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
      :auto-upload="false"
      @added="onFileSelected"
    />

    <div class="q-mt-md">
      <q-btn
        v-if="selectedFile && !uploadSuccess"
        color="primary"
        label="Upload File"
        @click="triggerUpload"
      />
      <q-btn
        v-if="uploadSuccess"
        color="positive"
        icon="check_circle"
        label="Uploaded"
        flat
        disable
      />
    </div>

    <div v-if="uploadedFilePath" class="q-mt-md">
      <div v-if="uploadedFilePath.endsWith('.pdf')">
        <a :href="uploadedFilePath" target="_blank" class="text-primary">View Uploaded PDF</a>
      </div>
      <div v-else>
        <img :src="uploadedFilePath" alt="Uploaded File" style="max-width: 200px;" />
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue';
import axios from 'axios';

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

export default defineComponent({
  name: 'FileUploader',
  data() {
    return {
      uploadedFilePath: '',
      selectedFile: null,
      uploadSuccess: false
    };
  },
  methods: {
    uploadHeaders() {
      const token = localStorage.getItem('access_token_applicant');
      return [
        { name: 'Authorization', value: `Bearer ${token}` }
      ];
    },
    onFileSelected(files) {
      this.selectedFile = files[0];
      this.uploadSuccess = false; // reset on new file
    },
    async triggerUpload() {
      if (!this.selectedFile) return;

      const token = localStorage.getItem('access_token_applicant');
      const formData = new FormData();
      formData.append('profile_picture', this.selectedFile);

      try {
        const response = await axios.post(`${apiBaseUrl}/upload-profile-picture`, formData, {
          headers: {
            Authorization: `Bearer ${token}`,
            'Content-Type': 'multipart/form-data'
          }
        });

        this.uploadedFilePath = response.data.path;
        this.uploadSuccess = true;

        // ✅ Emit success event
        this.$emit('file-uploaded', response.data.path);

      } catch (error) {
        console.error('Upload failed:', error);
        this.uploadSuccess = false;

        // ❌ Emit error event with error message
        const message = 'File too large. Please upload a smaller file.';
        this.$emit('upload-error', message);
  
      }
    },
    uploadFile() {
      return new Promise(() => {}); // required by QUploader
    }
  }
});
</script>

<style scoped>
img {
  border: 1px solid #ccc;
  padding: 4px;
  border-radius: 6px;
}
</style>
