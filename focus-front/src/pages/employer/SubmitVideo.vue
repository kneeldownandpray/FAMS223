<template>
  <q-page padding>
    <h3 style="font-weight: 600;">Video Management</h3>

    <!-- Button to Open the Form Dialog -->
    <q-btn color="primary" @click="openDialog" label="Add Video" />

    <!-- Video Table -->
    <q-table
      :rows="videos"
      :columns="columns"
      row-key="id"
      :pagination.sync="pagination"
      class="q-mt-lg"
    >
      <template v-slot:body="props">
        <q-tr :props="props">
          <q-td auto-width>
            <q-btn flat color="primary" @click="openEditDialog(props.row)">Edit</q-btn>
            <q-btn flat color="negative" @click="confirmDeleteVideo(props.row.id)">Delete</q-btn>
          </q-td>
          <q-td>{{ props.row.title }}</q-td>
          <q-td>{{ props.row.description }}</q-td>
          <q-td>
            <q-btn color="red" :href="props.row.youtube_link" target="_blank" icon="play_circle" text-color="white">Watch</q-btn>
          </q-td>
        </q-tr>
      </template>
    </q-table>

    <!-- Video Form Dialog -->
    <q-dialog v-model="dialogOpen">
      <q-card class="custom-dialog">
        <q-card-section>
          <q-form @submit.prevent="isEditing ? updateVideo : submitVideo">
            <q-input
              v-model="youtubeLink"
              label="YouTube Video Link"
              :rules="[val => val && val.length > 0 || 'YouTube link is required']"
            />
            <q-input
              v-model="title"
              label="Video Title"
              maxlength="25"
              counter
              :rules="[val => val && val.length <= 25 || 'Title must be under 25 characters']"
            />
            <q-input
              v-model="description"
              label="Video Description"
              type="textarea"
              maxlength="40"
              counter
              :rules="[val => val.length <= 40 || 'Description must be under 40 characters']"
            />
            <q-btn color="primary" v-if="!isEditing" @click="submitVideo" class="q-mr-sm" label="Submit" type="submit" />
            <q-btn color="secondary" v-if="isEditing" @click="updateVideo" label="Update" class="q-mr-sm" type="submit" />
            <q-btn  color="red" @click="closeDialog" label="Cancel" />
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Delete Confirmation Dialog -->
    <q-dialog v-model="deleteDialogOpen">
      <q-card class="custom-dialog">
        <q-card-section>
          <p>Are you sure you want to delete this video?</p>
        </q-card-section>
        <q-card-actions>
          <q-btn color="primary"  class="q-mr-sm" @click="deleteVideoConfirmed">Yes, delete</q-btn>
          <q-btn  color="red" @click="closeDeleteDialog">Cancel</q-btn>
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Edit Confirmation Dialog -->
    <q-dialog v-model="editDialogOpen">
      <q-card class="custom-dialog">
        <q-card-section>
          <p>Are you sure you want to edit this video?</p>
        </q-card-section>
        <q-card-actions>
          <q-btn color="primary" class="q-mr-sm" @click="confirmUpdateVideo">Yes, edit</q-btn>
          <q-btn  color="red" @click="closeEditDialog">Cancel</q-btn>
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
export default {
  data() {
    return {
      youtubeLink: '',
      title: '',
      description: '',
      isEditing: false,
      editId: null,
      videos: [],
      dialogOpen: false,
      deleteDialogOpen: false,
      editDialogOpen: false,
      columns: [
        { name: 'actions', label: 'Actions', align: 'left', field: row => row, sortable: false },
        { name: 'title', label: 'Title', align: 'left', field: 'title', sortable: true },
        { name: 'description', label: 'Description', align: 'left', field: 'description', sortable: true },
        { name: 'link', label: 'Link', align: 'left', field: 'youtube_link', sortable: false }
      ],
      pagination: {
        rowsPerPage: 10
      }
    };
  },
  methods: {
    openDialog() {
      this.dialogOpen = true;
    },
    closeDialog() {
      this.dialogOpen = false;
      this.resetForm();
    },
    openEditDialog(video) {
      this.youtubeLink = video.youtube_link;
      this.title = video.title;
      this.description = video.description;
      this.isEditing = true;
      this.editId = video.id;
      this.dialogOpen = true; // Open the dialog for editing
    },
    confirmDeleteVideo(id) {
      this.editId = id;
      this.deleteDialogOpen = true;
    },
    closeDeleteDialog() {
      this.deleteDialogOpen = false;
    },
    deleteVideoConfirmed() {
      const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;
      this.$axios.delete(`${apiBaseUrl}/user-videos/${this.editId}`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('access_token_employer')}`
        }
      }).then(() => {
        this.fetchVideos();
        this.closeDeleteDialog();
        // this.$q.notify({
        //   type: 'positive',
        //   message: 'Video deleted successfully!'
        // });
      }).catch(err => {
        console.error(err);
      });
    },
    confirmUpdateVideo() {
      this.updateVideo();
      this.closeDialog();
    },
    fetchVideos() {
      const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;
      this.$axios.get(`${apiBaseUrl}/user-videos`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('access_token_employer')}`
        }
      }).then(response => {
        this.videos = response.data;
      }).catch(err => {
        console.error(err);
      });
    },
    submitVideo() {
      const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;
      this.$axios.post(`${apiBaseUrl}/user-videos`, {
        youtube_link: this.youtubeLink,
        title: this.title,
        description: this.description,
      }, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('access_token_employer')}`
        }
      }).then(() => {
        this.fetchVideos();
        this.closeDialog();
        // this.$q.notify({
        //   type: 'positive',
        //   message: 'Video added successfully!'
        // });
      }).catch(err => {
        console.error(err);
      });
    },
    updateVideo() {
      const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;
      this.$axios.put(`${apiBaseUrl}/user-videos/${this.editId}`, {
        youtube_link: this.youtubeLink,
        title: this.title,
        description: this.description,
      }, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('access_token_employer')}`
        }
      }).then(() => {
        this.fetchVideos();
        this.closeDialog();
        // this.$q.notify({
        //   type: 'positive',
        //   message: 'Video updated successfully!'
        // });
      }).catch(err => {
        console.error(err);
      });
    },
    resetForm() {
      this.youtubeLink = '';
      this.title = '';
      this.description = '';
      this.isEditing = false;
      this.editId = null;
    }
  },
  mounted() {
    this.fetchVideos();
  }
};
</script>

<style scoped>
/* Add any specific styles if needed */
</style>
