<template>
  <q-page padding>
    <q-card class="no-shadow" bordered>
      <q-card-section>
        <div class="text-h6 text-grey-8">Lending Accounts</div>
      </q-card-section>
      <q-separator></q-separator>
      <q-card-section class="q-pa-none">
        <q-table
          square
          class="no-shadow"
          title="Users"
          :rows="filteredUsers"
          :columns="columns"
          row-key="id"
          :filter="filter"
          :pagination="pagination"
          hide-pagination
        >
          <template v-slot:top-right>
            <q-input
              v-if="show_filter"
              filled
              borderless
              dense
              debounce="300"
              v-model="filter"
              placeholder="Search"
            >
              <template v-slot:append>
                <q-icon name="search" />
              </template>
            </q-input>
            <q-btn
              class="q-ml-sm"
              icon="filter_list"
              @click="toggleFilter"
              flat
            />
          </template>
          <template v-slot:body-cell-action="props">
            <q-td :props="props">
              <q-btn
                flat
                icon="edit"
                @click="openEditModal(props.row)"
              />
              <q-btn
                flat
                icon="delete"
                @click="confirmDelete(props.row)"
                color="negative"
              />
            </q-td>
          </template>
          <template v-slot:body-cell-account_type="props">
            <q-td :props="props">
              {{ getAccountTypeName(props.row.account_type) }}
            </q-td>
          </template>
        </q-table>

        <!-- Custom Pagination Controls -->
        <div style="display: flex; justify-content: space-between; padding: 10px;">
          <q-pagination
            v-model="pagination.page"
            :max="totalPages"
            boundary-numbers
            class="q-mt-md"
            @update:model-value="fetchUsers"
          />
          <q-select
            filled
            v-model="pagination.rowsPerPage"
            :options="rowsPerPageOptions"
            label="Rows per page"
            class="q-ml-sm"
            dense
            style="width: 200px;"
            @update:model-value="fetchUsers"
          />
        </div>
      </q-card-section>
    </q-card>

    <!-- Edit Modal -->
    <q-dialog v-model="editDialog">
      <q-card    class="custom-dialog">
        <q-card-section class="header-edit">
          <div class="text-h6">Edit User</div>
        </q-card-section>
        <q-card-section>
          <q-form @submit.prevent="updateUser">
            <q-input
              v-model="editForm.first_name"
              label="First Name"
              required
            />
            <q-input
              v-model="editForm.last_name"
              label="Last Name"
              required
            />
            <q-input
              v-model="editForm.email"
              label="Email"
              type="email"
              required
            />
            <q-select
              v-model="editForm.account_type"
              :options="accountTypeOptions"
              label="Account Type"
              required
            />
            <q-btn
              type="submit"
              label="Save"
              color="primary"
              class="q-mt-md"
            />
            <q-btn
              label="Cancel"
              class="q-ml-sm q-mt-md"
              @click="closeEditModal"
            />
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Delete Confirmation Dialog -->
    <q-dialog v-model="deleteDialog">
      <q-card>
        <q-card-section>
          <div class="text-h6">Confirm Delete</div>
          <p>Are you sure you want to delete this user?</p>
        </q-card-section>
        <q-card-section>
          <q-btn
            label="Delete"
            color="negative"
            @click="deleteUser"
          />
          <q-btn
            label="Cancel"
            class="q-ml-sm"
            @click="closeDeleteDialog"
          />
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { defineComponent, ref, computed } from 'vue';
import axios from 'axios';

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL;

export default defineComponent({
  name: 'TableBasic',
  data() {
    return {
      filter: '',
      show_filter: false,
      users: [],
      loading: false,
      editDialog: false,
      deleteDialog: false,
      userToDelete: null,
      editForm: {
        id: null,
        first_name: '',
        last_name: '',
        email: '',
        account_type: 1
      },
      rowsPerPageOptions: [5, 10],
      pagination: {
        rowsPerPage: 10,
        page: 1,
      },
      totalPages: 0,
      columns: [
        { name: 'id', label: 'ID', align: 'left', field: 'id', sortable: true },
        { name: 'first_name', label: 'First Name', align: 'left', field: 'first_name', sortable: true },
        { name: 'last_name', label: 'Last Name', align: 'left', field: 'last_name', sortable: true },
        { name: 'email', label: 'Email', align: 'left', field: 'email', sortable: true },
        { name: 'account_type', label: 'Account Type', align: 'left', field: 'account_type', sortable: true },
        { name: 'action', label: 'Actions', align: 'center', field: 'action' }
      ],
      accountTypeOptions: [
        { label: 'Developer', value: 1 },
        { label: 'Admin', value: 2 },
        { label: 'Admin Workers', value: 3 },
        { label: 'Lending', value: 4 },
        { label: 'Employer', value: 5 },
        { label: 'Worker', value: 6 }
      ]
    };
  },
  computed: {
    filteredUsers() {
      if (this.filter) {
        return this.users.filter(user =>
          user.first_name.toLowerCase().includes(this.filter.toLowerCase()) ||
          user.last_name.toLowerCase().includes(this.filter.toLowerCase()) ||
          user.email.toLowerCase().includes(this.filter.toLowerCase())
        );
      }
      return this.users;
    }
  },
  methods: {
    getAccountTypeName(accountType) {
      const mappings = {
        1: 'Developer',
        2: 'Admin',
        3: 'Admin Workers',
        4: 'Lending',
        5: 'Employer',
        6: 'Worker'
      };
      return mappings[accountType] || 'Unknown';
    },
    async fetchUsers() {
      this.loading = true;
      try {
        const token = localStorage.getItem('access_token_employer');
        const response = await axios.get(`${apiBaseUrl}/users`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
          params: {
            search: this.filter,
            per_page: this.pagination.rowsPerPage,
            page: this.pagination.page,
            account_type: 4,
          }
        });
        this.users = response.data.data;
        this.totalPages = response.data.last_page; // Get total pages from API response
      } catch (error) {
        console.error('Failed to fetch users:', error);
        if (error.response && error.response.status === 401) {
          console.error('Authentication failed. Please log in again.');
          localStorage.removeItem('access_token_employer');
          this.$router.push('/login');
        }
      } finally {
        this.loading = false;
      }
      setTimeout(() => {
       this.fetchUsers();
      }, 10000); 
    },
    openEditModal(user) {
      this.editForm = { ...user }; // Populate the edit form with selected user data
      this.editDialog = true;
    },
    closeEditModal() {
      this.editDialog = false;
    },
    async updateUser() {
      try {
        const token = localStorage.getItem('access_token_employer');
        await axios.put(`${apiBaseUrl}/users/${this.editForm.id}`, this.editForm, {
          headers: {
            Authorization: `Bearer ${token}`,
          }
        });
        this.fetchUsers(); // Refresh the list after successful update
        this.closeEditModal();
      } catch (error) {
        console.error('Failed to update user:', error);
      }
    },
    confirmDelete(user) {
      this.userToDelete = user;
      this.deleteDialog = true;
    },
    closeDeleteDialog() {
      this.deleteDialog = false;
      this.userToDelete = null;
    },
    async deleteUser() {
      try {
        const token = localStorage.getItem('access_token_employer');
        await axios.delete(`${apiBaseUrl}/users/${this.userToDelete.id}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          }
        });
        this.fetchUsers(); // Refresh the list after successful deletion
        this.closeDeleteDialog();
      } catch (error) {
        console.error('Failed to delete user:', error);
      }
    },
    toggleFilter() {
      this.show_filter = !this.show_filter;
    }
  },
  mounted() {
    this.fetchUsers(); // Fetch users when the component is mounted
  }
});
</script>
