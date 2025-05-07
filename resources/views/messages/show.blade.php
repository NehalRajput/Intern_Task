@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Message Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Message</h1>
                    <p class="mt-1 text-lg text-gray-600">View and reply to messages</p>
                </div>
                <a href="{{ route('messages.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Back to Messages
                </a>
            </div>
        </div>

        <!-- Message Thread -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            <div class="p-6">
                <!-- Original Message -->
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="text-sm text-gray-500">
                                From: {{ $message->sender->name }}
                                <span class="mx-2">•</span>
                                To: {{ $message->receiver->name }}
                            </p>
                            <p class="text-sm text-gray-500">{{ $message->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        @if(!$message->is_read && $message->receiver_id === auth()->id())
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Unread
                            </span>
                        @endif
                    </div>
                    <div class="prose max-w-none">
                        {{ $message->content }}
                    </div>
                </div>

                <!-- Replies -->
                @foreach($message->replies as $reply)
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="text-sm text-gray-500">
                                    From: {{ $reply->sender->name }}
                                    <span class="mx-2">•</span>
                                    To: {{ $reply->receiver->name }}
                                </p>
                                <p class="text-sm text-gray-500">{{ $reply->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            @if(!$reply->is_read && $reply->receiver_id === auth()->id())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Unread
                                </span>
                            @endif
                        </div>
                        <div class="prose max-w-none">
                            {{ $reply->content }}
                        </div>
                    </div>
                @endforeach

                <!-- Reply Form -->
                <form action="{{ route('messages.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $message->id }}">
                    <input type="hidden" name="receiver_id" value="{{ $message->sender_id === auth()->id() ? $message->receiver_id : $message->sender_id }}">
                    
                    <div class="space-y-4">
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700">Reply</label>
                            <textarea name="content" id="content" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Write your reply here..."></textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Send Reply
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Mark message as read when viewing
    @if(!$message->is_read && $message->receiver_id === auth()->id())
        fetch('{{ route('messages.mark-as-read', $message) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
    @endif
</script>
@endpush
@endsection 