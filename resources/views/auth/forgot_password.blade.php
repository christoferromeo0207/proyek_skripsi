<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded shadow-md">
        <h2 class="text-2xl font-bold text-center text-gray-700">Forgot Password</h2>

        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded" role="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('check-username') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="username" class="block text-sm font-medium text-gray-600">Username</label>
                <input type="text" id="username" name="username" required 
                       class="w-full px-3 py-2 mt-1 text-gray-700 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-200" 
                       placeholder="Enter your username">
            </div>
        
            <button type="submit" class="w-full px-4 py-2 font-semibold text-white bg-blue-500 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
                Check Username
            </button>
        </form>
        
    </div>
</body>
</html>
