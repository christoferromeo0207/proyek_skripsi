<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded shadow-md">
        <h2 class="text-2xl font-bold text-center text-gray-700">Create Your Account</h2>
        
        @if (session('error'))
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-600">Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                       class="w-full px-3 py-2 mt-1 text-gray-700 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-200" 
                       placeholder="Enter your full name">
            </div>
            
            <div>
                <label for="username" class="block text-sm font-medium text-gray-600">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required 
                       class="w-full px-3 py-2 mt-1 text-gray-700 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-200" 
                       placeholder="Enter your username">
            </div>
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-600">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                       class="w-full px-3 py-2 mt-1 text-gray-700 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-200" 
                       placeholder="Enter your email">
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-600">Password</label>
                <input type="password" id="password" name="password" required 
                       class="w-full px-3 py-2 mt-1 text-gray-700 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-200" 
                       placeholder="Enter your password">
            </div>
            
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-600">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required 
                       class="w-full px-3 py-2 mt-1 text-gray-700 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-200" 
                       placeholder="Confirm your password">
            </div>
            
            <button type="submit" class="w-full px-4 py-2 font-semibold text-white bg-blue-500 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
                Register
            </button>

        </form>

        <div class="text-center">
            <a href="{{ route('login') }}" class="inline-block px-4 py-2 mt-4 text-sm font-medium text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                Back to Login
            </a>
        </div>
    </div>
</body>
</html>
