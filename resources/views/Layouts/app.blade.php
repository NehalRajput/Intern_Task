<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <title>{{ config('app.name', 'Task Management System') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                     
                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('dashboard') }}" 
                               class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('dashboard') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                {{ __('Dashboard') }}
                            </a>

                            <!-- Messages Link -->
                            <button type="button" onclick="openChatModal()" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-blue-700 hover:border-blue-400 focus:outline-none focus:text-blue-700 focus:border-blue-400 transition">
                                <svg class="w-5 h-5 mr-1 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2h5m6-4v-4m0 0V4m0 4l-2-2m2 2l2-2"/></svg>
                                Messages
                            </button>

                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition">
                                    Tasks
                                </a>
                            @endif

                            @if(auth()->user()->role === 'intern')
                                <a href="{{ route('intern.tasks') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition">
                                    My Tasks
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        @auth
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                    Logout
                                </button>
                            </form>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>

    <!-- Chat Modal -->
    @php
        if (!isset($interns)) {
            $interns = \App\Models\User::where('role', 'intern')->get();
        }
    @endphp
    <div id="chatModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-lg flex flex-col h-[80vh] border border-gray-200 relative">
            <div class="flex items-center justify-between p-4 border-b bg-gradient-to-r from-blue-100 to-blue-200 rounded-t-xl sticky top-0 z-10">
                <h2 class="text-lg font-bold text-blue-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2h5m6-4v-4m0 0V4m0 4l-2-2m2 2l2-2"/></svg>
                    Chat
                </h2>
                <select id="chat-intern-select" class="rounded border-gray-300 focus:ring-blue-400 focus:border-blue-400 text-sm">
                    @foreach($interns as $intern)
                        <option value="{{ $intern->id }}">{{ $intern->name }}</option>
                    @endforeach
                </select>
                <button onclick="closeChatModal()" class="ml-2 text-gray-500 hover:text-red-500 text-xl font-bold">&times;</button>
            </div>
            <div id="chat-panel-messages" class="flex-1 p-4 overflow-y-auto bg-gray-50 space-y-4"></div>
            <form id="chat-panel-form" class="p-4 border-t flex gap-2 bg-white sticky bottom-0 z-10">
                <input type="hidden" id="chat-panel-receiver-id" name="receiver_id" value="">
                <input type="text" id="chat-panel-message-input" name="message" class="flex-1 rounded-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 px-4 py-2" placeholder="Type your message...">
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-full hover:bg-blue-600 transition-colors">Send</button>
            </form>
        </div>
    </div>
    <script>
    function openChatModal() {
        document.getElementById('chatModal').classList.remove('hidden');
        // Initial load
        const internSelect = document.getElementById('chat-intern-select');
        if (internSelect.value) {
            document.getElementById('chat-panel-receiver-id').value = internSelect.value;
            loadChatPanelMessages(internSelect.value);
        }
    }
    function closeChatModal() {
        document.getElementById('chatModal').classList.add('hidden');
    }
    function openChatModalWithIntern(internId) {
        openChatModal();
        document.getElementById('chat-intern-select').value = internId;
        document.getElementById('chat-panel-receiver-id').value = internId;
        loadChatPanelMessages(internId);
    }
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
                    const isMine = messageDiv.classList.contains('text-right');
                    const msgText = messageDiv.querySelector('p')?.textContent || '';
                    const timeText = messageDiv.querySelector('p.text-xs')?.textContent || '';
                    const senderName = isMine ? '{{ auth()->user()->name }}' : internSelect.options[internSelect.selectedIndex].text;
                    chatPanelMessages.insertAdjacentHTML('beforeend', renderMessage(msgText, isMine, senderName, timeText));
                });
                chatPanelMessages.scrollTop = chatPanelMessages.scrollHeight;
            });
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
    </script>
</body>
</html>
