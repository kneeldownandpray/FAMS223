<template>
  <q-page padding>
    
    <div v-if="resume">
      <!-- CV or Resume -->
      <div class="text-h3 q-mb-md" style="
    display: flex;
    justify-content: space-between;
      "><strong>CV or Resume</strong>
      <div style="cursor: pointer; position: relative; width: 160px; height: 160px;">
    <!-- Black Overlay with Pencil Icon (hidden by default) -->
    <div  @click="showDialogForProfilePic = true"
      class="overlay"
      style="background-color: black; position: absolute; top: 0; left: 0; width: 160px; height: 160px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            box-shadow: rgb(149 157 165 / 51%) 1px 2px 6px; opacity: 0; transition: opacity 0.3s;"
    >
      <q-icon name="edit" color="white" size="32px" />
    </div>

    <!-- Profile Picture -->
    <img
      :src="this.imgsourcelink"
      alt=""
      style="height: 160px; width: 160px; border-radius: 50%;
            box-shadow: rgb(149 157 165 / 51%) 1px 2px 6px;"
    >
  </div>
    </div>
    <q-dialog v-model="showDialogForProfilePic" persistent>
  <q-card class="q-pa-md" style="min-width: 300px; max-width: 400px;">
    <div class="row items-center justify-between q-mb-md">
      <div class="text-h6">Upload Profile Picture</div>
      <q-btn flat round dense icon="close" @click="showDialogForProfilePic = false" />
    </div>

    <q-separator />

    <div class="q-mt-md">
      <UploadPP   @file-uploaded="handleFileUploadSuccess"
      @upload-error="handleUploadError" />
    </div>
  </q-card>
</q-dialog>
      <q-card flat bordered class="q-mb-lg">
        <q-card-section>
          <!-- Resume Details -->
          <div class="text-h6">{{ resume.full_name }}</div>
          <div><strong class="q-mr-sm">Profession:</strong> {{ resume.profession }}</div>
          <div><strong class="q-mr-sm">Current Address:</strong> {{ resume.address }}</div>
          <div><strong class="q-mr-sm">Email:</strong> {{ resume.user.email }}</div>
          <div><strong class="q-mr-sm">Contact Number:</strong> {{ resume.contact_no }}</div>

          <!-- User Details -->
        
          <div class="text-h6 q-mt-md "><b>User Details</b></div>
          <q-separator  class=" q-mb-sm " />
          <div><strong class="q-mr-sm">Civil Status:</strong>{{ resume.civil_status }}</div>
          <div><strong class="q-mr-sm">Religion:</strong>{{ resume.religion }}</div>
          <div><strong class="q-mr-sm">Nationality:</strong>{{ resume.nationality }}</div>
          <div><strong class="q-mr-sm">Birth Address:</strong>{{ resume.birth_address }}</div>
          <div><strong class="q-mr-sm">Height:</strong>{{ resume.height }} ft.</div>
          <div><strong class="q-mr-sm">Weight:</strong>{{ resume.weight }} kg.</div>
          <div><strong class="q-mr-sm">Gender:</strong> {{ resume.user.gender }}</div>
          <div><strong class="q-mr-sm">Birthday:</strong> {{ resume.user.birthday }}</div>
          <div><strong class="q-mr-sm">Age:</strong> {{ calculateAge(resume.user.birthday) }}</div>
        </q-card-section>
      </q-card>

      <!-- Educational Attainments -->
      <q-separator  />
      <div  class="text-h6 q-mb-md">Educational Attainments</div>
      <q-list bordered  class="q-mb-lg">
        <q-item v-for="education in resume.educational_attainments" :key="education.id">
          <q-item-section>
            <div><strong class="q-mr-sm">Level:</strong> {{ education.level }}</div>
            <div ><strong class="q-mr-sm">Institution:</strong> {{ education.institution }}</div>
            <div v-if="education.level !== 'Primary' && (education.level === 'Secondary' || education.level === 'Tertiary')">
              <strong v-if="education.course" class="q-mr-sm">Course:</strong> {{ education.course }}
            </div>
            <div v-if="education.level !== 'Primary' && (education.level === 'Secondary' || education.level === 'Tertiary')">
              <strong  v-if="education.major" class="q-mr-sm">Major:</strong> {{ education.major }}
            </div>
            <div><strong class="q-mr-sm">Period:</strong> {{ education.period }}</div>
          </q-item-section>
        </q-item>
        <q-btn class="q-mt-sm q-mb-sm q-ml-sm" label="Educational Attainment" @click="openEducationDialog" color="primary" icon="add" />
      </q-list>

      <!-- Work Experiences -->
      <q-separator  />
      <div  class="text-h6 q-mb-md">Work Experiences</div>
      <q-list bordered >
        <q-item class="q-mt-sm" v-for="work in resume.work_experiences" :key="work.id">
          <q-item-section>
            <div style="font-size: 25px;"><strong class="q-mr-sm">{{ work.company_name }}</strong> </div>
            <div><strong class="q-mr-sm">Address:</strong> {{ work.company_address }}</div>
            <div><strong class="q-mr-sm">Position:</strong> {{ work.position }}</div>
            <div><strong class="q-mr-sm">Start Date:</strong> {{ work.start_date }}</div>
            <div><strong class="q-mr-sm">End Date:</strong> {{ work.end_date }}</div>
            <div class="q-mt-sm"><strong class="q-mt-lg">Description:</strong> </div>
                      <div v-for="work2 in work.job_descriptions" :key="work.id">
                        <div style="display: flex; align-items: center;" class="q-ml-sm"><div class="q-mr-sm" style="background-color: black; height: 6px; width: 6px; border-radius: 3px; "></div> 
                        {{ work2.description }}</div>
                      </div>
          </q-item-section>
          
        </q-item>
        <q-btn class="q-mt-sm q-mb-sm q-ml-sm" label="Add Work Experience" @click="openWorkDialog" color="primary" icon="add" />
      </q-list>

<!-- Skills Dialog -->
<q-dialog v-model="showSkillsDialog">
  <q-card class="custom-dialog">
    <q-card-section>
      <div class="text-h6">Add Skills</div>
    </q-card-section>

    <q-card-section>
      <q-input v-model="this.skill_name1" label="Skill Name" />
    </q-card-section>

    <q-card-actions align="right">
      <q-btn flat label="Cancel" v-close-popup />
      <q-btn label="Save" color="primary" @click="addSkill" />
    </q-card-actions>
  </q-card>
</q-dialog>

<br>
<!-- Skills Section -->
<q-separator />
<div class="text-h6 q-mb-md">Skills</div>
<q-list bordered>
  <q-item v-for="skill in resume.skills" :key="skill.id">
    <q-item-section>
      <div style="display: flex; align-items: center;" class="q-ml-sm">
        <div class="q-mr-sm" style="background-color: black; height: 6px; width: 6px; border-radius: 3px;"></div>
        {{ skill.skill_name }}
      </div>
    </q-item-section>
  </q-item>
  <q-btn v-if="!showSkillsDialog" class="q-mt-sm q-mb-sm q-ml-sm" label="Add Skills" @click="openSkillsDialog" color="primary" icon="add" />
</q-list>



 <!-- Certificates -->
<br>
<q-separator />
<div class="text-h6 q-mb-md">Certificates</div>


<q-list bordered>
  <q-item v-for="certificate in resume.certifications" :key="certificate.id">
    <q-item-section>
      <div style="display: flex; align-items: center;" class="q-ml-sm">
        <div class="q-mr-sm" style="background-color: black; height: 6px; width: 6px; border-radius: 3px;"></div>
        {{ certificate.certificate_name }} ({{ certificate.year_received }})
      </div>
    </q-item-section>
  </q-item>
  <q-btn class="q-mt-sm q-mb-sm q-ml-sm" label="Add Certificates" @click="openCertificateDialog" color="primary" icon="add" />
</q-list>

<!-- Certificate Dialog -->
<q-dialog v-model="showCertificateDialog">
  <q-card class="custom-dialog">
    <q-card-section>
      <div class="text-h6">Add Certificate</div>
    </q-card-section>

    <q-card-section>
      <q-input v-model="certificate.cert_name" label="Certificate Name" />
      <q-input v-model="certificate.year" label="Year" type="number" />
    </q-card-section>

    <q-card-actions align="right">
      <q-btn flat label="Cancel" v-close-popup />
      <q-btn label="Save" color="primary" @click="addCertificate" />
    </q-card-actions>
  </q-card>
</q-dialog>



      <!-- Education Dialog -->
      <q-dialog v-model="showEducationDialog" >
        <q-card class="custom-dialog" >
          <q-card-section>
            <div class="text-h6">Add Educational Attainment</div>
          </q-card-section>

          <q-card-section>
            <q-select
              v-model="education.level"
              label="Level"
              :options="['Primary', 'Secondary', 'Tertiary']"
            />

            <!-- Conditionally display Institution field -->
            <q-input
   
              v-model="education.institution"
              label="Institution"
            />

            <!-- Conditionally display Course and Major fields -->
            <q-input
              v-if="education.level === 'Tertiary' || education.level === 'Tertiary'"
              v-model="education.course"
              label="Course"
            />
            <q-input
              v-if="education.level === 'Secondary'"
              v-model="education.course"
              label="Strand (optional for k12)"
            />
            <q-input
              v-if="education.level === 'Secondary' || education.level === 'Tertiary'"
              v-model="education.major"
              label="Major (Optional)"
            />

            <!-- Period input with format hint -->
            <q-input
              v-model="education.period"
              label="Period"
              :rules="[periodRule]"
              hint="example, 2000 to 2009"
              lazy-rules
            />
            
          </q-card-section>

          <q-card-actions align="right">
            <q-btn flat label="Cancel" v-close-popup />
            <q-btn label="Save" color="primary" @click="addEducationalAttainment" />
          </q-card-actions>
        </q-card>
      </q-dialog>

 <!-- Work Dialog -->
<q-dialog v-model="showWorkDialog">
  <q-card class="custom-dialog">
    <q-card-section>
      <div class="text-h6">Work Experience</div>
    </q-card-section>

    <q-card-section>
      <q-input v-model="work.company_name" label="Company Name" />
      <q-input v-model="work.company_address" label="Company Address" />
      <q-input v-model="work.position" label="Position" />
      <q-input v-model="work.start_date" label="Start Date" type="date" />
      <q-input v-model="work.end_date" label="End Date" type="date" />

      <!-- Dynamic Job Descriptions -->
      <div v-for="(job_descriptions, index) in work.job_descriptions" :key="index">
        <q-input v-model="job_descriptions.description" label="Job Description" type="textarea" />
        <q-btn flat label="Remove Description" color="negative" @click="removeDescription(index)" />
      </div>

      <!-- Button to Add New Job Description -->
      <q-btn flat label="Add Description" color="primary" @click="addDescription" />
    </q-card-section>

    <q-card-actions align="right">
      <q-btn flat label="Cancel" v-close-popup />
      <q-btn label="Save" color="primary" @click="addWorkExperience" />
    </q-card-actions>
  </q-card>
</q-dialog>

    </div>
    <create-resume @resume-saved="handleResumeSaved" v-else />
  </q-page>
</template>
<script>
import axios from 'axios';
import CreateResume from '../../components/CreateResume.vue'; // Import your CreateResume component
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;
const ImageBaseUrl = import.meta.env.VITE_IMG_DP;
import UploadPP from '../../components/UploadProfilePic.vue'; 

export default {
  name: 'ResumePage',
  data() {
    return {
      resume: null,
      showDialogForProfilePic:false,
      showWorkDialog: false,
      showEducationDialog: false,
      showSkillsDialog: false, // Added for skills dialog visibility
      showCertificateDialog: false, // Add certificate dialog visibility
      linkenv:null,
      resumeProfilePic:null,
      imgsourcelink:null,
  
      work: {
        company_name: '',
        company_address: '',
        position: '',
        start_date: '',
        end_date: '',
        job_descriptions: [{ description: '' }]
      },
      education: {
        level: '',
        institution: '',
        period: '',
        course: '',
        major: '',
      },
      certificate: {
        cert_name: '',
        year: '',
      },
      skill_name1:'',
    };
  },
  components: {
    CreateResume,UploadPP
  },
  computed: {
    showEducationButton() {
      return this.resume && this.resume.educational_attainments.length > 0;
    },
    showWorkButton() {
      return this.resume && this.resume.work_experiences.length > 0;
    }
  },
  methods: {
    handleFileUploadSuccess(path) {
      this.$router.go(); // or this.$router.replace(...) if gusto mo refresh route
    },
    handleUploadError(message) {
      alert('Upload failed: ' + message);
    },
    openCertificateDialog() {
      this.certificate = { cert_name: '', year: '' }; // Reset certificate data
      this.showCertificateDialog = true;
    },

    async addCertificate() {
      try {
        const token = localStorage.getItem('access_token_applicant');
        const response = await axios.post(`${apiBaseUrl}/resumes/${this.resume.id}/certifications`, {
          cert_name: this.certificate.cert_name,
          year: this.certificate.year
        }, {
          headers: { Authorization: `Bearer ${token}` }
        });
        this.resume.certifications.push(response.data);
        this.showCertificateDialog = false;
      } catch (error) {
        console.error('Error adding certificate:', error);
      }
    },
    openSkillsDialog() {
      this.skill_name1 = "";
      this.showSkillsDialog = true; // Show skills dialog
    },

    async addSkill(skillss) {
      try {
       
        const token = localStorage.getItem('access_token_applicant');
        const response = await axios.post(`${apiBaseUrl}/resumes/${this.resume.id}/skills`, {
          skill_name: this.skill_name1, // Ensure skill_name is bound properly
        }, {
          headers: { Authorization: `Bearer ${token}` }
        });
        this.resume.skills.push(response.data); // Add the new skill to the resume's skills list
        this.showSkillsDialog = false; // Close the skills dialog after saving
      } catch (error) {
        console.error('Error adding skill:', error);
      }
    },

    async fetchResume() {
      this.linkenv = ImageBaseUrl;
      try {
        const token = localStorage.getItem('access_token_applicant');
        const response = await axios.get(`${apiBaseUrl}/resume`, {
          headers: { Authorization: `Bearer ${token}` }
        });

        if (response.data.message === 'Resume not found for this user.') {
          this.resume = false;
        } else {
          this.resume = response.data;
        }
        this.getresumeLink(this.resume.user.profile_picture);
      } catch (error) {
        console.error('Error fetching resume:', error);
        this.resume = false;
      }
    },

    getresumeLink(get){
      this.imgsourcelink = this.linkenv + get.replace('profile_pictures/', '');
    },

    handleResumeSaved() {
      this.fetchResume();
    },

    calculateAge(birthday) {
      const today = new Date();
      const birthDate = new Date(birthday);
      let age = today.getFullYear() - birthDate.getFullYear();
      const monthDifference = today.getMonth() - birthDate.getMonth();
      
      if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDate.getDate())) {
        age--;
      }
      
      return age;
    },

    openWorkDialog() {
      this.work = {
        company_name: '',
        company_address: '',
        position: '',
        start_date: '',
        end_date: '',
        job_descriptions: [{ description: '' }]
      };
      this.showWorkDialog = true;
    },

    openEducationDialog() {
      this.education = {
        level: '',
        institution: '',
        period: '',
        course: '',
        major: '',
      };
      this.showEducationDialog = true;
    },

    async addWorkExperience() {
      try {
        const token = localStorage.getItem('access_token_applicant');
        const response = await axios.post(`${apiBaseUrl}/work-experiences`, {
          resume_id: this.resume.id,
          ...this.work
        }, {
          headers: { Authorization: `Bearer ${token}` }
        });
        this.resume.work_experiences.push(response.data);
        this.showWorkDialog = false;
      } catch (error) {
        console.error('Error adding work experience:', error);
      }
    },

    addDescription() {
    this.work.job_descriptions.push({ description: '' });
  },

  // Method to remove a job description field
  removeDescription(index) {
    this.work.job_descriptions.splice(index, 1);
  },

  // Method to save work experience with multiple job descriptions
  // async addWorkExperience() {
  //   try {
  //     const token = localStorage.getItem('access_token_applicant');
  //     const response = await axios.post(`${apiBaseUrl}/resumes/${this.resume.id}/work-experiences`, {
  //       resume_id: this.resume.id,
  //       company_name: this.work.company_name,
  //       company_address: this.work.company_address,
  //       position: this.work.position,
  //       start_date: this.work.start_date,
  //       end_date: this.work.end_date,
  //       job_descriptions: this.work.job_descriptions
  //     }, {
  //       headers: { Authorization: `Bearer ${token}` }
  //     });

  //     this.resume.work_experiences.push(response.data);
  //     this.showWorkDialog = false;
  //   } catch (error) {
  //     console.error('Error adding work experience:', error);
  //   }
  // },

    async addEducationalAttainment() {
      try {
        const token = localStorage.getItem('access_token_applicant');
        const response = await axios.post(`${apiBaseUrl}/educational-attainments`, {
          resume_id: this.resume.id,
          ...this.education
        }, {
          headers: { Authorization: `Bearer ${token}` }
        });
        this.resume.educational_attainments.push(response.data);
        this.showEducationDialog = false;
      } catch (error) {
        // console.error('Error adding educational attainment:', error);
      }
    },

    periodRule(value) {
      if (!value) return 'Period is required';
      const yearRangePattern = /^[0-9]{4}\s*to\s*[0-9]{4}$/;
      const monthDatePattern = /^(?:[A-Za-z]+ \d{4}|[A-Za-z]+ \d{1,2} \d{4})$/;
      return yearRangePattern.test(value) || monthDatePattern.test(value) ? true : 'Invalid period format';
    }
  },
  mounted() {
    this.fetchResume();
  }
};
</script>


<style scoped>
/* Hover effect */
 .overlay:hover {
  opacity: 0.7 !important;
}
</style>

<!-- <style scoped>
.q-page {
  max-width: 600px;
  margin: 0 auto;
}

.q-mb-lg {
  margin-bottom: 1.5rem;
}

.q-mb-md {
  margin-bottom: 1rem;
}

.q-mt-sm {
  margin-top: 0.5rem;
}

.q-mb-sm {
  margin-bottom: 0.5rem;
}

.q-ml-sm {
  margin-left: 0.5rem;
}
</style> -->
