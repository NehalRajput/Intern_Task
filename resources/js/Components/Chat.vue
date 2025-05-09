<template>
  <div class="flex flex-col h-full">
    <!-- Chat Messages -->
    <div class="flex-1 overflow-y-auto p-4" ref="messageContainer">
      <div v-for="message in messages" :key="message.id" class="mb-4">
        <div :class="[
          'max-w-[70%] rounded-lg p-3',
          message.sender_id === currentUser.id
            ? 'ml-auto bg-blue-500 text-white'
            : 'bg-gray-100'
        ]">
          <p class="text-sm">{{ message.content }}</p>
          <span class="text-xs mt-1 block" :class="[
            message.sender_id === currentUser.id ? 'text-blue-100' : 'text-gray-500'
          ]">
            {{ formatDate(message.created_at) }}
          </span>
        </div>
      </div>
    </div>

    <!-- Message Input -->
    <div class="border-t p-4">
      <form @submit.prevent="sendMessage" class="flex gap-2">
        <input
          v-model="newMessage"
          type="text"
          placeholder="Type your message..."
          class="flex-1 rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:border-blue-500"
        />
        <button
          type="submit"
          class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition"
          :disabled="!newMessage.trim()"
        >
          Send
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { useForm } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  receiverId: {
    type: Number,
    required: true
  },
  currentUser: {
    type: Object,
    required: true
  }
})

const messages = ref([])
const newMessage = ref('')
const messageContainer = ref(null)

const scrollToBottom = () => {
  nextTick(() => {
    if (messageContainer.value) {
      messageContainer.value.scrollTop = messageContainer.value.scrollHeight
    }
  })
}

const formatDate = (date) => {
  return new Date(date).toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const fetchMessages = async () => {
  try {
    const response = await axios.get(`/api/messages/${props.receiverId}`)
    messages.value = response.data.messages
    scrollToBottom()
  } catch (error) {
    console.error('Error fetching messages:', error)
  }
}

const sendMessage = async () => {
  if (!newMessage.value.trim()) return

  try {
    const response = await axios.post('/api/messages', {
      receiver_id: props.receiverId,
      content: newMessage.value
    })
    
    messages.value.push(response.data.message)
    newMessage.value = ''
    scrollToBottom()
  } catch (error) {
    console.error('Error sending message:', error)
  }
}

// Listen for new messages
window.Echo.private(`chat.User.${props.currentUser.id}`)
  .listen('MessageSent', (e) => {
    messages.value.push(e.message)
    scrollToBottom()
  })

onMounted(() => {
  fetchMessages()
})
</script> 