<?php
session_start();
require 'config.php'; 

$message = ""; 
$alertType = ""; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($email) && !empty($password)) {
        // Enkripsi password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Cek apakah email sudah terdaftar
        $checkSql = "SELECT * FROM users WHERE email = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param('s', $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            // Email sudah terdaftar
            $message = "Email sudah terdaftar. Silakan gunakan email lain.";
            $alertType = "error";
        } else {
            // Jika email belum terdaftar, tambahkan pengguna baru ke database
            $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 0)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sss', $username, $email, $passwordHash);

            if ($stmt->execute()) {
                // Jika berhasil
                $message = "Registrasi berhasil! Silakan login.";
                $alertType = "success";
            } else {
                // Jika terjadi kesalahan saat menyimpan
                $message = "Registrasi gagal. Silakan coba lagi.";
                $alertType = "error";
            }
        }

        $checkStmt->close();
    } else {
        $message = "Semua kolom harus diisi.";
        $alertType = "error";
    }
}

$conn->close();
?>




<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>SignUp</title>
        <link href="/src/output.css" rel="stylesheet">
    </head>
    
<body class="flex items-center justify-center min-h-screen font-serif bg-[#F0EDE3]">
    <div class="w-full max-w-xs flex flex-col items-center">
    <div class="bg-white shadow-lg rounded-lg px-4 pt-4 pb-6 mb-4 w-full">
        <form action="" method="POST" class="form-bg rounded px-4 pt-4 pb-4">
            <h1 class="text-2xl font-bold mb-4 text-center text-gray-700">Create new account</h1>
            
            <div class="mb-3">
                <label for="username" class="block text-gray-600 text-sm font-medium mb-1">Username:</label>
                <input type="text" name="username" id="username" class="shadow-sm appearance-none border border-gray-300 rounded w-full py-1.5 px-2 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent" required>
            </div>
            
            <div class="mb-3">
                <label for="email" class="block text-gray-600 text-sm font-medium mb-1">Email:</label>
                <input type="email" name="email" id="email" class="shadow-sm appearance-none border border-gray-300 rounded w-full py-1.5 px-2 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent" required>
            </div>
            
            <div class="mb-4">
                <label for="password" class="block text-gray-600 text-sm font-medium mb-1">Password:</label>
                <input type="password" name="password" id="password" class="shadow-sm appearance-none border border-gray-300 rounded w-full py-1.5 px-2 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent" required>
            </div>
            
            <div class="flex items-center justify-center">
                <button type="submit" class="bg-[#DBA7A7] hover:bg-[#c59191] text-white font-semibold py-1.5 px-4 rounded focus:outline-none focus:ring-2 focus:ring-[#DBA7A7] focus:ring-opacity-50 transition duration-300">REGISTER</button>
            </div>
        </form>
        
        <p class="text-center text-gray-500 text-xs">
            Already have an account? <a href="login.php" class="text-blue-500 hover:text-blue-700">Log In</a>
        </p>
    </div>
    </div>

    <!-- Modal Pop-up -->
    <?php if ($message): ?>
        <div id="modal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50">
            <div class="bg-white p-6 rounded shadow-md text-center">
                <h2 class="text-2xl font-bold mb-4 <?php echo $alertType === 'success' ? 'text-green-500' : 'text-red-500'; ?>">
                    <?php echo $alertType === 'success' ? 'Success' : 'Error'; ?>
                </h2>
                <p class="mb-6"><?php echo $message; ?></p>
                <button onclick="closeModal()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">OK</button>
            </div>
        </div>
        <script>
            function closeModal() {
                document.getElementById('modal').style.display = 'none';

                // Jika registrasi berhasil, arahkan ke halaman login
                <?php if ($alertType === 'success'): ?>
                    window.location.href = 'login.php';
                <?php endif; ?>
            }
        </script>
    <?php endif; ?>

</body>
</html>
