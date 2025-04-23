<template>
  <q-page padding>
    <div style="border-radius: 5px; overflow: hidden;">
      <q-card-section class="header-format-w">
        <div class="text-h6">
          <q-icon name="business" size="25px" class="q-mr-sm"/>Employers
        </div>
      </q-card-section>
      <q-card v-for="entry in employers" :key="entry.employer.id" class="my-card">
        <q-card-section>
          <div class="q-gutter-md" style="display: flex; justify-content: space-between;">
            <div>
              <q-icon name="account_circle" size="50px"/>
              <q-btn flat @click="toggleApplicants(entry)">
                {{ entry.employer.first_name }} {{ entry.employer.last_name }}
              </q-btn>
            </div>
            <!-- <q-btn color="primary" @click="toggleApplicants(entry)">View Applicants</q-btn> -->
            <q-btn
              flat
            
              color="primary"
              @click="toggleApplicants(entry)"
            >
              <q-icon 
                name="expand_more" 
                :class="{
                  'rotate-icon': true,
                  'rotated': entry.showApplicants
                }"
                size="40px"
            />
            </q-btn>

          </div>
          <q-slide-transition>
            <div v-if="entry.showApplicants" class="q-mt-sm">
              <q-table
                :rows="entry.applicants"
                :columns="applicantColumns"
                row-key="id"
                :pagination.sync="pagination"
              >
                <template v-slot:body-cell-action="props">
                  <q-td v-if="props.row" class="q-pa-none" style="display: flex; justify-content: center;">
                    <q-btn @click="showDialog(props.row)" label="View Details" color="secondary" class="q-mr-sm"/>
                    <q-btn @click="confirmUnlink(props.row)" label="Unlink Permanently" color="negative"/>
                  </q-td>
                </template>
              </q-table>
            </div>
          </q-slide-transition>
        </q-card-section>
      </q-card>

      <!-- Applicant Details Dialog -->
      <q-dialog v-model="dialog" persistent>
        <q-card>
          <q-card-section>
            <div>
              <h5>{{ selectedApplicant.full_name }}</h5>
              <p><strong>Address:</strong> {{ selectedApplicant.address }}</p>
              <p><strong>Birth Address:</strong> {{ selectedApplicant.birth_address }}</p>
              <p><strong>Height:</strong> {{ selectedApplicant.height }}</p>
              <p><strong>Weight:</strong> {{ selectedApplicant.weight }}</p>
              <p><strong>Objectives:</strong> {{ selectedApplicant.objectives }}</p>
              <p><strong>Civil Status:</strong> {{ selectedApplicant.civil_status }}</p>
              <p><strong>Religion:</strong> {{ selectedApplicant.religion }}</p>
              <p><strong>Nationality:</strong> {{ selectedApplicant.nationality }}</p>
              <p><strong>Contact No:</strong> {{ selectedApplicant.contact_no }}</p>
              <p><strong>Profession:</strong> {{ selectedApplicant.profession }}</p>
            </div>
          </q-card-section>
          <q-card-actions>
            <q-btn @click="dialog = false" label="Close" color="primary"/>
          </q-card-actions>
        </q-card>
      </q-dialog>

      <!-- Unlink Confirmation Dialog -->
      <q-dialog v-model="confirmDialog" persistent>
        <q-card>
          <q-card-section class="header-format-w">
            <div>
              <div class="text-h6">Confirm Delete</div>
              <p>Are you sure you want to unlink {{ selectedApplicant.full_name }}?</p>
              <i>you cannot undo this process!</i>
            </div>
          </q-card-section>
          <q-card-actions align="right">
            <q-btn flat label="No" color="primary" @click="confirmDialog = false"/>
            <q-btn flat label="Yes" color="negative" @click="unlinkConfirmed"/>
          </q-card-actions>
        </q-card>
      </q-dialog>
    </div>
  </q-page>
</template>

<script>
import axios from 'axios';
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;
export default {
  name: 'EmployerHired',
  data() {
    return {
      employers: [],
      dialog: false,
      confirmDialog: false,
      selectedApplicant: {},
      pagination: { rowsPerPage: 5 },
      applicantColumns: [
        { name: 'full_name', label: 'Full Name', align: 'left', field: 'full_name' },
        { name: 'address', label: 'Address', align: 'left', field: 'address' },
        { name: 'profession', label: 'Profession', align: 'left', field: 'profession' },
        { name: 'action', label: 'Action', align: 'center' },
      ],
      applicantToUnlink: null,
    };
  },
  methods: {
    async unlinkApplicant(id) {
      try {
        const token = localStorage.getItem('access_token');
        await axios.delete(`${apiBaseUrl}/hire/${id}/unlink-permanently`, {
          headers: { Authorization: `Bearer ${token}` }
        });
        this.fetchApi();
      } catch (error) {
        console.error('Error unlinking hired record:', error.response?.data || error.message);
      }
    },
    async fetchApi() {
      try {
        const token = localStorage.getItem('access_token');
        const response = await axios.get(`${apiBaseUrl}/displayAllDataDropdown`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });

        if (Array.isArray(response.data.data)) {
          this.employers = response.data.data.map(entry => ({
            ...entry,
            showApplicants: false,
          }));
        } else {
          console.error('Expected an array but got:', response.data);
        }
      } catch (error) {
        console.error('Failed to fetch user data:', error);
      }
    },
    toggleApplicants(entry) {
      entry.showApplicants = !entry.showApplicants;
    },
    showDialog(applicant) {
      this.selectedApplicant = applicant;
      this.dialog = true;
    },
    confirmUnlink(applicant) {
      this.selectedApplicant = applicant;
      this.confirmDialog = true;
    },
    unlinkConfirmed() {
      this.unlinkApplicant(this.selectedApplicant.id);
      this.confirmDialog = false;
    },
  },
  mounted() {
    this.fetchApi();
  },
};
</script>

<style scoped>
.my-card {
  border-bottom: 3px solid rgb(255, 187, 0);
  margin-bottom: 5px;
  box-shadow: none !important;
}
.rotate-icon {
  transition: transform 0.3s ease;
}

.rotated {
  transform: rotate(180deg);
}
</style>
