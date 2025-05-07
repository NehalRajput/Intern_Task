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
                @foreach($users as $user)
                    <a href="{{ route('messages.show', $user->id) }}" 
                       class="block p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-600 font-medium">
                                        {{ substr($user->name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <h3 class="font-medium">{{ $user->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $user->role }}</p>
                                </div>
                            </div>
                            @if(isset($messages[$user->id]) && $messages[$user->id]->where('is_read', false)->count() > 0)
                                <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                                    {{ $messages[$user->id]->where('is_read', false)->count() }}
                                </span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Welcome Message -->
        <div class="w-full md:w-2/3 bg-white rounded-lg shadow flex items-center justify-center">
            <div class="text-center p-8">
                <h2 class="text-2xl font-semibold mb-4">Welcome to Messages</h2>
                <p class="text-gray-600">Select a conversation from the list to start messaging</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Add real-time functionality here
    Echo.private('chat.{{ auth()->id() }}')
        .listen('NewMessage', (e) => {
            // Handle new message
            console.log(e);
        });
</script>
@endpush
@endsection 