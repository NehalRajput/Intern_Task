@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Users List -->
        <div class="w-full md:w-1/3 bg-white rounded-lg shadow">
            <div class="p-4 border-b">
                <h2 class="text-xl font-semibold">Conversations</h2>
            </div>
            <div class="divide-y">
                @foreach($users as $otherUser)
                    <a href="{{ route('messages.show', $otherUser->id) }}" 
                       class="block p-4 hover:bg-gray-50 transition-colors {{ $otherUser->id == $user->id ? 'bg-gray-50' : '' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-600 font-medium">
                                        {{ substr($otherUser->name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <h3 class="font-medium">{{ $otherUser->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $otherUser->role }}</p>
                                </div>
                            </div>
                            @if(isset($messages[$otherUser->id]) && $messages[$otherUser->id]->where('is_read', false)->count() > 0)
                                <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                                    {{ $messages[$otherUser->id]->where('is_read', false)->count() }}
                                </span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Chat Area -->
        <div class="w-full md:w-2/3 bg-white rounded-lg shadow flex flex-col">
            <!-- Chat Header -->
            <div class="p-4 border-b">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-600 font-medium">
                            {{ substr($user->name, 0, 1) }}
                        </span>
                    </div>
                    <div>
                        <h2 class="font-semibold">{{ $user->name }}</h2>
                        <p class="text-sm text-gray-500">{{ $user->role }}</p>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div class="flex-1 p-4 overflow-y-auto" id="messages">
                @foreach($messages as $message)
                    <div class="mb-4 {{ $message->sender_id === auth()->id() ? 'text-right' : '' }}">
                        <div class="inline-block max-w-2xl {{ $message->sender_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-800' }} rounded-lg px-4 py-2">
                            <p>{{ $message->message }}</p>
                            <p class="text-xs mt-1 {{ $message->sender_id === auth()->id() ? 'text-blue-100' : 'text-gray-500' }}">
                                {{ $message->created_at->format('M d, h:i A') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Message Input -->
            <div class="p-4 border-t">
                <form id="message-form" class="flex space-x-4">
                    <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                    <input type="text" 
                           name="message" 
                           class="flex-1 rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                           placeholder="Type your message...">
                    <button type="submit" 
                            class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                        Send
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const messagesContainer = document.getElementById('messages');
    const messageForm = document.getElementById('message-form');

    // Scroll to bottom of messages
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    // Handle form submission
    messageForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(messageForm);
        
        try {
            const response = await fetch('{{ route("messages.store") }}', {
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
                messageForm.reset();
            }
        } catch (error) {
            console.error('Error sending message:', error);
        }
    });

    // Listen for new messages
    Echo.private('chat.{{ auth()->id() }}')
        .listen('NewMessage', (e) => {
            const message = e.message;
            const messageHtml = `
                <div class="mb-4">
                    <div class="inline-block max-w-2xl bg-gray-100 text-gray-800 rounded-lg px-4 py-2">
                        <p>${message.message}</p>
                        <p class="text-xs mt-1 text-gray-500">
                            ${new Date(message.created_at).toLocaleString()}
                        </p>
                    </div>
                </div>
            `;
            messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        });
</script>
@endpush
@endsection 