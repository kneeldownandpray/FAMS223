<template>
  <q-page padding>

    <q-card-section class="header-format-w">
      <div class="text-h6">
        <q-icon name="list" size="25px" class="q-mr-sm" /> Requirement Settings
      </div>
    </q-card-section>

    <!-- Add Button -->

    <!-- Add Requirement Type Dialog -->
    <q-dialog v-model="addDialogVisible">
      <q-card style="min-width: 400px">
        <q-card-section>
          <div class="text-h6">Add a New Requirement Type</div>
        </q-card-section>

        <q-card-section>
          <q-form @submit.prevent="addRequirementType">
            <q-input
              filled
              v-model="newRequirement.name"
              label="Requirement Type Name"
              :rules="[val => !!val || 'Name is required']"
              class="q-mb-md"
            />
            <q-input
              filled
              v-model="newRequirement.description"
              label="Description (Optional)"
              :rules="[val => val.length <= 1000 || 'Description should be under 1000 characters']"
              class="q-mb-md"
            />
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn label="Cancel" flat @click="addDialogVisible = false" />
          <q-btn label="Add" color="primary" @click="addRequirementType" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Display Requirement Types Table -->
    <q-card>
      <q-card-section class="flex" style="justify-content: space-between">
        <div class="text-h6">Existing Requirement Types</div> 
        <q-btn
        icon="add"
      label="Add Requirement" 
      color="primary" 
      class="" 
      @click="addDialogVisible = true" 
    />

      </q-card-section>

      <q-card-section>
        <q-input
          v-model="filter"
          debounce="300"
          placeholder="Search Requirement Types"
          filled
          class="q-mb-md"
        />
        <q-table
          :rows="filteredRequirementTypes"
          :columns="columns"
          row-key="id"
          :filter="filter"
          :rows-per-page-options="[5, 10, 20]"
        >
          <template v-slot:body-cell-actions="props">
            <q-td :props="props">
              <q-btn
                flat
                icon="edit"
                color="primary"
                @click="openEditDialog(props.row)"
                class="q-mr-sm"
              />
              <q-btn
                flat
                icon="delete"
                color="negative"
                @click="openDeleteDialog(props.row.id)"
              />
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- Error Dialog -->
    <q-dialog v-model="errorDialogVisible">
      <q-card>
        <q-card-section>
          <div class="text-h6">Error</div>
          <div v-if="errorDetails.length">
            <div v-for="(msg, index) in errorDetails" :key="index">{{ msg }}</div>
          </div>
        </q-card-section>

        <q-card-actions>
          <q-btn label="Close" color="negative" @click="errorDialogVisible = false" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Edit Requirement Type Dialog -->
    <q-dialog v-model="editDialogVisible">
      <q-card>
        <q-card-section>
          <div class="text-h6">Edit Requirement Type</div>
        </q-card-section>

        <q-card-section>
          <q-input
            filled
            v-model="editRequirement.name"
            label="Requirement Type Name"
            :rules="[val => !!val || 'Name is required']"
            class="q-mb-md"
          />
          <q-input
            filled
            v-model="editRequirement.description"
            label="Description (Optional)"
            :rules="[val => val.length <= 1000 || 'Description should be under 1000 characters']"
            class="q-mb-md"
          />
        </q-card-section>

        <q-card-actions>
          <q-btn label="Save Changes" color="primary" @click="saveChanges" />
          <q-btn label="Cancel" color="negative" @click="editDialogVisible = false" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Delete Confirmation Dialog -->
    <q-dialog v-model="deleteDialogVisible">
      <q-card>
        <q-card-section>
          <div class="text-h6">Are you sure you want to delete?</div>
        </q-card-section>

        <q-card-actions>
          <q-btn label="Yes" color="primary" @click="deleteRequirementType" />
          <q-btn label="No" color="negative" @click="deleteDialogVisible = false" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { ref, computed } from 'vue';
import axios from 'axios';

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

export default {
  name: 'RequirementTypesPage',

  data() {
    return {
      newRequirement: {
        name: '',
        description: '',
      },
      requirementTypes: [],
      filter: '',
      columns: [
        { name: 'name', label: 'Name', align: 'left', field: 'name' },
        { name: 'description', label: 'Description', align: 'left', field: 'description' },
        { name: 'actions', label: 'Actions', align: 'center' },
      ],
      errorDialogVisible: false,
      errorDetails: [],
      editDialogVisible: false,
      editRequirement: {
        id: null,
        name: '',
        description: '',
      },
      deleteDialogVisible: false,
      requirementToDelete: null,
      addDialogVisible: false,
    };
  },

  computed: {
    filteredRequirementTypes() {
      return this.requirementTypes.filter((type) => {
        return type.name.toLowerCase().includes(this.filter.toLowerCase());
      });
    },
  },

  created() {
    this.fetchRequirementTypes();
  },

  methods: {
    async fetchRequirementTypes() {
      try {
        const token = localStorage.getItem('access_token');
        const response = await axios.get(`${apiBaseUrl}/display/requirements`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        this.requirementTypes = response.data;
      } catch (error) {
        this.showErrorDialog('Failed to fetch requirement types', error);
      }
    },

    async addRequirementType() {
      if (!this.newRequirement.name) return;

      try {
        const token = localStorage.getItem('access_token');
        await axios.post(`${apiBaseUrl}/add-requirement-type`, this.newRequirement, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });

        this.newRequirement.name = '';
        this.newRequirement.description = '';
        this.addDialogVisible = false;
        this.fetchRequirementTypes();
      } catch (error) {
        this.showErrorDialog('Error adding requirement type', error);
      }
    },

    openEditDialog(requirement) {
      this.editRequirement.id = requirement.id;
      this.editRequirement.name = requirement.name;
      this.editRequirement.description = requirement.description;
      this.editDialogVisible = true;
    },

    async saveChanges() {
      try {
        const token = localStorage.getItem('access_token');
        await axios.put(`${apiBaseUrl}/edit-requirement-type/${this.editRequirement.id}`, this.editRequirement, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });

        this.editDialogVisible = false;
        this.fetchRequirementTypes();
      } catch (error) {
        this.showErrorDialog('Error updating requirement type', error);
      }
    },

    openDeleteDialog(id) {
      this.requirementToDelete = id;
      this.deleteDialogVisible = true;
    },

    async deleteRequirementType() {
      try {
        const token = localStorage.getItem('access_token');
        await axios.delete(`${apiBaseUrl}/delete-requirement-type/${this.requirementToDelete}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });

        this.deleteDialogVisible = false;
        this.fetchRequirementTypes();
      } catch (error) {
        this.showErrorDialog('Error deleting requirement type', error);
      }
    },

    showErrorDialog(message, error) {
      this.errorDetails = [];
      if (error.response && error.response.data.errors) {
        Object.keys(error.response.data.errors).forEach((field) => {
          this.errorDetails.push(...error.response.data.errors[field]);
        });
      } else {
        this.errorDetails.push(error.message || 'An unknown error occurred.');
      }
      this.errorDialogVisible = true;
    },
  },
};
</script>
