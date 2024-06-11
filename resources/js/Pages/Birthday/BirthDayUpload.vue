<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Upload Birthdays</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

          <form @submit.prevent="submitForm">
            <input type="file" ref="fileInput" class="cursor-pointer mb-4 text-sm" @change="handleFileChange">
            <button type="submit" :disabled="file === null"
                    class="cursor-pointer px-3 py-2 text-xs font-medium text-center inline-flex items-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
              Upload
            </button>
          </form>

          <div v-if="success" class="mt-4 p-4 bg-blue-100 text-green-800 rounded-lg">
            <p><b>{{ success.message }}</b></p>
            <p><b>Batch ID:</b> {{ success.batch_id }}</p>
            <p><b>Check Progress:</b> <a class="text-blue-500" :href="success.link_status" target="_blank">{{success.link_status}}</a></p>
            <p><b>Redis Key:</b> {{ success.redis_key }}</p>
          </div>
          <div v-if="error" class="mt-4 p-4 bg-red-100 text-red-800 rounded-lg">
            <p>{{ error }}</p>
          </div>

          <div class="scroll-container"  >
            <ul>
              <li v-for="record in records" :key="record.id">{{ record.id }} | {{ record.external_id }} | {{ record.name }} | {{ record.date }}</li>
            </ul>
          </div>

        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script>
export default {
  data() {
    return {
      records: [],
      file: null,
      success: null,
      error: null
    };
  },
  mounted () {
    window.Echo.channel('birthday')
        .listen('Birthday.BirthdayEvent', (e) => {
          this.records.push(e.record);
          this.scrollToBottom();
        })
        .listen('.failed', (e) => {
          console.error('Failed to connect to birthday channel:', e);
        });
  },
  methods: {
    scrollToBottom() {
      const container = document.querySelector('.scroll-container');
      container.scrollTop = container.scrollHeight;
    },
    handleFileChange(event) {
      this.file = event.target.files[0];
    },
    async submitForm() {
      const formData = new FormData();
      formData.append('file', this.file);

      try {
        const response = await axios.post('/birthday/upload', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        this.success = response.data;
        this.error = null;

        this.$refs.fileInput.value = '';
      } catch (error) {
        console.error(error.response.data);
        this.error = error.response.data.message;
        this.success = null;
      }
    }
  }
};
</script>
<style scoped>
.scroll-container {
  max-height: 300px; /* или любая другая фиксированная высота */
  overflow-y: auto;
  border: 1px solid #ccc;
  padding: 10px;
}
</style>