<template>
  <q-page padding>
    <q-card flat bordered>
      <q-card-section class="text-h6 row items-center">
        <q-icon name="history" class="q-mr-sm" size="md" />
        Visa Transaction History
      </q-card-section>

      <q-separator />

      <q-card-section>
        <!-- Filter input -->
        <div class="q-mb-md flex" style="flex-direction: row; align-items: center; gap:10px">
          <q-input clearable filled v-model="filters.worker_name" label="Search Worker Name" class="col-12 col-md-4" />
          <q-input clearable filled v-model="filters.employer_name" label="Search Employer Name" class="col-12 col-md-4" />
          <q-select filled clearable :options="professionOptions" v-model="filters.profession" label="Profession" class="col-12 col-md-4" style="width: 190px;" 
          @update:model-value="fetchVisaStatusHistory" />
          <q-select
            filled
            v-model="filters.status"
            :options="visaStatusOptions"
            label="Status"
            emit-value
            map-options
            clearable
            style="width: 190px;"
           @update:model-value="fetchVisaStatusHistory"
          />
          <q-btn icon="search" label="Search" color="primary" @click="fetchVisaStatusHistory" class="q-pa-md" />
        </div>

        <q-table
          :rows="visaStatusHistoryList"
          :columns="columns"
          row-key="worker_id"
          :pagination.sync="pagination"
          flat
          :loading="loading"
          hide-bottom
        >


          <!-- name_combo as button -->
          <template v-slot:body-cell-name_combo="props">
            <q-td :props="props">
              <q-btn
                flat
                dense
                size="13px"
                class="q-pa-sm"
                color="primary"
                :label="`${props.row.user?.first_name || ''} ${props.row.user?.last_name || ''} (${props.row.employer?.first_name || ''} ${props.row.employer?.last_name || ''})`"
                @click="onNameClick(props.row)"
              />
            </q-td>
          </template>

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
                label="Revert"
                color="red"
                size="sm"
                @click="handleDoneTransaction(props.row.worker_id, 0, props.row)"
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
  <q-dialog v-model="WorkerDetailDialog">
          <q-card>
            <q-card-section class="header-format-w">
              <div class="flex" style="justify-content: space-between;">
              <div class="text-h6">Requirements</div> 
              <q-btn icon="close" flat @click="this.WorkerDetailDialog = false"  />
            </div>
        </q-card-section>
           <DisplayWorkerSpecificDialog :idOfRequirement="userIDtoManage" @emittest="noRequirement(emittest)"/>
          </q-card>
    </q-dialog>
</template>

<script>
import axios from 'axios';
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;
export default {
  components: {
    DisplayWorkerSpecificDialog
  },
  data() {
    return {
      worker_id:null,
      selected_row:null,
      sected_row_status:null,
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
      userIDtoManage:null,
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
    columns() {
      return [
        {
          name: 'name_combo',
          label: 'Worker (Employer)',
          field: row => {
            const workerName = `${row.user?.first_name || ''} ${row.user?.last_name || ''}`;
            const employerName = `${row.employer?.first_name || ''} ${row.employer?.last_name || ''}`;
            return `${workerName} (${employerName})`;
          },
          align: 'left'
        },
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

            const timeString = date.toLocaleTimeString([], timeOptions); // e.g. 3:45 PM
            const dateString = date.toLocaleDateString(undefined, dateOptions); // e.g. April 22, 2025

            return `${timeString} (${dateString})`;
          }
        },
        {
          name: 'Approved',
          label: 'Approved by',
          field: row => {
           
            const employerName = `${row.approved_by?.first_name || ''} `;
            return `${employerName}`;
          },
          align: 'center'
        },

        { name: 'actions', label: 'Actions', align: 'center' }
      ];
    }
  },
  mounted() {
 
  },
  methods: {
  }
};
</script>

<style scoped>
/* Custom styling if needed */
</style>
