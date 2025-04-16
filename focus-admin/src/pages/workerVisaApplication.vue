<template>
  <q-page padding>
    <q-card flat bordered>
      <q-card-section class="text-h6 row items-center">
        <q-icon name="checklist" class="q-mr-sm" />
        Visa Status Tracker
      </q-card-section>


      
      <q-separator />



      <q-card-section>
        <!-- Filter input -->
        <div class="q-mb-md flex" style="flex-direction: row; align-items: center; gap:10px">
          <q-input filled v-model="filters.name" label="Search Name" class="col-12 col-md-4" />
          <q-select filled clearable :options="professionOptions" v-model="filters.profession" label="Profession" class="col-12 col-md-4" style="width: 190px;" />
            <q-select
            filled
            v-model="filters.visa_step"
            :options="visaSteps"
            label="Filter by Visa Step"
            emit-value
            map-options
            clearable
            style="width: 190px;"
          /> 
          <q-btn icon="search" label="Search" color="primary"  @click="fetchVisaStatuses" class="q-pa-md" />
        </div>

        <q-table
          :rows="filteredVisaStatusList"
          :columns="columns"
          row-key="worker_id"
          :pagination.sync="pagination"
          flat
          :loading="loading"
          hide-bottom
        >
          <!-- Dynamic visa status checkbox cells -->
          <template
            v-for="status in visaStatusKeys"
            v-slot:[`body-cell-${status}`]="props"
            :key="status"
          >
            <q-td :props="props" class="text-center">
              <template v-if="props.row.visa_status[status] !== null">
                <q-checkbox
                  dense
                  :model-value="props.row.visa_status[status] === 1"
                  @update:model-value="confirmUpdate(props.row.worker_id, status, $event)"
                />
              </template>
              <template v-else>
                <span>None</span>
              </template>
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- Confirmation Dialog -->
    <q-dialog v-model="confirmDialog">
      <q-card>
        <q-card-section class="row items-center justify-between">
          <div class="text-h6">Confirm Update</div>
          <q-btn flat icon="close" @click="confirmDialog = false" />
        </q-card-section>

        <q-card-section>
          <p>{{ confirmationMessage }}</p>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="grey" @click="confirmDialog = false" />
          <q-btn color="green" label="Yes, Update" @click="updateVisaStatus" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import axios from 'axios';

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

export default {
  name: 'VisaStatusMatrix',
  data() {
    return {
      
      visaStatusList: [],
      visaStatusKeys: [
        'application_received',
        'interview_employer_confirmation',
        'requirements',
        'skill_assessment',
        'visa_preparation',
        'visa_lodged',
        'medical_biometrics',
        'awaiting_decision',
        'visa_outcome',
        'ready_to_fly'
      ],
      loading: false,
      pagination: {
        page: 1,
        rowsPerPage: 10
      },
      confirmDialog: false,
      confirmationMessage: '',
      selectedWorkerId: null,
      selectedField: '',
      selectedValue: null,
      filter: '',
      filters: {
        name: '',
  profession: '',
  visa_step: ''
      },

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
      visaSteps: [
  { label: 'Application Received', value: 'application_received' },
  { label: 'Interview Employer Confirmation', value: 'interview_employer_confirmation' },
  { label: 'Requirements', value: 'requirements' },
  { label: 'Skill Assessment', value: 'skill_assessment' },
  { label: 'Visa Preparation', value: 'visa_preparation' },
  { label: 'Visa Lodged', value: 'visa_lodged' },
  { label: 'Medical Biometrics', value: 'medical_biometrics' },
  { label: 'Awaiting Decision', value: 'awaiting_decision' },
  { label: 'Visa Outcome', value: 'visa_outcome' },
  { label: 'Ready To Fly', value: 'ready_to_fly' },
],

    };
  },
  computed: {
    columns() {
      return [
        {
          name: 'applicant_employer',
          label: 'Name (Employer)',
          field: row => `${row.applicant_name} (${row.employer_name})`,
          align: 'left'
        },
        {
          name: 'profession',
          label: 'Profession',
          field: 'profession',
          align: 'left'
        },
        ...this.visaStatusKeys.map(key => ({
          name: key,
          label: this.formatLabel(key),
          field: row => row.visa_status[key],
          align: 'center'
        }))
      ];
    },
    filteredVisaStatusList() {
      if (!this.filter) return this.visaStatusList;

      const keyword = this.filter.toLowerCase();
      return this.visaStatusList.filter(row => {
        return (
          row.applicant_name.toLowerCase().includes(keyword) ||
          row.employer_name.toLowerCase().includes(keyword) ||
          row.profession.toLowerCase().includes(keyword)
        );
      });
    }
  },
  methods: {
    async fetchVisaStatuses() {
      this.loading = true;
      try {
        const token = localStorage.getItem('access_token');
        const response = await axios.get(`${apiBaseUrl}/visa-statuses`, {
          headers: {
            Authorization: `Bearer ${token}`
          },
          params: {
            page: this.pagination.page,
            per_page: this.pagination.rowsPerPage,
            name: this.filters.name,
            profession: this.filters.profession,
            visa_step: this.filters.visa_step
          }
        });

        this.visaStatusList = response.data.data;
      } catch (error) {
        console.error('Error fetching visa statuses:', error);
      } finally {
        this.loading = false;
      }
    },

    confirmUpdate(workerId, field, value) {
      this.selectedWorkerId = workerId;
      this.selectedField = field;
      this.selectedValue = value ? 1 : 0;

      this.confirmationMessage = value
        ? `Are you sure you want to approve "${this.formatLabel(field)}" for this applicant?`
        : `Are you sure you want to remove approval for "${this.formatLabel(field)}"?`;

      this.confirmDialog = true;
    },

    async updateVisaStatus() {
      this.confirmDialog = false;
      try {
        const token = localStorage.getItem('access_token');
        await axios.put(
          `${apiBaseUrl}/visa-statuses/${this.selectedWorkerId}`,
          {
            field: this.selectedField,
            value: this.selectedValue
          },
          {
            headers: {
              Authorization: `Bearer ${token}`
            }
          }
        );

        this.fetchVisaStatuses();
      } catch (error) {
        console.error('Failed to update visa status:', error);
      }
    },

    formatLabel(key) {
      return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }
  },
  mounted() {
    this.fetchVisaStatuses();
  }
};
</script>



<!-- 

<template>
  <q-page padding>
    <q-card>
      <q-card-section>
        <div class="text-h6">Visa Status Management</div>
      </q-card-section>

      <q-card-section>


        <div class="row q-col-gutter-md">
          <q-input filled v-model="filters.name" label="Search Name" class="col-12 col-md-4" />
          <q-input filled v-model="filters.profession" label="Search Profession" class="col-12 col-md-4" />
          <q-select
            filled
            v-model="filters.visa_step"
            :options="visaSteps"
            label="Filter by Visa Step"
            emit-value
            map-options
            clearable
            class="col-12 col-md-4"
          />
          <q-btn label="Search" color="primary" class="q-mt-md" @click="fetchVisaStatuses" />
        </div>

     
        <q-table
          :rows="visaStatuses"
          :columns="columns"
          row-key="applicant_name"
          :loading="loading"
          class="q-mt-lg"
        >
          <template v-slot:body-cell="props">
            <q-td :props="props">
              <span v-if="typeof props.value === 'boolean'">
                <q-icon :name="props.value ? 'check' : 'close'" :color="props.value ? 'green' : 'red'" />
              </span>
              <span v-else>{{ props.value }}</span>
            </q-td>
          </template>

          <template v-slot:body-cell-action="props">
            <q-td :props="props">
              <q-btn
                color="green"
                size="sm"
                icon="check"
                @click="confirmApproval(props.row)"
              />
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>


    <q-dialog v-model="dialog.visible">
      <q-card>
        <q-card-section class="text-h6">
          {{ dialog.title }}
        </q-card-section>

        <q-card-section>
          {{ dialog.message }}
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" v-close-popup />
          <q-btn flat label="Approve" color="primary" @click="approveStatus(dialog.row)" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useQuasar } from 'quasar'

const $q = useQuasar()

const visaStatuses = ref([])
const loading = ref(false)
const token = localStorage.getItem('access_token')
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL

const filters = ref({
  name: '',
  profession: '',
  visa_step: ''
})

const visaSteps = [
  { label: 'Application Received', value: 'application_received' },
  { label: 'Interview Employer Confirmation', value: 'interview_employer_confirmation' },
  { label: 'Requirements', value: 'requirements' },
  { label: 'Skill Assessment', value: 'skill_assessment' },
  { label: 'Visa Preparation', value: 'visa_preparation' },
  { label: 'Visa Lodged', value: 'visa_lodged' },
  { label: 'Medical Biometrics', value: 'medical_biometrics' },
  { label: 'Awaiting Decision', value: 'awaiting_decision' },
  { label: 'Visa Outcome', value: 'visa_outcome' },
  { label: 'Ready To Fly', value: 'ready_to_fly' },
]

const columns = [
  { name: 'employer_name', label: 'Name (Employer)', field: 'employer_name', align: 'left' },
  { name: 'profession', label: 'Profession', field: 'profession', align: 'left' },
  { name: 'application_received', label: 'Application Received', field: row => row.visa_status.application_received, align: 'center' },
  { name: 'interview_employer_confirmation', label: 'Interview Employer Confirmation', field: row => row.visa_status.interview_employer_confirmation, align: 'center' },
  { name: 'requirements', label: 'Requirements', field: row => row.visa_status.requirements, align: 'center' },
  { name: 'skill_assessment', label: 'Skill Assessment', field: row => row.visa_status.skill_assessment, align: 'center' },
  { name: 'visa_preparation', label: 'Visa Preparation', field: row => row.visa_status.visa_preparation, align: 'center' },
  { name: 'visa_lodged', label: 'Visa Lodged', field: row => row.visa_status.visa_lodged, align: 'center' },
  { name: 'medical_biometrics', label: 'Medical Biometrics', field: row => row.visa_status.medical_biometrics, align: 'center' },
  { name: 'awaiting_decision', label: 'Awaiting Decision', field: row => row.visa_status.awaiting_decision, align: 'center' },
  { name: 'visa_outcome', label: 'Visa Outcome', field: row => row.visa_status.visa_outcome, align: 'center' },
  { name: 'ready_to_fly', label: 'Ready To Fly', field: row => row.visa_status.ready_to_fly, align: 'center' },
  { name: 'action', label: 'Action', field: 'action', align: 'center' }
]

const dialog = ref({
  visible: false,
  title: '',
  message: '',
  row: null
})

function confirmApproval(row) {
  dialog.value = {
    visible: true,
    title: 'Approve Visa Step',
    message: `Are you sure you want to approve the next visa step for ${row.applicant_name}?`,
    row
  }
}

async function approveStatus(row) {
  dialog.value.visible = false
  try {
    await axios.post(`${apiBaseUrl}/visa-statuses/${row.worker_id}/approve`, {}, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })
    $q.notify({ type: 'positive', message: 'Visa step approved successfully!' })
    fetchVisaStatuses()
  } catch (error) {
    console.error(error)
    $q.notify({ type: 'negative', message: 'Failed to approve visa step.' })
  }
}

async function fetchVisaStatuses() {
  loading.value = true
  try {
    const response = await axios.get(`${apiBaseUrl}/visa-statuses`, {
      headers: {
        Authorization: `Bearer ${token}`
      },
      params: {
        name: filters.value.name,
        profession: filters.value.profession,
        visa_step: filters.value.visa_step
      }
    })
    visaStatuses.value = response.data.data
  } catch (error) {
    console.error(error)
    $q.notify({ type: 'negative', message: 'Failed to fetch visa statuses.' })
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchVisaStatuses()
})
</script> -->
