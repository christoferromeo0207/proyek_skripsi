<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        // Fungsi untuk toggle menampilkan atau menyembunyikan password
        function togglePassword() {
            var passwordField = document.getElementById("password");
            var passwordConfirmField = document.getElementById("password_confirmation");
            var passwordIcon = document.getElementById("password-icon");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                passwordConfirmField.type = "text";
                passwordIcon.src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' fill='currentColor' viewBox='0 0 24 24'%3E%3Cpath fill-rule='evenodd' d='M4.998 7.78C6.729 6.345 9.198 5 12 5c2.802 0 5.27 1.345 7.002 2.78a12.713 12.713 0 0 1 2.096 2.183c.253.344.465.682.618.997.14.286.284.658.284 1.04s-.145.754-.284 1.04a6.6 6.6 0 0 1-.618.997 12.712 12.712 0 0 1-2.096 2.183C17.271 17.655 14.802 19 12 19c-2.802 0-5.27-1.345-7.002-2.78a12.712 12.712 0 0 1-2.096-2.183 6.6 6.6 0 0 1-.618-.997C2.144 12.754 2 12.382 2 12s.145-.754.284-1.04c.153-.315.365-.653.618-.997A12.714 12.714 0 0 1 4.998 7.78ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z' clip-rule='evenodd'/%3E%3C/svg%3E";
            } else {
                passwordField.type = "password";
                passwordConfirmField.type = "password";
                passwordIcon.src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' fill='currentColor' viewBox='0 0 24 24'%3E%3Cpath d='m4 15.6 3.055-3.056A4.913 4.913 0 0 1 7 12.012a5.006 5.006 0 0 1 5-5c.178.009.356.027.532.054l1.744-1.744A8.973 8.973 0 0 0 12 5.012c-5.388 0-10 5.336-10 7A6.49 6.49 0 0 0 4 15.6Z'/%3E%3Cpath d='m14.7 10.726 4.995-5.007A.998.998 0 0 0 18.99 4a1 1 0 0 0-.71.305l-4.995 5.007a2.98 2.98 0 0 0-.588-.21l-.035-.01a2.981 2.981 0 0 0-3.584 3.583c0 .012.008.022.01.033.05.204.12.402.211.59l-4.995 4.983a1 1 0 1 0 1.414 1.414l4.995-4.983c.189.091.386.162.59.211.011 0 .021.007.033.01a2.982 2.982 0 0 0 3.584-3.584c0-.012-.008-.023-.011-.035a3.05 3.05 0 0 0-.21-.588Z'/%3E%3Cpath d='m19.821 8.605-2.857 2.857a4.952 4.952 0 0 1-5.514 5.514l-1.785 1.785c.767.166 1.55.25 2.335.251 6.453 0 10-5.258 10-7 0-1.166-1.637-2.874-2.179-3.407Z'/%3E%3C/svg%3E";
            }
        }
    </script>
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded shadow-md">
        <h2 class="text-2xl font-bold text-center text-gray-700">Reset Your Password</h2>

        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded" role="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
            @csrf

            <input type="hidden" name="username" value="{{ $username }}">


            <!-- New Password and Confirm -->

            <div>
                <label for="password" class="block text-sm font-medium text-gray-600">New Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required 
                           class="w-full px-3 py-2 mt-1 text-gray-700 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-200" 
                           placeholder="Enter your new password">
                    <button type="button" onclick="togglePasswordVisibility('password', 'password-icon')" class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <img id="password-icon" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' fill='currentColor' viewBox='0 0 24 24'%3E%3Cpath d='m4 15.6 3.055-3.056A4.913 4.913 0 0 1 7 12.012a5.006 5.006 0 0 1 5-5c.178.009.356.027.532.054l1.744-1.744A8.973 8.973 0 0 0 12 5.012c-5.388 0-10 5.336-10 7A6.49 6.49 0 0 0 4 15.6Z'/%3E%3Cpath d='m14.7 10.726 4.995-5.007A.998.998 0 0 0 18.99 4a1 1 0 0 0-.71.305l-4.995 5.007a2.98 2.98 0 0 0-.588-.21l-.035-.01a2.981 2.981 0 0 0-3.584 3.583c0 .012.008.022.01.033.05.204.12.402.211.59l-4.995 4.983a1 1 0 1 0 1.414 1.414l4.995-4.983c.189.091.386.162.59.211.011 0 .021.007.033.01a2.982 2.982 0 0 0 3.584-3.584c0-.012-.008-.023-.011-.035a3.05 3.05 0 0 0-.21-.588Z'/%3E%3Cpath d='m19.821 8.605-2.857 2.857a4.952 4.952 0 0 1-5.514 5.514l-1.785 1.785c.767.166 1.55.25 2.335.251 6.453 0 10-5.258 10-7 0-1.166-1.637-2.874-2.179-3.407Z'/%3E%3C/svg%3E" alt="Show/Hide Password" />
                    </button>
                </div>
            </div>
            
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-600">Confirm Password</label>
                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation" 
                           class="w-full px-3 py-2 mt-1 text-gray-700 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-200" 
                           placeholder="Confirm your password" required>
                    <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'password-confirm-icon')" class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <img id="password-confirm-icon" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' fill='currentColor' viewBox='0 0 24 24'%3E%3Cpath d='m4 15.6 3.055-3.056A4.913 4.913 0 0 1 7 12.012a5.006 5.006 0 0 1 5-5c.178.009.356.027.532.054l1.744-1.744A8.973 8.973 0 0 0 12 5.012c-5.388 0-10 5.336-10 7A6.49 6.49 0 0 0 4 15.6Z'/%3E%3Cpath d='m14.7 10.726 4.995-5.007A.998.998 0 0 0 18.99 4a1 1 0 0 0-.71.305l-4.995 5.007a2.98 2.98 0 0 0-.588-.21l-.035-.01a2.981 2.981 0 0 0-3.584 3.583c0 .012.008.022.01.033.05.204.12.402.211.59l-4.995 4.983a1 1 0 1 0 1.414 1.414l4.995-4.983c.189.091.386.162.59.211.011 0 .021.007.033.01a2.982 2.982 0 0 0 3.584-3.584c0-.012-.008-.023-.011-.035a3.05 3.05 0 0 0-.21-.588Z'/%3E%3Cpath d='m19.821 8.605-2.857 2.857a4.952 4.952 0 0 1-5.514 5.514l-1.785 1.785c.767.166 1.55.25 2.335.251 6.453 0 10-5.258 10-7 0-1.166-1.637-2.874-2.179-3.407Z'/%3E%3C/svg%3E" alt="Show/Hide Password" />
                    </button>
                </div>
            </div>
            
            
            
            
            
            <div class="flex justify-center">
                <button type="submit" class="w-full py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-200">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</body>
</html>


<script>
   function togglePasswordVisibility(inputId, iconId) {
        const passwordField = document.getElementById(inputId);
        const passwordIcon = document.getElementById(iconId);

        if (passwordField.type === "password") {
            passwordField.type = "text";
            passwordIcon.src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3E%3Cpath d='M12 9a3.02 3.02 0 0 0-3 3c0 1.642 1.358 3 3 3 1.641 0 3-1.358 3-3 0-1.641-1.359-3-3-3z'></path><path d='M12 5c-7.633 0-9.927 6.617-9.948 6.684L1.946 12l.105.316C2.073 12.383 4.367 19 12 19s9.927-6.617 9.948-6.684l.106-.316-.105-.316C21.927 11.617 19.633 5 12 5zm0 12c-5.351 0-7.424-3.846-7.926-5C4.578 10.842 6.652 7 12 7c5.351 0 7.424 3.846 7.926 5-.504 1.158-2.578 5-7.926 5z'></path></svg>";
        } else {
            passwordField.type = "password";
            passwordIcon.src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' fill='currentColor' viewBox='0 0 24 24'%3E%3Cpath d='m4 15.6 3.055-3.056A4.913 4.913 0 0 1 7 12.012a5.006 5.006 0 0 1 5-5c.178.009.356.027.532.054l1.744-1.744A8.973 8.973 0 0 0 12 5.012c-5.388 0-10 5.336-10 7A6.49 6.49 0 0 0 4 15.6Z'/%3E%3Cpath d='m14.7 10.726 4.995-5.007A.998.998 0 0 0 18.99 4a1 1 0 0 0-.71.305l-4.995 5.007a2.98 2.98 0 0 0-.588-.21l-.035-.01a2.981 2.981 0 0 0-3.584 3.583c0 .012.008.022.01.033.05.204.12.402.211.59l-4.995 4.983a1 1 0 1 0 1.414 1.414l4.995-4.983c.189.091.386.162.59.211.011 0 .021.007.033.01a2.982 2.982 0 0 0 3.584-3.584c0-.012-.008-.023-.011-.035a3.05 3.05 0 0 0-.21-.588Z'/%3E%3Cpath d='m19.821 8.605-2.857 2.857a4.952 4.952 0 0 1-5.514 5.514l-1.785 1.785c.767.166 1.55.25 2.335.251 6.453 0 10-5.258 10-7 0-1.166-1.637-2.874-2.179-3.407Z'/%3E%3C/svg%3E";
        }
    }
</script>
  


