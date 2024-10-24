<?php
session_start();
require 'config.php';

if (isset($_POST['event_id'])) {
    $event_id = intval($_POST['event_id']);

    // Fetch event details
    $eventStmt = $conn->prepare("SELECT name, max_participants FROM events WHERE id = ?");
    $eventStmt->bind_param("i", $event_id);
    $eventStmt->execute();
    $eventResult = $eventStmt->get_result();

    // Check if event exists
    if ($eventResult->num_rows > 0) {
        $event = $eventResult->fetch_assoc();
        $eventName = $event['name'];
        $maxParticipants = $event['max_participants'];

        // Query to count the number of registrations based on event_id
        $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM registrations WHERE event_id = ?");
        $countStmt->bind_param("i", $event_id);
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $countRow = $countResult->fetch_assoc();
        $totalParticipants = $countRow['total'];

        // Fetch registrations for the event
        $stmt = $conn->prepare("
            SELECT u.username, u.email, r.registration_date 
            FROM users u
            JOIN registrations r ON u.id = r.user_id 
            WHERE r.event_id = ?
        ");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        
        $result = $stmt->get_result();

        // Set headers for CSV file
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="list_participants.csv"');

        $output = fopen('php://output', 'w');

        fputcsv($output, ['Event Name', $eventName]);
        fputcsv($output, ['Total Participants', $totalParticipants]);
        fputcsv($output, ['Max Participants', $maxParticipants]);
        fputcsv($output, []); 

        fputcsv($output, ['List of Registrants']);
        fputcsv($output, ['Username', 'Email', 'Registration Date']);

        // Add data to CSV
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [$row['username'], $row['email'], $row['registration_date']]);
        }

        fclose($output);
        exit();
    } else {
        die("Event not found.");
    }
}
