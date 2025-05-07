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

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Dashboard Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Tasks</h1>
                    <p class="mt-1 text-lg text-gray-600">View all your assigned tasks</p>
                </div>
            </div>
        </div>

        <!-- Task List -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comments</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tasks as $task)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $task->title }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">{{ Str::limit($task->description, 50) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">{{ $task->due_date ? date('M d, Y', strtotime($task->due_date)) : 'No due date' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('intern.update-task-status', $task->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()" 
                                                class="text-sm rounded-full px-3 py-1 font-semibold
                                                @if($task->status === 'completed') bg-green-100 text-green-800
                                                @elseif($task->status === 'todo') bg-blue-100 text-blue-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                            <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="todo" {{ $task->status === 'todo' ? 'selected' : '' }}>To Do</option>
                                            <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-6 py-4">
                                    <button onclick="openCommentsModal({{ $task->id }})" 
                                            class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 transition-colors">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                        </svg>
                                        Comments
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No tasks assigned yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@foreach($tasks as $task)
    <!-- Comments Modal -->
    <div id="commentsModal{{ $task->id }}" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Comments</h3>
                    <button onclick="closeCommentsModal({{ $task->id }})" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Comments List -->
                <div class="space-y-4 mb-4 max-h-96 overflow-y-auto">
                    @forelse($task->comments as $comment)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        @if($comment->is_query)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Query
                                            </span>
                                        @endif
                                        <p class="text-sm text-gray-800">{{ $comment->content }}</p>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        By {{ $comment->user->name }} • {{ $comment->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center">No comments yet</p>
                    @endforelse
                </div>

                <!-- Add Comment Form -->
                <form action="{{ route('comments.store', $task->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Add Comment</label>
                        <textarea name="content" id="content" rows="3" 
                                  class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md"
                                  placeholder="Write your comment here..."></textarea>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="is_query" id="is_query" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_query" class="text-sm text-gray-700">Mark as Query</label>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-md">
                            Add Comment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<script>
function openCommentsModal(taskId) {
    document.getElementById('commentsModal' + taskId).classList.remove('hidden');
}

function closeCommentsModal(taskId) {
    document.getElementById('commentsModal' + taskId).classList.add('hidden');
}
</script>
@endsection