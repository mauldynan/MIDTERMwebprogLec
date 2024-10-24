<?php
session_start();
require 'config.php';

if (isset($_GET['user_id'])) {
    $userId = intval($_GET['user_id']);

    $stmt = $conn->prepare("
        SELECT DISTINCT e.name 
        FROM history h 
        JOIN events e ON h.event_id = e.id 
        WHERE h.user_id = ?
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row; 
    }

    $stmt->close();
    echo json_encode($events);
} else {
    echo json_encode([]); 
}

$conn->close();
?>
