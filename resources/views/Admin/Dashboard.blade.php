@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<div class="min-h-screen bg-gray-100 py-6">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

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
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit"
                  class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
            Logout
          </button>
        </form>
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

    <!-- Task List Section -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Recent Tasks</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
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
                            <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                                <!-- Assign Button -->
                                <button onclick="openAssignModal({{ $task->id }})" 
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition-colors">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                    Assign
                                </button>

                                <!-- Assign Modal -->
                                <div id="assignModal{{ $task->id }}" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                                        <div class="mt-3">
                                            <h3 class="text-lg font-medium text-gray-900 mb-4">Assign Task to Intern</h3>
                                            <form action="{{ route('tasks.assign-intern', $task->id) }}" method="POST" class="space-y-4">
                                                @csrf
                                                <div>
                                                    <select name="intern_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                                                        <option value="">Select Intern</option>
                                                        @foreach($interns as $intern)
                                                            <option value="{{ $intern->id }}">{{ $intern->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="flex justify-end space-x-3">
                                                    <button type="button" onclick="closeAssignModal({{ $task->id }})" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-md border border-gray-300">
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
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No tasks found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function openAssignModal(taskId) {
    document.getElementById('assignModal' + taskId).classList.remove('hidden');
}

function closeAssignModal(taskId) {
    document.getElementById('assignModal' + taskId).classList.add('hidden');
}
</script>
@endsection
