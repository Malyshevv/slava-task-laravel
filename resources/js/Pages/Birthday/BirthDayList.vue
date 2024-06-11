<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NavLink from '@/Components/NavLink.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Imported Birthdays
        <NavLink class="text-blue-500" :href="route('birthday.upload')" :active="route().current('birthday.upload')">
          Upload
        </NavLink>
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
          <p v-if="!result || result.length === 0" class="text-center p-5 text-gray-600">Not found records</p>
          <div v-for="(rows, date) in result" :key="date" class="mb-4">
            <h2 class="p-2 text-blue-500 bg-indigo-50 text-lg font-semibold">{{ date }}</h2>
            <table class="w-full text-sm text-left rtl:text-right text-gray-500">
              <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3">#ID</th>
                  <th scope="col" class="px-6 py-3">Name</th>
                  <th scope="col" class="px-6 py-3">Email</th>
                </tr>
              </thead>
              <tbody>
                <tr class="bg-white border-b" v-for="row in rows" :key="row.id">
                  <td class="px-6 py-4">{{ row.id }}</td>
                  <td class="px-6 py-4">{{ row.external_id }}</td>
                  <td class="px-6 py-4">{{ row.name }}</td>
                  <td class="px-6 py-4">{{ row.date }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <Pagination v-if="result && result.length" class="mt-6" :links="links" />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
<script>

import Pagination from '@/Components/Pagination.vue'

export default {
  components: {
    Pagination
  },
  props: {
    result: Object,
    links: Object
  },
}
</script>