<?php
session_start();
include '../config.php';

// Check if form data is received
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the event ID and user ID from the form
    $eventId = isset($_POST['event_id']) ? $_POST['event_id'] : null;
    $userId = isset($_POST['user_id']) ? $_POST['user_id'] : null;

    if ($eventId && $userId) {
        // Insert registration into the registrations table
        $insertRegistrationSql = "INSERT INTO registrations (event_id, user_id, registration_date) VALUES (?, ?, NOW())"; // Add registration_date
        $registrationStmt = $conn->prepare($insertRegistrationSql);
        $registrationStmt->bind_param("ii", $eventId, $userId);

        if ($registrationStmt->execute()) {
            // Registration successful, now insert into the history table
            $insertHistorySql = "INSERT INTO history (event_id, user_id) VALUES (?, ?)";
            $historyStmt = $conn->prepare($insertHistorySql);
            $historyStmt->bind_param("ii", $eventId, $userId);

            if ($historyStmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Registration successful!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to record history.']);
            }
            $historyStmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed.']);
        }
        $registrationStmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid event or user ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
?>
