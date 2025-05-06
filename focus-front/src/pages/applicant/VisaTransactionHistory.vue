<template>
  <q-page padding>
    <q-card flat bordered>
      <q-card-section class="text-h6 row items-center">
        <q-icon name="history" class="q-mr-sm" size="md" />
        Visa Transaction History
      </q-card-section>

      <q-separator />

      <q-card-section>

        <q-table
          :rows="visaStatusHistoryList"
          :columns="columns"
          row-key="worker_id"
          :pagination.sync="pagination"
          flat
          :loading="loading"
          hide-bottom
        >



          <!-- Status column with colored flat buttons -->
          <template v-slot:body-cell-status="props">
            <q-td :props="props" class="text-center">
              <q-btn
                :label="props.row.status == 2 ? 'Incomplete' : 'Complete'"
                :color="props.row.status == 2 ? 'red' : 'green'"
                flat
                class="q-pa-sm"
                dense
                size="13px"
              />
            </q-td>
          </template>

          <!-- Actions -->
          <template v-slot:body-cell-actions="props">
            <q-td :props="props" class="text-center">
              <!-- <q-btn
                label="Done Transaction"
                color="green"
                size="md"
                @click="handleDoneTransaction(props.row.worker_id, 1, props.row)"
                class="q-mr-sm"
              /> -->
              <q-btn
                label="Review More"
                color="green"
                size="sm"
                @click="handleDoneTransaction(props.row)"
              />
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <q-dialog v-model="confirmDialog">
  <q-card>
    <q-card-section class="row items-center justify-between">
      <div class="text-h6">Visa Transaction</div>
      <q-btn flat icon="close" @click="confirmDialog = false" />
    </q-card-section>

    <q-separator />

    <q-card-section>
      <div
        v-for="(value, key) in filteredVisaStatus"
        :key="key"
        class="q-mb-sm"
      >
        <div class="row justify-between items-center">
          <div class="text-subtitle2 text-capitalize">
            {{ formatKey(key) }}
          </div>
          <q-badge
            :color="value == 1 ? 'green' : 'red'"
            :label="value == 1 ? 'Complete' : 'Incomplete'"
            class="q-ml-sm"
          />
        </div>
      </div>
    </q-card-section>

    <!-- <q-card-actions align="right">
      <q-btn flat label="Close" color="grey" @click="confirmDialog = false" />
    </q-card-actions> -->
  </q-card>
</q-dialog>

    
  </q-page>

</template>
<script>
import axios from 'axios';


const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

export default {

  data() {
    return {
      worker_id: null,
      selected_row: null,
      sected_row_status: null,
      WorkerDetailDialog: false,
      filters: {
        worker_name: '',
        employer_name: '',
        profession: '',
        status: null,
      },
      pagination: {
        page: 1,
        rowsPerPage: 10,
      },
      loading: false,
      visaStatusHistoryList: [],
      userIDtoManage: null,
      visaStatusOptions: [
        { label: 'Completed', value: 1 },
        { label: 'Incomplete', value: 2 },
      ],
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
      confirmDialog: false,
      confirmationMessage: '',
    };
  },
  computed: {
    filteredVisaStatus() {
    const { id, user_id, application_status, created_at, updated_at, skill_assessment, ...rest } = this.selected_row;

    const filtered = { ...rest };

    if (skill_assessment !== 3) {
      filtered.skill_assessment = skill_assessment;
    }

    return filtered;
  },
    columns() {
      return [
        
        { name: 'profession', label: 'Profession', align: 'left', field: 'profession' },
        { name: 'status', label: 'Visa Status', align: 'center', field: 'status', sortable: true },
        {
          name: 'Date',
          label: 'Date',
          align: 'center',
          field: 'completed_at',
          format: val => {
            if (!val) return '';
            const date = new Date(val);
            const timeOptions = { hour: 'numeric', minute: 'numeric', hour12: true };
            const dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
            return `${date.toLocaleTimeString([], timeOptions)} (${date.toLocaleDateString(undefined, dateOptions)})`;
          }
        },
        { name: 'actions', label: 'Review', align: 'center' }
      ];
    }
  },
  mounted() {
    this.fetchVisaStatusHistory();
  },
  methods: {
    formatKey(key) {
    return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
  },
    async fetchVisaStatusHistory() {
      this.loading = true;
      try {
        const token = localStorage.getItem('access_token_applicant');
        const response = await axios.get(`${apiBaseUrl}/worker/visa/history`, {
          headers: {
            Authorization: `Bearer ${token}`
          },
        });

        // Set the visaStatusHistoryList from response
      
        this.visaStatusHistoryList = response.data.data;
        console.log(this.visaStatusHistoryList);
      } catch (error) {
        console.error('Error fetching visa status history:', error);
      } finally {
        this.loading = false;
      }
    },

    onNameClick(row) {
      this.userIDtoManage = row.user_id;
      this.WorkerDetailDialog = true;
    },

    handleDoneTransaction(rowData) {
     
      this.selected_row = rowData;
      console.log(this.selected_row);
      // this.worker_id = workerId;
      // this.selected_row = rowData;
      // this.sected_row_status = statusValue;
      // this.confirmationMessage = `Are you sure you want to ${statusValue === 1 ? 'mark this as done' : 'revert this transaction'}?`;
      this.confirmDialog = true;
    },

    async updateVisaStatus() {
      const token = localStorage.getItem('access_token_employer');
      try {
        await axios.put(`${apiBaseUrl}/api/visa/update-status/${this.worker_id}`, {
          status: this.sected_row_status
        }, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });

        this.confirmDialog = false;
        this.fetchVisaStatusHistory(); // Refresh table
      } catch (error) {
        console.error('Failed to update status:', error);
      }
    },

    noRequirement(value) {
      this.WorkerDetailDialog = value;
    }
  }
};
</script>
