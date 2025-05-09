@extends('Layouts.app')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<div class="flex h-screen bg-white">
    <!-- Left Sidebar -->
    <div class="w-80 border-r">
        <div class="h-16 border-b flex items-center px-4">
            <h2 class="text-xl font-semibold text-gray-800">Messages</h2>
        </div>
        <div class="overflow-y-auto h-[calc(100vh-4rem)]" id="users-list">
            @foreach($users as $user)
                <div 
                    onclick="loadChat({{ $user->id }}, '{{ $user->name }}', this)"
                    class="flex items-center px-4 py-3 hover:bg-gray-50 cursor-pointer border-b user-item"
                    data-user-id="{{ $user->id }}"
                >
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold text-lg uppercase">
                        {{ substr($user->name, 0, 2) }}
                    </div>
                    <div class="ml-3 flex-1">
                        <div class="flex items-center justify-between">
                            <p class="font-medium text-gray-900">{{ $user->name }}</p>
                            @if($user->received_messages_count > 0)
                                <span class="bg-indigo-600 text-white text-xs px-2 py-0.5 rounded-full unread-count">
                                    {{ $user->received_messages_count }}
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Chat Area -->
    <div class="flex-1 flex flex-col">
        <!-- Chat Header -->
        <div class="h-16 border-b flex items-center px-6">
            <h3 class="text-xl font-semibold text-gray-800" id="chat-header">Select a user to start chatting</h3>
        </div>

        <!-- Messages -->
        <div class="flex-1 overflow-y-auto px-6 py-4" id="messages-container">
            <div class="flex items-center justify-center h-full text-gray-500">
                Select a conversation to start messaging
            </div>
        </div>

        <!-- Message Input -->
        <div class="border-t p-4">
            <form id="message-form" class="hidden">
                @csrf
                <div class="flex items-center space-x-4">
                    <input 
                        type="text" 
                        id="message-input"
                        class="flex-1 border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:border-indigo-500"
                        placeholder="Type your message..."
                        required
                    >
                    <button 
                        type="submit"
                        class="bg-indigo-600 text-white rounded-full p-2 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentReceiverId = null;
    let isLoadingMessages = false;
    
    // Initialize Pusher
    const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
        encrypted: true
    });

    const channel = pusher.subscribe('chat');
    
    channel.bind('message.sent', function(data) {
        if (data.message.sender_id == currentReceiverId || 
            data.message.receiver_id == currentReceiverId) {
            appendMessage(data.message);
            
            if (data.message.receiver_id == {{ auth()->id() }}) {
                markMessageAsRead(data.message.id);
            }
        }
        updateUnreadCount(data.message.sender_id);
    });

    function loadChat(userId, userName, element) {
        if (isLoadingMessages || userId === currentReceiverId) return;
        isLoadingMessages = true;
        
        // Update active state
        document.querySelectorAll('.user-item').forEach(item => {
            item.classList.remove('bg-gray-50');
        });
        element.classList.add('bg-gray-50');
        
        // Show message form and loading state
        document.getElementById('message-form').classList.remove('hidden');
        document.getElementById('messages-container').innerHTML = `
            <div class="flex items-center justify-center h-full">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            </div>
        `;
        
        // Update header
        document.getElementById('chat-header').textContent = `Chat with ${userName}`;
        
        // Set current receiver
        currentReceiverId = userId;
        
        // Load messages
        $.get(`{{ route('messages.get') }}?user_id=${userId}`, function(response) {
            if (response.messages.length === 0) {
                $('#messages-container').html(`
                    <div class="flex items-center justify-center h-full text-gray-500">
                        No messages yet. Start a conversation!
                    </div>
                `);
            } else {
                $('#messages-container').html('');
                response.messages.forEach(message => {
                    appendMessage(message);
                });
                scrollToBottom();
            }
            markAllAsRead(userId);
            isLoadingMessages = false;
        }).fail(function() {
            $('#messages-container').html(`
                <div class="flex items-center justify-center h-full text-red-500">
                    Failed to load messages. Please try again.
                </div>
            `);
            isLoadingMessages = false;
        });
    }

    function appendMessage(message) {
        const isReceived = message.sender_id != {{ auth()->id() }};
        const messageClass = isReceived ? 'received' : 'sent';
        const time = new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        
        // Clear "No messages" text if it exists
        const noMessages = document.querySelector('#messages-container .text-gray-500');
        if (noMessages) {
            noMessages.remove();
        }
        
        const messageHtml = `
            <div class="message ${messageClass}" data-message-id="${message.id}">
                <div class="message-content">
                    ${message.content}
                </div>
                <div class="message-time">
                    ${time}
                </div>
            </div>
        `;
        
        $('#messages-container').append(messageHtml);
        scrollToBottom();
    }

    function scrollToBottom() {
        const container = document.getElementById('messages-container');
        container.scrollTop = container.scrollHeight;
    }

    function markMessageAsRead(messageId) {
        $.post(`{{ url('/messages') }}/${messageId}/read`, {
            _token: $('meta[name="csrf-token"]').attr('content')
        });
    }

    function markAllAsRead(userId) {
        $.post(`{{ url('/messages/read-all') }}/${userId}`, {
            _token: $('meta[name="csrf-token"]').attr('content')
        }).done(() => {
            const userItem = document.querySelector(`.user-item[data-user-id="${userId}"]`);
            const unreadBadge = userItem.querySelector('.unread-count');
            if (unreadBadge) unreadBadge.remove();
        });
    }

    function updateUnreadCount(userId) {
        $.get(`{{ route('chat.users') }}`, function(response) {
            response.users.forEach(user => {
                const userItem = document.querySelector(`.user-item[data-user-id="${user.id}"]`);
                if (!userItem) return;
                
                let unreadBadge = userItem.querySelector('.unread-count');
                
                if (user.received_messages_count > 0) {
                    if (unreadBadge) {
                        unreadBadge.textContent = user.received_messages_count;
                    } else {
                        unreadBadge = document.createElement('span');
                        unreadBadge.className = 'bg-indigo-600 text-white text-xs px-2 py-0.5 rounded-full unread-count';
                        unreadBadge.textContent = user.received_messages_count;
                        userItem.querySelector('.flex.items-center.justify-between').appendChild(unreadBadge);
                    }
                } else if (unreadBadge) {
                    unreadBadge.remove();
                }
            });
        });
    }

    // Handle message form submission
    document.getElementById('message-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const input = document.getElementById('message-input');
        const content = input.value.trim();
        if (!content || !currentReceiverId) return;

        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;

        $.post('{{ route('messages.store') }}', {
            receiver_id: currentReceiverId,
            content: content,
            _token: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(response) {
            input.value = '';
            appendMessage(response.message);
        })
        .fail(function(error) {
            console.error('Error details:', error.responseJSON || error);
            if (error.responseJSON?.message?.id) {
                input.value = '';
                appendMessage(error.responseJSON.message);
                console.warn('Message saved but real-time update may have failed');
            } else {
                alert('Failed to send message: ' + (error.responseJSON?.message || error.statusText || 'Unknown error'));
            }
        })
        .always(function() {
            submitBtn.disabled = false;
            input.focus();
        });
    });

    // Auto-refresh users list
    setInterval(() => {
        if (currentReceiverId) updateUnreadCount();
    }, 30000);

    // Focus input when clicking messages container
    document.getElementById('messages-container').addEventListener('click', function() {
        if (currentReceiverId) {
            document.getElementById('message-input').focus();
        }
    });

    // Handle Enter key in message input
    document.getElementById('message-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            document.getElementById('message-form').dispatchEvent(new Event('submit'));
        }
    });
</script>
@endpush

@push('styles')
<style>
    .message {
        @apply mb-4 max-w-[70%];
    }
    .message.sent {
        @apply ml-auto;
    }
    .message.received {
        @apply mr-auto;
    }
    .message-content {
        @apply px-4 py-2 rounded-lg inline-block;
    }
    .sent .message-content {
        @apply bg-indigo-600 text-white;
    }
    .received .message-content {
        @apply bg-gray-100 text-gray-900;
    }
    .message-time {
        @apply text-xs text-gray-500 mt-1;
    }
    .sent .message-time {
        @apply text-right;
    }

    /* Custom Scrollbar */
    .overflow-y-auto::-webkit-scrollbar {
        @apply w-1.5;
    }
    .overflow-y-auto::-webkit-scrollbar-track {
        @apply bg-transparent;
    }
    .overflow-y-auto::-webkit-scrollbar-thumb {
        @apply bg-gray-200 rounded-full;
    }
    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        @apply bg-gray-300;
    }
</style>
@endpush
@endsection 