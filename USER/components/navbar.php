<?php
session_start();
require '../config.php'; 

// Cek apakah pengguna telah login
if (!isset($_SESSION['userid'])) {
    header("Location: ../login.php"); 
    exit();
}

// Mendapatkan data profil pengguna dari database
$userid = $_SESSION['userid'];
$sql = "SELECT profile, username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userid);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Jika tombol logout diklik
if (isset($_GET['logout'])) {
    session_unset(); 
    session_destroy(); 
    header("Location: ../login.php"); 
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventWave</title>
    <link href="/src/output.css" rel="stylesheet">
</head>
<body>
    <div class="bg-[#DBA7A7] p-4 flex justify-between items-center my-8 rounded-full mx-10 font-serif">
        <a href="dashboard.php" class="mx-5 text-xl font-bold text-white hover:text-gray-600">E v e n t W a v e</a>
        <div class="flex justify-between">
            <a href="registered_events.php" class="hover:scale-110 transition-transform duration-300"><svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 24 24"><path fill="white" d="M18 19H6c-1.1 0-2-.9-2-2V7c0-1.1.9-2 2-2h3c1.1 0 2 .9 2 2h7c1.1 0 2 .9 2 2v8c0 1.1-.9 2-2 2"/></svg></a>
            
            <h2 class="text-sm font-bold text-gray-700 ml-5 mr-2">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
                <?php if (!empty($user['profile'])): ?>
                <?php endif; ?>

            <a href="profile.php" class="hover:scale-110 transition-transform duration-300">
                <?php if (!empty($user['profile'])): ?>
                    <img src="<?= htmlspecialchars($user['profile']); ?>" alt="Profile Picture" class="w-6 h-6 rounded-full cursor-pointer">
                <?php else: ?>
                    <img src="path/to/default/image.png" alt="Default Profile Picture" class="w-8 h-8 rounded-full cursor-pointer"> 
                <?php endif; ?>
            </a>

            <a href="?logout=true" class="ml-8 mr-4 text-sm font-semibold text-white hover:text-gray-600">|  Log Out</a>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
