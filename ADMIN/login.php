<?php
session_start(); 
require 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                
                // Cek apakah user memiliki role 1
                if ($user['role'] == 1) {
                    $_SESSION['userid'] = $user['id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['username'] = $user['username'];

                    // Redirect ke dashboard user
                    header("Location: components/dashboard.php");
                    exit;
                } else {
                    // Jika role bukan 1(admin), beri pesan kesalahan
                    $_SESSION['alertMessage'] = "You are not authorized to access this system.";
                }
            } else {
                $_SESSION['alertMessage'] = "Invalid password. Please try again.";
            }
        } else {
            $_SESSION['alertMessage'] = "No user found with this email.";
        }

        $stmt->close();
    } else {
        $_SESSION['alertMessage'] = "All fields are required.";
    }

    header("Location: login.php");
    exit;
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="/src/output.css" rel="stylesheet">
</head>

<body class="flex flex-col min-h-screen font-serif bg-[#F0EDE3]">
    <a href="../../index.php" class="px-10 text-xl font-bold mt-4">H O M E</a>

    <!-- Flex container for centering the login form -->
    <div class="flex-grow flex items-center justify-center">
        <div class="w-full max-w-xs flex flex-col items-center">

            <!-- Alert jika ada pesan error -->
            <?php if (!empty($_SESSION['alertMessage'])) : ?>
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-2 rounded relative mb-4 w-full" role="alert">
                    <strong class="font-bold">Alert!</strong>
                    <span class="block sm:inline"><?php echo $_SESSION['alertMessage']; ?></span>
                </div>
                <?php unset($_SESSION['alertMessage']); ?>
            <?php endif; ?>

            <!-- Login Form -->
            <div class="bg-white shadow-lg rounded-lg px-4 pt-4 pb-6 mb-4 w-full">
                <form action="login.php" method="POST" class="form-bg rounded px-4 pt-4 pb-4">
                    <h1 class="text-2xl font-bold mb-4 text-center text-gray-700">Log In</h1>
                    <div class="mb-3">
                        <label for="email" class="block text-gray-600 text-sm font-medium mb-1">Email:</label>
                        <input type="email" name="email" id="email" class="shadow-sm appearance-none border border-gray-300 rounded w-full py-1.5 px-2 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-gray-600 text-sm font-medium mb-1">Password:</label>
                        <input type="password" name="password" id="password" class="shadow-sm appearance-none border border-gray-300 rounded w-full py-1.5 px-2 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent" required>
                    </div>
                    <div class="flex items-center justify-center">
                        <button type="submit" class="bg-[#DBA7A7] hover:bg-[#c59191] text-white font-semibold py-1.5 px-4 rounded focus:outline-none focus:ring-2 focus:ring-[#DBA7A7] focus:ring-opacity-50 transition duration-300">LOGIN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
