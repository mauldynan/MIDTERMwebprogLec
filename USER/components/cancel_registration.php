<?php
session_start();
include '../config.php';

// Ambil data dari permintaan POST
$data = json_decode(file_get_contents('php://input'), true);
$registrationId = $data['registration_id'] ?? null;

// Pastikan ID registrasi ada
if ($registrationId) {
    // Query untuk menghapus registrasi
    $sql = "DELETE FROM registrations WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $registrationId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
}

$conn->close();
?>
