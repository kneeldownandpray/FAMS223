<template>
  <q-page class="q-pa-md">
    <div class="q-gutter-sm" style="display: flex; flex-wrap: wrap;">
      <q-card
        v-for="requirement in requirements"
        :key="requirement.id"
        flat
        bordered
        class="requirement-card"
      >
        <q-card-section class="q-pa-sm">
          <div class="text-subtitle1">{{ requirement.name }}</div>
          <div class="text-caption text-grey-7">{{ requirement.description }}</div>
          <div class="text-caption q-mt-xs" v-if="userRequirementsMap && userRequirementsMap[requirement.id] && userRequirementsMap[requirement.id].note">
  <i style="color: red; font-weight: 600; font-size: 15px;"> 
    NOTE : {{ userRequirementsMap[requirement.id].note || 'No note available' }}
  </i>
</div>
        </q-card-section>

        <q-separator />

        <q-card-section class="q-pa-sm">
          <div v-if="userRequirementsMap[requirement.id]" style="display: flex; flex-wrap: wrap; justify-content: space-between;">
            <q-chip
              dense
              :color="getStatusColor(userRequirementsMap[requirement.id].status)"
              :icon="getStatusIcon(userRequirementsMap[requirement.id].status)"
              class="q-mb-xs"
              :label="`Status: ${userRequirementsMap[requirement.id].status}`"
              :text-color="getTextColor(userRequirementsMap[requirement.id].status)"
            />

            <q-btn
              color="secondary"
              icon="download"
              label="Download"
              size="sm"
              @click="downloadFile(userRequirementsMap[requirement.id].id)"
              class="q-mt-xs"
            />
          </div>

          <div v-else>
            <q-file
              v-model="files[requirement.id]"
              filled
              dense
              label="Upload"
              class="q-mb-xs"
            />
            <q-btn
              color="primary"
              label="Submit"
              size="sm"
              @click="uploadFile(requirement.id)"
              :disable="!files[requirement.id]"
            />
          </div>
        </q-card-section>
      </q-card>
    </div>
  </q-page>
</template>



<script>
import axios from 'axios';


export default {
  data() {
    return {
      apiBaseUrl: import.meta.env.VITE_API_BASE_URL,
      token: localStorage.getItem('access_token_applicant'),
      requirements: [],
      userRequirements: [],
      files: {}, // file holder per requirement
    };
  },
  computed: {
    userRequirementsMap() {
      const map = {};
      this.userRequirements.forEach((item) => {
        map[item.requirement_type_id] = item;
      });
      return map;
    },
  },
  methods: {
    getStatusColor(status) {
    if (status === 'processing') return 'yellow';
    if (status === 'accepted') return 'green';
    if (status === 'rejected') return 'red';
    return 'grey'; // default color
  },
  getStatusIcon(status) {
    if (status === 'processing') return 'hourglass_full'; // or any icon you prefer
    if (status === 'accepted') return 'check'; // green check icon
    if (status === 'rejected') return 'close'; // red close icon
    return 'help'; // default icon
  },
  getTextColor(status) {
    if (status === 'processing') return 'black'; // neutral text for yellow
    if (status === 'accepted') return 'white'; // white text on green
    if (status === 'rejected') return 'white'; // white text on red
    return 'black'; // default text color
  },
    async fetchRequirements() {
      try {
        const res = await axios.get(`${this.apiBaseUrl}/display/requirements`, {
          headers: { Authorization: `Bearer ${this.token}` },
        });
        this.requirements = res.data;
      } catch (error) {
        console.error('Error fetching requirements:', error);
      }
    },
    async fetchUserRequirements() {
      try {
        const res = await axios.get(`${this.apiBaseUrl}/user-requirements`, {
          headers: { Authorization: `Bearer ${this.token}` },
        });
        this.userRequirements = res.data;
      } catch (error) {
        console.error('Error fetching user requirements:', error);
      }
    },
    async uploadFile(requirementId) {
      const formData = new FormData();
      formData.append('requirement_type_id', requirementId);
      formData.append('file', this.files[requirementId]);

      try {
        await axios.post(`${this.apiBaseUrl}/user-requirements`, formData, {
          headers: {
            Authorization: `Bearer ${this.token}`,
            'Content-Type': 'multipart/form-data',
          },
        });
        alert("Upload Success");
        await this.fetchUserRequirements();
      } catch (error) {
        console.error('Upload failed:', error);
        alert("Upload failed");
      }
    },
  },
  mounted() {
    this.fetchRequirements();
    this.fetchUserRequirements();
  },

};
</script>

<style scoped>
.requirement-card {
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
  width: 23%;
}

@media (max-width: 650px) {
  .requirement-card {
    width: 100% !important;
    
  }
}
</style>
