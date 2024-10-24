<?php
session_start();
require '../config.php'; 

// Ambil data pengguna dari session
$userId = $_SESSION['userid']; 

// Mengambil data pengguna dari database
$sql = "SELECT username, email, password, profile FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$successMessage = '';
$errorMessage = '';

// Proses form jika ada pengunggahan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']); 
    $newPassword = trim($_POST['new_password']); 
    
    // Validasi password saat mengupdate
    if (!empty($newPassword)) {
        if (password_verify($password, $user['password'])) { 
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $updatePasswordSql = "UPDATE users SET password = ? WHERE id = ?";
            $updatePasswordStmt = $conn->prepare($updatePasswordSql);
            $updatePasswordStmt->bind_param('si', $passwordHash, $userId);
            $updatePasswordStmt->execute();
            $successMessage = "Password berhasil diperbarui!";
        } else {
            $errorMessage = "Password lama tidak valid!";
        }
    }

    // Update informasi pengguna (username dan email)
    $updateSql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param('ssi', $username, $email, $userId);
    $updateStmt->execute();

    // Proses pengunggahan gambar hanya jika ada gambar yang dipilih
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] == 0) {
        $targetDir = "../../pp_user/"; 
        // Buat nama file unik
        $fileName = uniqid() . '_' . basename($_FILES["profile"]["name"]);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Cek apakah file adalah gambar
        $check = getimagesize($_FILES["profile"]["tmp_name"]);
        if ($check !== false) {
            // Simpan gambar ke folder dan update ke database
            if (move_uploaded_file($_FILES["profile"]["tmp_name"], $targetFile)) {
                $updatePictureSql = "UPDATE users SET profile = ? WHERE id = ?";
                $updatePictureStmt = $conn->prepare($updatePictureSql);
                $updatePictureStmt->bind_param('si', $targetFile, $userId);
                $updatePictureStmt->execute();
                $successMessage = "Data berhasil diupdate!";
            } else {
                $errorMessage = "Gagal mengunggah gambar. Error: " . print_r(error_get_last(), true);
            }
        } else {
            $errorMessage = "File yang diunggah bukan gambar.";
        }
    }
}

// Mengambil pesan dari session jika ada dan menghapusnya setelah ditampilkan
if (isset($_SESSION['successMessage']) && isset($_SESSION['formSubmitted'])) {
    $successMessage = $_SESSION['successMessage'];
    unset($_SESSION['successMessage']); 
    unset($_SESSION['formSubmitted']); 
}

// Ambil data nama event berdasarkan user_id
$eventSql = "SELECT events.name FROM history 
              INNER JOIN events ON history.event_id = events.id 
              WHERE history.user_id = ?";
$eventStmt = $conn->prepare($eventSql);
$eventStmt->bind_param('i', $userId);
$eventStmt->execute();
$eventResult = $eventStmt->get_result();

// Simpan nama-nama event dalam array asosiatif untuk menghindari duplikat
$eventNames = [];
while ($row = $eventResult->fetch_assoc()) {
    $eventNames[$row['name']] = true; 
}

// Mengambil hanya nama-nama event unik
$eventNames = array_keys($eventNames);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="/src/output.css" rel="stylesheet">
</head>
<body class="bg-[#F0EDE3] font-serif">
    <div class="container mx-auto p-12">
        <div class="flex justify-between p-6 rounded-lg shadow-md bg-[#DBA7A7]">
            <!-- Update Profile -->
            <div class="w-1/2">
                <?php if ($successMessage): ?>
                    <div class="alert bg-green-100 border border-green-400 text-green-700 p-4 rounded-lg shadow-lg max-w-xs fixed top-18 right-10" role="alert">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline"><?php echo htmlspecialchars($successMessage); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($errorMessage): ?>
                    <div class="alert bg-red-100 border border-red-400 text-red-700 p-4 rounded-lg shadow-lg max-w-xs fixed top-18 right-10" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline"><?php echo htmlspecialchars($errorMessage); ?></span>
                    </div>
                <?php endif; ?>

                <div class="flex justify-center mb-4">
                    <?php if ($user['profile']): ?>
                        <img src="<?= htmlspecialchars($user['profile']); ?>" alt="Profile Picture" class="w-32 h-32 rounded-full mb-2">
                    <?php else: ?>
                        <img src="path/to/default/image.png" alt="Default Profile Picture" class="w-32 h-32 rounded-full mb-2"> 
                    <?php endif; ?>
                </div>
                            
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="username" class="block text-gray-700">Username:</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>" class="border rounded w-full p-2" required>
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700">Email:</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" class="border rounded w-full p-2" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700">Current Password:</label>
                        <input type="password" name="password" class="border rounded w-full p-2" placeholder="Enter Current Password">
                    </div>
                    <div class="mb-4">
                        <label for="new_password" class="block text-gray-700">New Password:</label>
                        <input type="password" name="new_password" class="border rounded w-full p-2" placeholder="New Password (leave blank if no change)">
                    </div>
                    <div class="mb-4">
                        <label for="profile" class="block text-gray-700">Profile Picture:</label>
                        <input type="file" name="profile" accept="image/*" class="w-full p-2">
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-950">Update Profile</button>
                        <a href="dashboard.php" class="bg-gray-400 text-white p-2 py-2.5 rounded hover:bg-slate-600">Back</a>
                    </div>
                </form>
            </div>

            <!-- History Event -->
            <div class="w-1/2 pl-6 mt-8">
                <h2 class="text-2xl text-center text-gray-600 font-bold mb-6">Event Registration History</h2>
                <div class="bg-white shadow rounded-lg p-4">
                    <ul class="space-y-2">
                        <?php if (empty($eventNames)): ?>
                            <li class="text-gray-500 text-center">Belum ada event yang didaftarkan.</li>
                        <?php else: ?>
                            <?php foreach ($eventNames as $eventName): ?>
                                <li class="bg-gray-100 border border-gray-300 rounded-lg p-3 text-gray-700 hover:bg-gray-200 transition duration-200">
                                    <?= htmlspecialchars($eventName); ?>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <script>
                window.onload = function() {
                    const alertBox = document.querySelector('.alert');
                    if (alertBox) {
                        setTimeout(function() {
                            alertBox.style.display = 'none';
                        }, 2000);
                    }
                }
            </script>
        </div>
    </div>
</body>
</html>
