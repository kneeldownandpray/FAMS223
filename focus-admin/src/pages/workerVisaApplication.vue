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
          <template v-slot:body-cell-actions="props">
            <q-td :props="props" class="text-center">
              <q-btn
                label="Done Transaction"
                color="green"
                size="sm"
                @click="handleDoneTransaction(props.row.worker_id)"
              />
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
    })),
    {
      name: 'actions',
      label: 'Actions',
      field: 'worker_id',
      align: 'center'
    }
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
    async handleDoneTransaction(workerId) {
      console.log(workerId);
  // try {
  //   const token = localStorage.getItem('access_token');
  //   await axios.post(`${apiBaseUrl}/visa-statuses/${workerId}/done`, {}, {
  //     headers: {
  //       Authorization: `Bearer ${token}`
  //     }
  //   });
  //   this.$q.notify({
  //     type: 'positive',
  //     message: 'Transaction marked as done!'
  //   });
  //   this.fetchVisaStatuses(); // refresh list
  // } catch (error) {
  //   console.error('Error marking transaction as done:', error);
  //   this.$q.notify({
  //     type: 'negative',
  //     message: 'Something went wrong.'
  //   });
  // }
},
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


