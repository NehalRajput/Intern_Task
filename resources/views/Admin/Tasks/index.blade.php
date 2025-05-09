@extends('Layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<div class="min-h-screen bg-gray-50 py-6">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
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

    <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
      <div class="px-6 py-4 flex justify-between items-center border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">Task List</h2>
        <a href="{{ route('admin.tasks.create') }}"
           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700 transition">
          + Create Task
        </a>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 font-medium text-gray-700">Title</th>
              <th class="px-6 py-3 font-medium text-gray-700">Description</th>
              <th class="px-6 py-3 font-medium text-gray-700">Due Date</th>
              <th class="px-6 py-3 font-medium text-gray-700">Assigned Interns</th>
              <th class="px-6 py-3 font-medium text-gray-700">Comments</th>
              <th class="px-6 py-3 font-medium text-gray-700">Actions</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assign</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
              @forelse ($tasks as $task)
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
                          <div class="flex flex-wrap gap-1 max-h-16 overflow-y-auto">
                              @forelse($task->interns as $intern)
                                  <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                      <svg class="w-2.5 h-2.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                      </svg>
                                      {{ $intern->name }}
                                      <form action="{{ route('admin.tasks.detach-intern', ['task' => $task->id, 'intern' => $intern->id]) }}" method="POST" class="inline ml-1">
                                          @csrf
                                          @method('DELETE')
                                          <button type="submit" class="text-red-600 hover:text-red-800">
                                              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                              </svg>
                                          </button>
                                      </form>
                                  </span>
                              @empty
                                  <span class="text-xs text-gray-500">None</span>
                              @endforelse
                          </div>
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
                      <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                          <a href="{{ route('admin.tasks.edit', $task->id) }}" 
                             class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700 transition-colors">
                              <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                              </svg>
                              Edit
                          </a>
                          <button onclick="openDeleteModal({{ $task->id }})"
                                  class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition-colors">
                              <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                              </svg>
                              Delete
                          </button>
                      </td>
                      <td class="px-6 py-4 text-right text-sm font-medium">
                          <button onclick="openAssignModal({{ $task->id }})" 
                                  class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition-colors">
                              <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                              </svg>
                              Assign
                          </button>
                      </td>
                  </tr>
              @empty
                  <tr>
                      <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                          No tasks found
                      </td>
                  </tr>
              @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@foreach($tasks as $task)
    <!-- Assign Modal -->
    <div id="assignModal{{ $task->id }}" class="hidden fixed inset-0 bg-gray-600/50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Assign Task to Intern</h3>
                <form action="{{ route('admin.tasks.assign-intern', $task->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="intern_id" class="block text-sm font-medium text-gray-700 mb-1">Select Intern</label>
                        <select name="intern_id" id="intern_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                            <option value="">Choose an intern</option>
                            @foreach($interns as $intern)
                                <option value="{{ $intern->id }}">{{ $intern->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3 mt-5">
                        <button type="button" onclick="closeAssignModal({{ $task->id }})" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-md border border-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-md">
                            Assign
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Comments Modal -->
    <div id="commentsModal{{ $task->id }}" class="hidden fixed inset-0 bg-gray-600/50 overflow-y-auto h-full w-full z-50">
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
                                        <div id="comment-content-{{ $comment->id }}" class="text-sm text-gray-800">{{ $comment->content }}</div>
                                    </div>
                                    <form id="edit-form-{{ $comment->id }}" action="{{ route('admin.comments.update', $comment->id) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('PUT')
                                        <textarea name="content" rows="2" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">{{ $comment->content }}</textarea>
                                        <div class="flex justify-end space-x-2 mt-2">
                                            <button type="button" onclick="cancelEdit({{ $comment->id }})" class="px-2 py-1 text-xs text-gray-600 hover:text-gray-800">Cancel</button>
                                            <button type="submit" class="px-2 py-1 text-xs text-white bg-indigo-600 hover:bg-indigo-700 rounded">Save</button>
                                        </div>
                                    </form>
                                    <p class="text-xs text-gray-500 mt-1">
                                        By {{ $comment->user->name }} • {{ $comment->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="startEdit({{ $comment->id }})" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center">No comments yet</p>
                    @endforelse
                </div>

                <!-- Add Comment Form -->
                <form action="{{ route('admin.comments.store', $task->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Add Comment</label>
                        <textarea name="content" id="content" rows="3" 
                                  class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md"
                                  placeholder="Write your comment here..."></textarea>
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

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal{{ $task->id }}" class="hidden fixed inset-0 bg-gray-600/50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Delete Task</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Are you sure you want to delete this task? This action cannot be undone.
                    </p>
                </div>
                <div class="flex justify-center gap-4 mt-3">
                    <button onclick="closeDeleteModal({{ $task->id }})"
                            class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                    <form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- Add this JavaScript at the bottom of the file -->
<script>
function openAssignModal(taskId) {
    document.getElementById('assignModal' + taskId).classList.remove('hidden');
}

function closeAssignModal(taskId) {
    document.getElementById('assignModal' + taskId).classList.add('hidden');
}

function openCommentsModal(taskId) {
    document.getElementById('commentsModal' + taskId).classList.remove('hidden');
}

function closeCommentsModal(taskId) {
    document.getElementById('commentsModal' + taskId).classList.add('hidden');
}

function startEdit(commentId) {
    document.getElementById('comment-content-' + commentId).classList.add('hidden');
    document.getElementById('edit-form-' + commentId).classList.remove('hidden');
}

function cancelEdit(commentId) {
    document.getElementById('comment-content-' + commentId).classList.remove('hidden');
    document.getElementById('edit-form-' + commentId).classList.add('hidden');
}

function openDeleteModal(taskId) {
    document.getElementById('deleteModal' + taskId).classList.remove('hidden');
}

function closeDeleteModal(taskId) {
    document.getElementById('deleteModal' + taskId).classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('fixed')) {
        event.target.classList.add('hidden');
    }
}
</script>
@endsection
