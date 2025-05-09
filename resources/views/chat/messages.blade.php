@if(isset($messages) && count($messages) > 0)
    @foreach($messages as $message)
        <div class="message {{ $message->sender_id == auth()->id() ? 'sent' : 'received' }}">
            <div class="message-content">
                {{ $message->content }}
            </div>
            <div class="message-time">
                {{ $message->created_at->format('g:i A') }}
            </div>
        </div>
    @endforeach
@else
    <div class="text-center text-muted py-3">
        No messages yet. Start a conversation!
    </div>
@endif 