<template>
  <q-page class="q-pa-md">
    <h2 class="q-mb-md">Saved Data</h2>

    <q-card v-if="savedData.length" class="q-mb-md">
      <q-card-section>
        <!-- Table for displaying data -->
        <q-table
          :rows="savedData"
          :columns="columns"
          row-key="id"
          :pagination="pagination"
          flat
        >
          <template v-slot:body="props">
            <q-tr :props="props">
              <q-td :props="props" key="pattern">
                <q-input
                  v-model="props.row.pattern"
                  label="Plate Pattern"
                  @blur="updateData(props.row)"
                  dense
                  filled
                  class="q-mb-none"
                />
              </q-td>
              <q-td :props="props" key="color">
                <div class="q-mb-xs">
                  <span :style="{ color: props.row.color }">{{ props.row.color }}</span>
                </div>
                <div :style="{ backgroundColor: props.row.color }" style="height: 20px; width: 20px; border-radius: 50%;"></div>
              </q-td>

              <q-td :props="props" key="vehicleType">
                {{ props.row.vehicle_type }}
              </q-td>
              <q-td :props="props" key="timestamp">
                {{ formatDate(props.row.created_at) }}
              </q-td>
              <q-td :props="props" key="image">
                <q-img
                  :src="props.row.image"
                  :alt="`Plate Image: ${props.row.pattern}`"
                  width="50px"
                  @click="showImage(props.row.image)"
                  class="thumbnail-image"
                />
              </q-td>
              <q-td :props="props" key="actions">
                <q-btn @click="alertMaintenance()" icon="person" color="black" flat size="sm" />
                <q-btn @click="editData(props.row)" icon="edit" color="primary" flat size="sm" />
                <q-btn @click="deleteData(props.row)" icon="delete" color="negative" flat size="sm" />
              </q-td>
            </q-tr>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <q-card v-else>
      <q-card-section>No data saved yet.</q-card-section>
    </q-card>

    <!-- Image Viewer Modal -->
    <q-dialog v-model="imageDialogVisible" style="z-index: 1111111;">
      <q-card style="min-width: 350px;">
        <q-card-section>
          <q-img :src="selectedImage" alt="Full-size Plate Image" />
        </q-card-section>
        <q-card-actions>
          <q-btn flat label="Close" color="primary" @click="imageDialogVisible = false" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { date } from 'quasar';
import axios from 'axios';

export default {
  data() {
    return {
      savedData: [],
      pagination: {
        rowsPerPage: 5, // Limit rows per page
      },
      columns: [
        { name: 'pattern', label: 'Plate Pattern', align: 'left', field: 'pattern' },
        { name: 'color', label: 'Color', align: 'left', field: 'color' },
        { name: 'vehicleType', label: 'Vehicle Type', align: 'left', field: 'vehicle_type' },
        { name: 'timestamp', label: 'Timestamp', align: 'left', field: 'created_at' },
        { name: 'image', label: 'Image', align: 'left', field: 'image' },
        { name: 'actions', label: 'Actions', align: 'center', field: 'actions' },
      ],
      imageDialogVisible: false, // To control the image modal visibility
      selectedImage: '', // To store the selected image URL
      userId: '', // User ID from localStorage
    };
  },
  mounted() {
    this.loadData();
  },
  methods: {
    // Load data from API
    alertMaintenance() {
      alert("This function is under maintenance (purpose of this is to show profile of registered user)");
    },

    // Fetch data from the API
    async loadData() {
      this.userId = JSON.parse(localStorage.getItem('user')).id; // Get user ID from localStorage

      if (this.userId) {
        try {
          const response = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/vehicle-records/${this.userId}`, {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('access_token_employer')}`,
              'Content-Type': 'multipart/form-data',  // This is required for file uploads
            },
          });
          this.savedData = response.data.data; // Assuming the API returns a paginated response with the vehicle records
        } catch (error) {
          console.error("Error fetching data:", error);
        }
      } else {
        console.log('User ID not found in localStorage');
      }
    },

    // Format timestamp into a more readable format
    formatDate(timestamp) {
      return date.formatDate(timestamp, 'MMMM D, YYYY (h:mm A)');
    },

    // Edit functionality
    editData(row) {
      // Here you can navigate to another page or open a modal for editing
      console.log("Edit item:", row);
      // You could show a modal or navigate to an edit page, for example:
      // this.$router.push({ name: 'edit-vehicle', params: { id: row.id } });
    },

    // Update data in localStorage
    updateData(row) {
  console.log('Updating row:', row);

  axios.put(`${import.meta.env.VITE_API_BASE_URL}/vehicle-records/${row.id}`, {
    pattern: row.pattern,
  }, {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('access_token_employer')}`,
      'Content-Type': 'application/json',
    },
  })
  .then(response => {
    console.log("Update successful:", response.data);

  })
  .catch(error => {
    console.error("Error updating record:", error);

  });
},


    // Delete functionality
    deleteData(row) {
      if (confirm("Are you sure you want to delete this item?")) {
        axios.delete(`${import.meta.env.VITE_API_BASE_URL}/vehicle-records/${row.id}`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('access_token_employer')}`,
            'Content-Type': 'multipart/form-data',
          },
        })
        .then(() => {
          // Remove deleted record from the list
          this.savedData = this.savedData.filter(item => item.id !== row.id);
        })
        .catch(error => {
          console.error("Error deleting record:", error);
        });
      }
    },

    // Function to show the larger image in a modal
    showImage(imageSrc) {
      this.selectedImage = imageSrc;
      this.imageDialogVisible = true;
    },
  },
};
</script>

<style scoped>
.thumbnail-image {
  cursor: pointer;
  width: 50px;
  height: 50px;
  object-fit: cover;
  margin-top: 5px;
  border-radius: 4px;
}
</style>
