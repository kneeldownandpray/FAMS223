<template>
  <q-page class="q-pa-md bg-grey-2">

    <!-- Requirement Cards -->
    <div style="display: flex; flex-wrap: wrap; gap: 5px;">
      <q-card
        v-for="req in requirements"
        :key="req.id"
        class="q-mb-xs q-pa-xs requirement-card"
        flat
        bordered
      >
        <q-card-section>
          <div class="text-h6 q-mb-xs">
            {{ req.requirement_type?.name || 'N/A' }}
          </div>

          <div class="q-mb-sm">
            <q-option-group
              v-model="req.status"
              :options="statusOptions"
              type="radio"
              color="primary"
              inline
              dense
              option-value="value"
              option-label="label"
            />
          </div>

          <!-- Send Notes button -->
       

          <!-- Conditionally show Note input -->
          <q-input
            v-if="req.showNoteInput || req.note"
            v-model="req.note"
            label="Note"
            outlined
            dense
            class="q-mb-sm"
          />

          <div class="row justify-between items-center q-mt-sm">
            <div>
            <q-btn
              icon="save"
              label="Save"
              color="green"
              dense
              @click="saveRequirement(req)"
            />
            <q-btn
            icon="send"
            v-if="!req.note"
            dense
            flat
            color="warning"
            label="Notes" class="q-ml-xs"
            @click="req.showNoteInput = !req.showNoteInput"
        
          />
        </div>
            <q-btn
              icon="download"
              label="Download"
              color="primary"
              flat
              dense
              @click="downloadFile(req.id)"
            />
          </div>
        </q-card-section>
      </q-card>
    </div>

    <!-- Dialog for messages -->
    <q-dialog v-model="dialog.visible">
      <q-card>
        <q-card-section class="text-h6">{{ dialog.title }}</q-card-section>
        <q-card-section v-html="dialog.message" />
        <q-card-actions align="right">
          <q-btn label="OK" color="red" v-close-popup @click="checkifemmit()" />
        </q-card-actions>
      </q-card>
    </q-dialog>

  </q-page>
</template>

<script>
import axios from 'axios'

export default {
  name: 'AdminRequirements',
  data() {
    return {
      requirements: [],
      UserIdOfWorker: null,
      token: localStorage.getItem('access_token'),
      apiBaseUrl: import.meta.env.VITE_API_BASE_URL,
      statusOptions: [
        { label: 'Processing', value: 'processing' },
        { label: 'Accepted', value: 'accepted' },
        { label: 'Rejected', value: 'rejected' }
      ],
      dialog: {
        visible: false,
        title: '',
        message: ''
      }
    }
  },
  props: {
    idOfRequirement: {
      type: null, // or the appropriate type for your data
      required: true
    }
  },
  
  methods: {
    async checkifemmit(){
    //   if(!this.requirements){
    //   this.$emit('emittest', true);
    // }
    if (Array.isArray(this.requirements) && !this.requirements.length > 0) {
    this.$emit('emittest', true);
} 
    // console.log(this.requirements);
    },
    async fetchRequirements() {
      try {
        const url = this.idOfRequirement
          ? `${this.apiBaseUrl}/admin/user-requirements?user_id=${this.idOfRequirement}`
          : `${this.apiBaseUrl}/admin/user-requirements`

        const response = await axios.get(url, {
          headers: { Authorization: `Bearer ${this.token}` }
        })

        // Add showNoteInput field per requirement
        this.requirements = response.data.map(req => ({
          ...req,
          showNoteInput: !!req.note
        }))

        this.checkMissingRequirements()
      } catch (error) {
        this.showDialog('Error', 'Failed to fetch requirements.')
      }
    },

    async saveRequirement(req) {
      try {
        await axios.put(`${this.apiBaseUrl}/admin/user-requirements/${req.id}`, {
          status: req.status,
          note: req.note
        }, {
          headers: { Authorization: `Bearer ${this.token}` }
        })
        this.showDialog('Success', 'Requirement updated.')
        
      } catch (error) {
        this.showDialog('Error', 'Failed to update requirement.')
      }
    },

    async downloadFile(id) {
      try {
        const response = await axios.get(`${this.apiBaseUrl}/user-requirements/${id}/download`, {
          headers: { Authorization: `Bearer ${this.token}` },
          responseType: 'blob'
        })
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', 'requirement_file')
        document.body.appendChild(link)
        link.click()
        link.remove()
      } catch (error) {
        this.showDialog('Error', 'Failed to download file.')
      }
    },

    async checkMissingRequirements() {
      try {
        const response = await axios.get(`${this.apiBaseUrl}/admin/requirement-types`, {
          headers: { Authorization: `Bearer ${this.token}` }
        })

        const allRequirements = response.data
        const missingRequirements = allRequirements.filter(req =>
          !this.requirements.some(userReq => userReq.requirement_type_id === req.id)
        )

        if (missingRequirements.length > 0) {
          const listHtml = `<ul>${missingRequirements.map(r => `<li>${r.name}</li>`).join('')}</ul>`
          this.showDialog('Missing Requirements', listHtml)
        }
      } catch (error) {
        this.showDialog('Error', 'Failed to check for missing requirements.')
      }
    },

    showDialog(title, message) {
      this.dialog.title = title
      this.dialog.message = message
      this.dialog.visible = true
    }
  },

  mounted() {
    this.fetchRequirements()
  }
}
</script>

<style>
.requirement-card {
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
  width: 48%;
}

@media (max-width: 650px) {
  .requirement-card {
    width: 100% !important;
  }
}
</style>
