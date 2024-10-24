<?php
require '../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika tombol logout diklik
if (isset($_GET['logout'])) {
    session_unset(); 
    session_destroy(); 
    header("Location: ../login.php"); 
    exit();
}

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php"); 
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link href="/src/output.css" rel="stylesheet">
</head>
<body>
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
    ?>

    <div class="bg-[#DBA7A7] p-4 flex justify-between items-center mt-7 rounded-full mx-10">
        <a href="dashboard.php" class="mx-5 text-xl font-bold text-white hover:text-gray-600">E v e n t W a v e</a>
        
        <div class="flex items-center space-x-6 mx-5">

            <a href="event_management.php" class="hover:scale-110 transition-transform duration-300"><svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 24 24"><path fill="white" d="M17 8h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-9a2 2 0 0 1 2-2h2v2H5v9h14v-9h-2zM6.5 5.5l1.414 1.414L11 3.828V14h2V3.828l3.086 3.086L17.5 5.5L12 0z"/></svg></a>
            <a href="user_management.php" class="hover:scale-110 transition-transform duration-300"><svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 24 24"><path fill="white" d="m14 20l6-6V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2zM6 6h12v6h-4a2 2 0 0 0-2 2v4H6zm10 4H8V8h8z"/></svg></a>

            <div class="flex justify-between space-x-2">
                <span class="font-semibold text-gray-700">Hi, <?php echo htmlspecialchars($username); ?>!</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 24 24"><path fill="white" d="M12 4a4 4 0 1 1 0 8a4 4 0 0 1 0-8m0 16s8 0 8-2c0-2.4-3.9-5-8-5s-8 2.6-8 5c0 2 8 2 8 2"/></svg>
            </div>  

            <a href="?logout=true" class="mx-5 font-semibold text-white hover:text-gray-600">| Log Out</a>
        </div>
        
    </div>

</body>
</html>