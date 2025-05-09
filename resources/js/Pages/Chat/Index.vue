<template>
  <AppLayout title="Chat">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="flex h-[600px]">
            <!-- Users List -->
            <div class="w-1/4 border-r">
              <div class="p-4 border-b">
                <h2 class="text-lg font-semibold">{{ isAdmin ? 'Interns' : 'Messages' }}</h2>
              </div>
              <div class="overflow-y-auto h-[calc(100%-65px)]">
                <div
                  v-for="user in users"
                  :key="user.id"
                  @click="selectUser(user)"
                  :class="[
                    'p-4 cursor-pointer hover:bg-gray-50 transition',
                    selectedUser?.id === user.id ? 'bg-gray-100' : ''
                  ]"
                >
                  <div class="font-medium">{{ user.name }}</div>
                  <div class="text-sm text-gray-500">{{ user.email }}</div>
                </div>
              </div>
            </div>

            <!-- Chat Area -->
            <div class="flex-1">
              <template v-if="selectedUser">
                <div class="p-4 border-b">
                  <h3 class="font-medium">{{ selectedUser.name }}</h3>
                  <p class="text-sm text-gray-500">{{ selectedUser.email }}</p>
                </div>
                <Chat
                  :receiver-id="selectedUser.id"
                  :current-user="$page.props.auth.user"
                />
              </template>
              <div v-else class="h-full flex items-center justify-center text-gray-500">
                Select a user to start chatting
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Chat from '@/Components/Chat.vue'

const props = defineProps({
  users: {
    type: Array,
    required: true
  }
})

const selectedUser = ref(null)
const isAdmin = computed(() => $page.props.auth.user.role === 'admin')

const selectUser = (user) => {
  selectedUser.value = user
}
</script> 