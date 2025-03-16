<template>
  <q-page padding>
    <q-card>
      <q-card-section>
        <div class="text-h6">Daily Vehicle Report</div>
      </q-card-section>

      <!-- Dropdown for Selecting Days -->
      <q-card-section class="q-gutter-md">
        <q-select
          v-model="selectedDays"
          :options="dayOptions"
          label="Select Days"
          outlined
          dense
          @update:model-value="fetchDailyReport"
        />
      </q-card-section>

      <!-- Table Displaying Report -->
      <q-card-section>
        <q-table
          flat bordered
          :rows="reportData"
          :columns="columns"
          row-key="date"
          :loading="loading"
        >
          <!-- Add "View" Button in the Last Column -->
          <template v-slot:body-cell-actions="props">
            <q-td>
              <q-btn
                label="Views"
                color="primary"
                dense
                @click="openControllerData(props.row.date)"
              />
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- Maximized ControllerData Dialog -->
    <q-dialog v-model="showControllerDataDialog" :persistent="true" :maximized="true" :style="{ zIndex: '11111' }">
      <q-card>
        <q-card-section>
          <q-btn color="red" label="Close" @click="closeControllerDataDialog" />
        </q-card-section>
        <q-card-section>
          <ControllerData :date2="this.selectedDate"  />
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import axios from "axios";
import ControllerData from "../../components/ControllerData.vue";

export default {
  components: { ControllerData },
  data() {
    return {
      selectedDays: 100, // Default to 100 days
      dayOptions: [7, 14, 30, 60, 100, 500, 700, 1000], // Dropdown options
      reportData: [],
      loading: false,
      showControllerDataDialog: false, // Control dialog visibility
      selectedDate: null, // Store selected date
    };
  },
  mounted() {
    this.fetchDailyReport(); // Fetch default 100-day report on load
  },
  methods: {
    async fetchDailyReport() {
      this.loading = true;
      try {
        const token = localStorage.getItem("access_token_employer"); // Get token
        const response = await axios.get(
          `${import.meta.env.VITE_API_BASE_URL}/vehicle-records/report/daily?days=${this.selectedDays}`,
          { headers: { Authorization: `Bearer ${token}` } }
        );
        this.reportData = response.data.report;
      } catch (error) {
        console.error("Error fetching report:", error);
      } finally {
        this.loading = false;
      }
    },
    openControllerData(date) {
      this.selectedDate = date; // Set selected date\
      console.log(this.selectedDate);
      this.showControllerDataDialog = true; // Open dialog
    },
    closeControllerDataDialog() {
      this.showControllerDataDialog = false; // Close dialog
    },
  },
  computed: {
    columns() {
      return [
        { name: "date", label: "Date", align: "left", field: "date" },
        { name: "inCount", label: "IN", align: "center", field: "inCount" },
        { name: "outCount", label: "OUT", align: "center", field: "outCount" },
        { name: "visitor", label: "Visitor", align: "center", field: "visitor" },
        { name: "park", label: "Park", align: "center", field: "park" },
        { name: "unknown", label: "Unknown", align: "center", field: "unknown" },
        { name: "actions", label: "Actions", align: "center" },
      ];
    },
  },
};
</script>
