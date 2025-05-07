@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>


<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 transition-all duration-300 hover:shadow-xl">
            <!-- Header -->
            <div class="border-b border-gray-200 bg-white px-4 py-4 relative">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center shadow-inner">
                            <svg class="h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900 tracking-tight">
                            {{ isset($task) ? 'Edit Task' : 'Create Task' }}
                        </h1>
                        <p class="text-xs text-gray-500 mt-0.5">Fill in the details below to {{ isset($task) ? 'update' : 'create' }} a task</p>
                    </div>
                </div>

                <!-- Show "New Task" button only in create mode -->
                @if (!isset($task))
                <a href="{{ route('tasks.create') }}"
                   class="absolute right-4 top-4 inline-flex items-center px-3 py-2 bg-indigo-500 text-white text-sm font-medium rounded hover:bg-indigo-600 shadow">
                    + New Task
                </a>
                @endif
            </div>

            <!-- Form -->
            <form method="POST" action="{{ isset($task) ? route('tasks.update', $task) : route('tasks.store') }}" class="divide-y divide-gray-200">
                @csrf
                @if(isset($task)) @method('PUT') @endif

                <div class="px-4 py-6 space-y-5 bg-gray-50">
                    <!-- Title Field -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1 tracking-wide">Title</label>
                        <input type="text" name="title" id="title" 
                            class="block w-full rounded-lg border border-gray-300 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 text-sm h-10 px-4 py-2 transition duration-200 ease-in-out bg-gray-50 hover:bg-white"
                            value="{{ old('title', $task->title ?? '') }}" 
                            placeholder="Enter task title"
                            required>
                        @error('title')
                            <p class="mt-1 text-xs text-red-600 animate-pulse">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description Field -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1 tracking-wide">Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="block w-full rounded-lg border border-gray-300 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 text-sm px-4 py-2 bg-gray-50 hover:bg-white transition"
                            placeholder="Enter detailed description"
                            required>{{ old('description', $task->description ?? '') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-600 animate-pulse">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Due Date Field -->
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1 tracking-wide">Due Date</label>
                        <input type="date" name="due_date" id="due_date"
                            class="block w-full rounded-lg border border-gray-300 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 text-sm h-10 px-4 py-2 transition duration-200 ease-in-out bg-gray-50 hover:bg-white"
                            value="{{ old('due_date', $task->due_date ?? '') }}"
                            min="{{ now()->format('Y-m-d') }}">
                        @error('due_date')
                            <p class="mt-1 text-xs text-red-600 animate-pulse">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Assign Interns Field -->
                    <div>
                        <label for="interns" class="block text-sm font-medium text-gray-700 mb-1 tracking-wide">Assign Interns</label>
                        <select name="interns[]" id="interns" multiple
                            class="block w-full rounded-lg border border-gray-300 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 text-sm px-4 py-2 bg-gray-50 hover:bg-white transition-all scrollbar-thin scrollbar-thumb-indigo-300 scrollbar-track-gray-100"
                            size="4">
                            @foreach($interns as $intern)
                                <option value="{{ $intern->id }}"
                                    {{ isset($assigned) && in_array($intern->id, $assigned) ? 'selected' : '' }}>
                                    {{ $intern->name }} ({{ $intern->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('interns')
                            <p class="mt-1 text-xs text-red-600 animate-pulse">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="fixed bottom-0 left-0 right-0 px-4 py-4 bg-white shadow-lg border-t border-gray-200">
                    <div class="max-w-2xl mx-auto">
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform hover:scale-105 transition-all duration-200">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Task
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Optional Scrollbar Enhancer -->
<style>
select::-webkit-scrollbar {
    width: 6px;
}
select::-webkit-scrollbar-thumb {
    background-color: #a5b4fc; /* indigo-300 */
    border-radius: 4px;
}
select::-webkit-scrollbar-track {
    background-color: #f3f4f6; /* gray-100 */
}
</style>
@endsection
