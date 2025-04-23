<template>
  <q-page class="q-pa-md bg-grey-2">
    <q-card flat bordered class="q-pa-md">
      <q-card-section>
        <!-- <div class="text-h5">Resume</div> -->
        <div v-if="users?.[0]?.resume">
          <div class="q-mt-sm text-h5 q-mb-sm" style="font-weight: 600;"><strong></strong> {{ users[0].resume.full_name }}</div>
          <div><strong>Address:</strong> {{ users[0].resume.address }}</div>
          <div><strong>Birth Address:</strong> {{ users[0].resume.birth_address }}</div>
          <div><strong>Birthday:</strong> {{ users[0].birthday }}</div>
          <div><strong>Gender:</strong> {{ users[0].gender }}</div>
          <div><strong>Age:</strong> {{ users[0].age }}</div>
          <div><strong>Contact No:</strong> {{ users[0].resume.contact_no }}</div>
          <div><strong>Religion:</strong> {{ users[0].resume.religion }}</div>
          <div><strong>Nationality:</strong> {{ users[0].resume.nationality }}</div>
          <div><strong>Objective:</strong> {{ users[0].resume.objectives }}</div>
          <div><strong>Civil Status:</strong> {{ users[0].resume.civil_status }}</div>
        </div>
      </q-card-section>

      <q-separator />

      <q-card-section>
        <div class="text-h6">Educational Attainments</div>
        <q-list bordered v-if="users?.[0]?.resume?.educational_attainments?.length">
          <q-item v-for="edu in users[0].resume.educational_attainments" :key="edu.id">
            <q-item-section>
              <div><strong>{{ edu.level }}</strong> - {{ edu.institution }} ({{ edu.period }})</div>
              <div v-if="edu.course">Course: {{ edu.course }}</div>
              <div v-if="edu.major">Major: {{ edu.major }}</div>
            </q-item-section>
          </q-item>
        </q-list>
      </q-card-section>

      <q-separator />

      <q-card-section>
  <div class="text-h6">Work Experiences</div>
  <q-list bordered v-if="users?.[0]?.resume?.work_experiences?.length">
    <q-item v-for="work in users[0].resume.work_experiences" :key="work.id">
      <q-item-section>
        <div><strong>{{ work.position }}</strong> at {{ work.company_name }}</div>
        <div>{{ work.company_address }}</div>
        <div>{{ work.start_date }} to {{ work.end_date }}</div>

        <!-- Job Descriptions -->
        <div v-if="work.job_descriptions?.length">
          <div class="q-mt-sm"><em>Job Responsibilities:</em></div>
          <ul class="q-ml-md">
            <li v-for="desc in work.job_descriptions" :key="desc.id">
              {{ desc.description }}
            </li>
          </ul>
        </div>
      </q-item-section>
    </q-item>
  </q-list>
</q-card-section>


      <q-separator />

      <q-card-section>
        <div class="text-h6">Skills</div>
        <q-chip
          v-for="skill in users?.[0]?.resume?.skills"
          :key="skill.id"
          color="gray"
          class="q-mb-sm cursor-pointer"
          size="15px"
        >
          {{ skill.skill_name }}
        </q-chip>
      </q-card-section>

      <q-separator />

      <q-card-section>
        <div class="text-h6">Certifications</div>
        <q-list bordered v-if="users?.[0]?.resume?.certifications?.length">
          <q-item v-for="cert in users[0].resume.certifications" :key="cert.id">
            <q-item-section>
              <div>{{ cert.certificate_name }} - {{ cert.year_received }}</div>
            </q-item-section>
          </q-item>
        </q-list>
      </q-card-section>

      <q-separator />

      <q-card-section>
        <div class="text-h6">Requirements</div>
        <q-list bordered v-if="requirements.length">
          <q-item v-for="req in requirements" :key="req.id">
            <q-item-section>
              <div><strong>{{ req.requirement_type.name }}</strong> - {{ req.original_name }}</div>
              <div>
                Status:
                <q-badge :color="req.status === 'accepted' ? 'green' : 'red'">
                  {{ req.status }}
                </q-badge>
              </div>
            </q-item-section>
          </q-item>
        </q-list>
        <div bordered v-else style="color: red;"><i> No Requirement Submitted </i></div>
      </q-card-section>

      <q-separator />

      <q-card-section v-if="users?.[0]?.resume?.user_videos?.length">
        <div class="text-h6">User Videos</div>
        <div
          v-for="video in users[0].resume.user_videos"
          :key="video.id"
          class="q-my-md"
        >
          <div><strong>{{ video.title }}</strong></div>
          <div>{{ video.description }}</div>
          <iframe
            :src="video.youtube_link.replace('watch?v=', 'embed/')"
            frameborder="0"
            allowfullscreen
            class="q-mt-sm"
            style="width: 100%; height: 300px;"
          ></iframe>
        </div>
      </q-card-section>

      <q-separator />

    </q-card>
  </q-page>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminRequirements',
  props: {
    idOfRequirement: {
      type: [Number, String],
      required: true
    }
  },
  data() {
    return {
      requirements: [],
      users: null,
      token: localStorage.getItem('access_token'),
      apiBaseUrl: import.meta.env.VITE_API_BASE_URL
    };
  },
  methods: {
    async fetchRequirements() {
      try {
        const url = this.idOfRequirement
          ? `${this.apiBaseUrl}/admin/user-requirements?user_id=${this.idOfRequirement}`
          : `${this.apiBaseUrl}/admin/user-requirements`;

        const response = await axios.get(url, {
          headers: { Authorization: `Bearer ${this.token}` }
        });

        this.requirements = response.data.map(req => ({
          ...req,
          showNoteInput: !!req.note
        }));

        this.fetchSpecificDetails();
      } catch (error) {
        console.error('Failed to fetch requirements:', error);
      }
    },

    async fetchSpecificDetails() {
      try {
        const response = await axios.get(`${this.apiBaseUrl}/validUsers`, {
          headers: { Authorization: `Bearer ${this.token}` },
          params: {
            id: this.idOfRequirement,
            account_type: 6
          }
        });
        this.users = response.data.data;
      } catch (error) {
        console.error('Failed to fetch user details:', error);
      }
    }
  },
  mounted() {
    this.fetchRequirements();
  }
};
</script>

<style scoped>
.requirement-card {
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  width: 100%;
}
</style>
