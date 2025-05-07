@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

@php
    $isAdmin = auth()->user()->role === 'admin';
    if (!isset($interns) && $isAdmin) {
        $interns = \App\Models\User::where('role', 'intern')->get();
    }
    // For intern, get the admin user
    $adminUser = !$isAdmin ? \App\Models\User::where('role', 'admin')->first() : null;
@endphp

<div class="min-h-screen bg-gray-100 py-6 flex flex-col lg:flex-row gap-6">
  <div class="flex-1">
    @if(session('success'))
      <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
      </div>
    @endif

    @if(session('error'))
      <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
      </div>
    @endif

    <!-- Dashboard Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">
            Welcome Back, {{ auth()->user()->name }}
          </h1>
          <p class="mt-1 text-lg text-gray-600">
            Manage your tasks efficiently
          </p>
        </div>
      </div>
    </div>

    <!-- Task Management Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
      <!-- View Tasks -->
      <a href="{{ route('tasks.index') }}"
         class="block bg-white rounded-lg border hover:shadow-md transition p-6">
        <div class="flex items-center">
          <div class="bg-blue-500 p-3 rounded shadow-inner">
            <!-- icon -->
          </div>
          <div class="ml-4">
            <h3 class="text-lg font-medium text-gray-900">View All Tasks</h3>
            <p class="mt-1 text-sm text-gray-600">Manage and monitor all tasks</p>
          </div>
        </div>
      </a>

      <!-- Create Task -->
      <a href="{{ route('tasks.create') }}"
         class="block bg-white rounded-lg border hover:shadow-md transition p-6">
        <div class="flex items-center">
          <div class="bg-green-500 p-3 rounded shadow-inner">
            <!-- icon -->
          </div>
          <div class="ml-4">
            <h3 class="text-lg font-medium text-gray-900">Create New Task</h3>
            <p class="mt-1 text-sm text-gray-600">Add a new task to the system</p>
          </div>
        </div>
      </a>
    </div>

    <!-- Interns List -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-900">Interns</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse($interns as $intern)
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ $intern->name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-500">{{ $intern->email }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <form action="{{ route('admin.delete-user', $intern->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this intern?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="px-6 py-4 text-center text-gray-500">No interns found</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Task List Section -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-900">Tasks</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Interns</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse($tasks as $task)
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-500">{{ Str::limit($task->description, 50) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-500">{{ $task->due_date ? date('M d, Y', strtotime($task->due_date)) : 'No due date' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                    @if($task->status === 'completed') bg-green-100 text-green-800
                    @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800
                    @elseif($task->status === 'todo') bg-yellow-100 text-yellow-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($task->status) }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-500">
                    @forelse($task->interns as $intern)
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mr-1">
                        {{ $intern->name }}
                      </span>
                      <button type="button" onclick="openChatModalWithIntern({{ $intern->id }})" class="ml-2 text-xs text-blue-600 hover:underline">Message</button>
                    @empty
                      No interns assigned
                    @endforelse
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <a href="{{ route('tasks.edit', $task->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                  <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No tasks found</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Message Modal -->
<div id="messageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4" id="modalTitle">Message <span id="internName"></span></h3>
            <div id="messageContainer" class="h-64 overflow-y-auto mb-4 border rounded p-2">
                <!-- Messages will be loaded here -->
            </div>
            <form id="messageForm" class="mt-4">
                <input type="hidden" id="receiverId" name="receiver_id">
                <div class="flex space-x-2">
                    <input type="text" id="messageInput" name="message" 
                           class="flex-1 rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                           placeholder="Type your message...">
                    <button type="submit" 
                            class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        Send
                    </button>
                </div>
            </form>
            <div class="mt-4">
                <button onclick="closeMessageModal()" 
                        class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<div id="chatModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-lg flex flex-col h-[80vh] border border-gray-200 relative">
        <div class="flex items-center justify-between p-4 border-b bg-gradient-to-r from-blue-100 to-blue-200 rounded-t-xl sticky top-0 z-10">
            <h2 class="text-lg font-bold text-blue-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2h5m6-4v-4m0 0V4m0 4l-2-2m2 2l2-2"/></svg>
                Chat
            </h2>
            @if($isAdmin)
                <select id="chat-intern-select" class="rounded border-gray-300 focus:ring-blue-400 focus:border-blue-400 text-sm">
                    @foreach($interns as $intern)
                        <option value="{{ $intern->id }}">{{ $intern->name }}</option>
                    @endforeach
                </select>
            @else
                <span class="font-semibold text-blue-700">{{ $adminUser ? $adminUser->name : 'Admin' }}</span>
            @endif
            <button onclick="closeChatModal()" class="ml-2 text-gray-500 hover:text-red-500 text-xl font-bold">&times;</button>
        </div>
        <div id="chat-panel-messages" class="flex-1 p-4 overflow-y-auto bg-gray-50 space-y-4"></div>
        <form id="chat-panel-form" class="p-4 border-t flex gap-2 bg-white sticky bottom-0 z-10">
            <input type="hidden" id="chat-panel-receiver-id" name="receiver_id" value="{{ $isAdmin ? '' : ($adminUser ? $adminUser->id : '') }}">
            <input type="text" id="chat-panel-message-input" name="message" class="flex-1 rounded-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 px-4 py-2" placeholder="Type your message...">
            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-full hover:bg-blue-600 transition-colors">Send</button>
        </form>
    </div>
</div>

<script>
const internSelect = document.getElementById('chat-intern-select');
const chatPanelReceiverId = document.getElementById('chat-panel-receiver-id');
const chatPanelMessages = document.getElementById('chat-panel-messages');
const chatPanelForm = document.getElementById('chat-panel-form');
const chatPanelMessageInput = document.getElementById('chat-panel-message-input');

function renderMessage(message, isMine, senderName, createdAt) {
  return `
    <div class="flex ${isMine ? 'justify-end' : 'justify-start'}">
      <div class="flex items-end gap-2 ${isMine ? 'flex-row-reverse' : ''}">
        <div class="w-8 h-8 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 font-bold text-sm">
          ${senderName.charAt(0).toUpperCase()}
        </div>
        <div class="max-w-xs px-4 py-2 rounded-2xl shadow ${isMine ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800'}">
          <div>${message}</div>
          <div class="text-xs mt-1 ${isMine ? 'text-blue-100' : 'text-gray-500'}">${createdAt}</div>
        </div>
      </div>
    </div>
  `;
}

function loadChatPanelMessages(internId) {
  fetch(`/messages/${internId}`)
    .then(response => response.text())
    .then(html => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const messages = doc.querySelectorAll('#messages > div');
      chatPanelMessages.innerHTML = '';
      messages.forEach(messageDiv => {
        // Extract info for bubble rendering
        const isMine = messageDiv.classList.contains('text-right');
        const msgText = messageDiv.querySelector('p')?.textContent || '';
        const timeText = messageDiv.querySelector('p.text-xs')?.textContent || '';
        const senderName = isMine ? '{{ auth()->user()->name }}' : internSelect.options[internSelect.selectedIndex].text;
        chatPanelMessages.insertAdjacentHTML('beforeend', renderMessage(msgText, isMine, senderName, timeText));
      });
      chatPanelMessages.scrollTop = chatPanelMessages.scrollHeight;
    });
}

// Initial load
if (internSelect.value) {
  chatPanelReceiverId.value = internSelect.value;
  loadChatPanelMessages(internSelect.value);
}

internSelect.addEventListener('change', function() {
  chatPanelReceiverId.value = this.value;
  loadChatPanelMessages(this.value);
});

chatPanelForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  const receiverId = chatPanelReceiverId.value;
  const message = chatPanelMessageInput.value;
  if (!message.trim()) return;
  try {
    const response = await fetch('/messages', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ receiver_id: receiverId, message })
    });
    if (response.ok) {
      chatPanelMessageInput.value = '';
      loadChatPanelMessages(receiverId);
    }
  } catch (error) {
    console.error('Error sending message:', error);
  }
});

Echo.private('chat.{{ auth()->id() }}')
  .listen('NewMessage', (e) => {
    if (e.message.sender_id == chatPanelReceiverId.value || e.message.receiver_id == chatPanelReceiverId.value) {
      loadChatPanelMessages(chatPanelReceiverId.value);
    }
  });

function openMessageModal(internId, internName) {
    document.getElementById('messageModal').classList.remove('hidden');
    document.getElementById('internName').textContent = internName;
    document.getElementById('receiverId').value = internId;
    loadMessages(internId);
}

function closeMessageModal() {
    document.getElementById('messageModal').classList.add('hidden');
}

function loadMessages(internId) {
    fetch(`/messages/${internId}`)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const messages = doc.querySelectorAll('#messages > div');
            const container = document.getElementById('messageContainer');
            container.innerHTML = '';
            messages.forEach(message => {
                container.appendChild(message.cloneNode(true));
            });
            container.scrollTop = container.scrollHeight;
        });
}

document.getElementById('messageForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch('/messages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                receiver_id: formData.get('receiver_id'),
                message: formData.get('message')
            })
        });

        if (response.ok) {
            document.getElementById('messageInput').value = '';
            loadMessages(formData.get('receiver_id'));
        }
    } catch (error) {
        console.error('Error sending message:', error);
    }
});

const isAdmin = @json(auth()->user()->role === 'admin');
const adminUserId = @json($adminUser ? $adminUser->id : null);

function openChatModal() {
    document.getElementById('chatModal').classList.remove('hidden');
    if (isAdmin) {
        const internSelect = document.getElementById('chat-intern-select');
        if (internSelect.value) {
            document.getElementById('chat-panel-receiver-id').value = internSelect.value;
            loadChatPanelMessages(internSelect.value);
        }
    } else {
        document.getElementById('chat-panel-receiver-id').value = adminUserId;
        loadChatPanelMessages(adminUserId);
    }
}

if (isAdmin) {
    internSelect.addEventListener('change', function() {
        chatPanelReceiverId.value = this.value;
        loadChatPanelMessages(this.value);
    });
}

function closeChatModal() {
    document.getElementById('chatModal').classList.add('hidden');
}
</script>
@endsection
