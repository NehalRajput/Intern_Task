@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

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
@endsection
