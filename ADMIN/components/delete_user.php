<?php
session_start();
require 'config.php'; 

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = intval($_GET['id']);

    $conn->begin_transaction();

    try {
        $deleteRegistrationStmt = $conn->prepare("DELETE FROM registrations WHERE user_id = ?");
        $deleteRegistrationStmt->bind_param("i", $user_id);
        $deleteRegistrationStmt->execute();
        $deleteRegistrationStmt->close();

        $deleteUserStmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $deleteUserStmt->bind_param("i", $user_id);
        $deleteUserStmt->execute();
        $deleteUserStmt->close();

        $conn->commit();

        header("Location: user_management.php");
        exit;
    } catch (Exception $e) {
        $conn->rollback();

        header("Location: user_management.php");
        exit;
    }
} 

$conn->close();
?>
