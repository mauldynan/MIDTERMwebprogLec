<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User and Admin Login</title>
    <link href="/src/output.css" rel="stylesheet">
</head>
<body class="bg-[#F0EDE3] font-serif">

    <div class="flex justify-center mt-10 mb-10">
        <h2 class="text-xl font-bold text-gray-700 bg-[#DBA7A7] p-4 px-10 text-center rounded-full">Welcome to the Eventwave System</h2>
    </div>

    <div class="container mx-auto my-8 px-4"> 
        <main class="mt-8 mb-8">
            <div class="flex flex-col md:flex-row justify-center items-stretch space-y-8 md:space-y-0 md:space-x-8"> 
                <!-- User Section -->
                <div class="bg-white rounded-lg shadow-lg p-12 text-center w-full md:w-1/2 lg:w-1/3 transition-transform transform hover:scale-105 flex flex-col">
                    <h3 class="text-2xl font-semibold mb-4 text-[#DBA7A7]">User</h3>
                    <p class="mb-4">Please login or register to access your account.</p>
                    <div class="flex justify-center space-x-4 mt-auto">
                        <!-- User Login Button -->
                        <a href="/USER/login.php" class="text-black py-2 px-10 rounded-full transition bg-[#FFC700]">Login</a>
                        <!-- User Register Button -->
                        <a href="/USER/signup.php" class="text-black py-2 px-10 rounded-full transition bg-[#FFC700]">Register</a>
                    </div>
                </div>

                <!-- Admin Section -->
                <div class="bg-white rounded-lg shadow-lg p-12 text-center w-full md:w-1/2 lg:w-1/3 transition-transform transform hover:scale-105 flex flex-col">
                    <h3 class="text-2xl font-semibold mb-4 text-[#DBA7A7]">Admin</h3>
                    <p class="mb-4">Admin can login to manage the system.</p>
                    <div class="flex justify-center mt-auto">
                        <!-- Admin Login Button -->
                        <a href="/ADMIN/login.php" class="text-black py-2 px-10 rounded-full transition bg-[#FFC700]">Login</a>
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
