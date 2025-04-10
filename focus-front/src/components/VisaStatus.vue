<template>
  <div>
    <h3><strong>Visa Application <br>Status</strong></h3>
    <!-- <q-btn @click="toggleSkillAssessment" label="Toggle Skill Assessment" color="primary" class="q-mb-md" /> -->
  </div>
  <div class="q-gutter-md">
    <q-row class="items-center q-col-gutter-md" style="flex-wrap: wrap;">
      <q-col
        v-for="(step, index) in steps"
        :key="index"
        :cols="12" :sm="4" :md="3" :lg="2"
        class="q-mb-md"
      >
        <!-- Conditionally render 'Skill Assessment' step based on the toggle state -->
        <div v-if="index !== 2 || showSkillAssessment" class="step-container" @click="openDialog(step)">
          <div class="q-mb-xs" :style="{ backgroundColor: getStepColor(index) }">
            <q-icon
              :name="step.icon"
              size="lg"
              style="color: whitesmoke;"
              flat
            />
          </div>
          <div :style="{ color: getStepColor(index) }" class="text-subtitle2 text-center">
            {{ step.label }}
          </div>
        </div>
      </q-col>
    </q-row>
  </div>

  <!-- Dialog for showing step details -->
  <q-dialog v-model="dialogVisible">
    <q-card>
      <q-card-section>
        <div class="text-h6">{{ selectedStep ? selectedStep.label : dialogMessage.title }}</div>
        <div class="q-mt-md">
          <p>{{ selectedStep ? selectedStep.details : dialogMessage.details }}</p>
        </div>
      </q-card-section>
      <q-card-actions>
        <q-btn flat label="Close" @click="dialogVisible = false" />
        <q-btn v-if="currentStep === 9" flat label="Start New Transaction" @click="startNewTransaction" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>
export default {
  name: 'VisaStatusTracker',

  data() {
    return {
      displayVisaStatus: true,
      currentStep: 4, // Set this value from 1 to 8
      dialogVisible: false, // Controls visibility of the dialog
      selectedStep: null, // Stores the step details for the dialog
      showSkillAssessment: true, // Toggle visibility for 'Skill Assessment' step
      steps: [
        { 
          label: 'Application Recieved', 
          icon: 'fa-solid fa-file-alt',
          details: 'Details about the application submission will go here. This step indicates that the application has been submitted and is awaiting review.'
        },
        { 
          label: 'Interview & Employer Confirmation', 
          icon: 'fa-solid fa-user-check',
          details: 'This step involves scheduling and attending an interview. After the interview, employer confirmation is needed before proceeding to the next step.' 
        },
        {
          label: 'Skill Assessment', 
          icon: 'fa-solid fa-cogs', 
          details: 'If required, this step involves completing a skill assessment through the relevant Australian authority like VETASSESS or ACTS.',
        },
        { 
          label: 'Visa Preparation', 
          icon: 'fa-solid fa-clipboard-list',
          details: 'Visa preparation includes gathering the necessary documents and completing any required forms before submitting the visa application.'
        },
        { 
          label: 'Visa Lodged', 
          icon: 'fa-solid fa-paper-plane',
          details: 'This step indicates that the visa application has been lodged with the appropriate authorities for processing.'
        },
        { 
          label: 'Medical & Biometrics', 
          icon: 'fa-solid fa-heartbeat',
          details: 'In this step, medical exams and biometric data (fingerprints, photo) are collected as part of the visa process.'
        },
        { 
          label: 'Awaiting Decision', 
          icon: 'fa-solid fa-hourglass',
          details: 'The application is being processed and you must wait for the decision. This may take several weeks.'
        },
        { 
          label: 'Visa Outcome', 
          icon: 'fa-solid fa-check-circle',
          details: 'Once the decision is made, you will be notified about the visa outcome. If successful, you will proceed to the next step.'
        },
        { 
          label: 'Ready to Fly', 
          icon: 'fa-solid fa-plane-departure',
          details: 'This final step indicates that your visa has been approved, and you are ready to make travel arrangements to your destination.'
        },
      ],
      dialogMessage: {
        title: 'Congratulations!',
        details: 'Wow, your visa application is complete! Do you want to start another transaction?'
      }
    };
  },

  methods: {
    toggleSkillAssessment() {
      // Toggle the visibility of the 'Skill Assessment' step
      this.showSkillAssessment = false;
    },
    triggerAlert() {
      if (this.currentStep === 9) {
        // Show the special dialog when the status reaches 9
        this.dialogMessage = {
          title: 'Congratulations!',
          details: 'Wow, your visa application is complete! Do you want to start another transaction?'
        };
        this.dialogVisible = true;
      } else {
        // Show the details of the current step
        const currentStepObject = this.steps[this.currentStep - 1];
        if (currentStepObject) {
          this.openDialog(currentStepObject);
        }
      }
    },
    getStepColor(index) {
      if (index < this.currentStep - 1) return '#4CAF50'; // Green for completed steps
      if (index === this.currentStep - 1) return '#FFC107'; // Yellow for current step
      return '#BDBDBD'; // Grey for upcoming steps
    },
    openDialog(step) {
      this.selectedStep = step; // Set the selected step's details
      this.dialogVisible = true; // Show the dialog
    },
    startNewTransaction() {
      // Logic for starting a new transaction
      console.log('Starting a new transaction...');
      this.dialogVisible = false; // Close the dialog
    }
  },

  mounted() {
    console.log('Visa tracker mounted');
    this.triggerAlert(); // Trigger the alert/dialog automatically
    if(!this.showSkillAssessment){
      this.toggleSkillAssessment();
    }
  }
};
</script>

<style scoped>
.q-icon {
  flex-shrink: 0;
}

.step-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 10px;
  border-radius: 8px;
  position: relative;
  cursor: pointer;
}

.q-pa-md {
  max-width: 100%;
}

.step-container .q-mb-xs {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 77px;
  height: 77px;
  border-radius: 50%;
  background-color: #f0f0f0;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.step-container .text-subtitle2 {
  margin-top: 10px;
  text-align: center;
}

/* Bubble for completed steps */
.bubble {
  position: absolute;
  top: -5px;
  right: -5px;
  width: 15px;
  height: 15px;
  background-color: #4CAF50; /* Green bubble */
  border-radius: 50%;
  border: 2px solid white;
}

/* Style for the dialog */
.q-dialog .q-card {
  width: 350px;
}

@media (max-width: 600px) {
  .step-container {
    flex-direction: column;
    align-items: center;
  }
}
</style>
