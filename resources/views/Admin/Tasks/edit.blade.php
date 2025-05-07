@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 transition-all duration-300 hover:shadow-xl">
            <!-- Header -->
            <div class="border-b border-gray-200 bg-white px-4 py-4">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center shadow-inner">
                            <svg class="h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900 tracking-tight">{{ isset($task) ? 'Edit' : 'Create' }} Task</h1>
                        <p class="text-xs text-gray-500 mt-0.5">Fill in the details below to {{ isset($task) ? 'update' : 'create' }} a task</p>
                    </div>
                </div>
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
                <div class="px-4 py-4 bg-white flex items-center justify-end border-t border-gray-200">
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 transition duration-200 hover:shadow-lg">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Update Task
                    </button>
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
