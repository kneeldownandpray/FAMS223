<template>
  <q-btn @click="openResumeDialog" label="Create Resume" color="primary" />

  <q-dialog v-model="resumeDialog" persistent>
    <q-card class="custom-dialog">
      <q-card-section class="header-format-w">
        <div class="text-h6">Create Resume</div>
      </q-card-section>

      <q-card-section>
        <q-input disable v-model="resume.full_name" label="Full Name" />
        <q-input disable v-model="resume.address" label="Address" />
        <q-input v-model="resume.birth_address" label="Birth Address" />
        <q-input v-model="resume.height" label="Height (ft)" type="number" />
        <q-input v-model="resume.weight" label="Weight (kg)" type="number" />
        <q-input v-model="resume.objectives" label="Objectives" type="textarea" />
        
        <q-select
          v-model="resume.profession"
          :options="professionOptions"
          label="Profession"
          emit-value
          map-options
        />
        
        <q-input
          v-model="resume.contact_no"
          label="Contact Number"
          type="text"
          :rules="[val => val && val.length >= 10 || 'Contact number must be at least 10 characters']"
        />
        
        <q-select
          v-model="resume.civil_status"
          :options="civilStatusOptions"
          label="Civil Status"
          emit-value
          map-options
        />
        
        <q-input v-model="resume.religion" label="Religion" />
        <q-input v-model="resume.nationality" label="Nationality" />
      </q-card-section>

      <q-card-actions align="right">
        <q-btn @click="saveResume" label="Save" color="primary" />
        <q-btn flat @click="closeResumeDialog" label="Cancel" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>
<script>
import { defineComponent, ref } from 'vue';
import axios from 'axios';

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

export default defineComponent({
  name: 'ResumePage',
  data() {
    return {
      resumeDialog: false,
      resume: {
        full_name: '',
        address: '',
        birth_address: '',
        height: null,
        weight: null,
        objectives: '',
        civil_status: '',
        religion: '',
        nationality: '',
        profession: '',
        contact_no: ''
      },
      resumeId: null,
      user: {}, // To store user data from API
      professionOptions: [
        'Auto Mechanic(Heavy Equipment Mechanic)',
        'Auto Mechanic(Small Engine Motor Mechanic)',
        'Barbers / Hairdresser',
        'Boat Builders',
        'Butcher/Slaughterer',
        'Cabinet Maker',
        'Car Tinter',
        'Fitter Welder',
        'Landscape Gardener',
        'Machinist',
        'Medical Laboratory Technicians',
        'Mechanical Fitters',
        'Panel Beaters/Vehicle Spray Painter',
        'Sign Writer',
        'Stonemason/Floor and Wall Tiler',
        'Tile Sitter/Glazier',
      ],


      
      civilStatusOptions: ['Single', 'Married', 'Divorced', 'Widowed']
    };
  },
  emits: ['resume-saved'], // Declare the custom event
  methods: {
    async openResumeDialog() {
      this.resumeDialog = true;
      try {
        const token = localStorage.getItem('access_token_applicant');
        const response = await axios.get(`${apiBaseUrl}/shortpolling`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        this.user = response.data;
        this.resume.full_name = `${this.user.first_name} ${this.user.middle_name} ${this.user.last_name}`;
        this.resume.address = this.user.address;
        // Optionally, populate other fields if needed
      } catch (error) {
        console.error('Failed to fetch user data:', error);
      }
    },
    closeResumeDialog() {
      this.resumeDialog = false;
    },
    async saveResume() {
      try {
        const token = localStorage.getItem('access_token_applicant');
        const response = await axios.post(`${apiBaseUrl}/resumes`, this.resume, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        this.resumeId = response.data.id; // Get the created resume ID
        this.resumeDialog = false;
        this.$emit('resume-saved', this.resumeId); // Emit the event
      } catch (error) {
        console.error(error);
      }
    }

  }
});
</script>