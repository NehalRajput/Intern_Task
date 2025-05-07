<!DOCTYPE html>
<html class="h-full bg-white">
<head>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Intern Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Hello Intern!</h2>
            <p class="mt-4 text-center text-gray-600">Welcome to your dashboard</p>
        </div>

        <div class="mt-10 text-center space-y-4">
            <a href="{{ route('intern.tasks') }}" 
               class="inline-block rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                View My Tasks
            </a>

            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Logout
                </button>
            </form>
        </div>
    </div>
</body>
</html>