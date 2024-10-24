<?php
session_start();
require 'config.php';

$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

// Fetch event details from the database
if ($event_id > 0) {
    $stmt = $conn->prepare("SELECT name, picture, max_participants, status FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();

        // Query to count the number of registrations based on event_id
        $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM registrations WHERE event_id = ?");
        $countStmt->bind_param("i", $event_id);
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $countRow = $countResult->fetch_assoc();
        $totalParticipants = $countRow['total'];
    } else {
        $event = null; 
    }
} else {
    echo "Invalid event ID.";
    exit();
}

// Fetch registrations for the event
$registrations = [];
if ($event) {
    $stmt = $conn->prepare("
        SELECT u.username, u.email, r.registration_date 
        FROM users u
        JOIN registrations r ON u.id = r.user_id 
        WHERE r.event_id = ?
    ");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $registrations[] = $row; 
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $event ? htmlspecialchars($event['name']) : 'Event Not Found'; ?></title>
    <link href="/src/output.css" rel="stylesheet">
</head>
<body class="bg-[#F0EDE3] font-serif">

    <?php include 'navbar.php'; ?>

    <div class="container mx-auto px-10">
        <?php if ($event): ?>
            <div class="flex justify-between p-10">
                <div class="w-1/3 pl-4">
                    <img src="<?php echo htmlspecialchars($event['picture']); ?>" alt="Event Image" class="w-80 h-64 object-cover rounded-md mb-4">
                    <h2 class="text-2xl font-bold mb-4 text-gray-600"><?php echo htmlspecialchars($event['name']); ?></h2>
                    <p class="text-gray-500">Participants: <?php echo $totalParticipants; ?></p>
                    <p class="text-gray-500">Max Participants: <?php echo htmlspecialchars($event['max_participants']); ?></p>
                
                    <form method="post" action="export_csv.php" class="mt-4">
                        <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                        <button type="submit" class="bg-[#DBA7A7] text-white hover:bg-gray-400 py-2 px-4 rounded-full">Export to CSV</button>
                    </form>
                </div>

                <div class="w-2/3 pr-4">
                    <?php if (!empty($registrations)): ?>
                        <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600">
                                    <th class="py-2 px-4 border">Username</th>
                                    <th class="py-2 px-4 border">Email</th>
                                    <th class="py-2 px-4 border">Registration Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($registrations as $registration): ?>
                                    <tr class="hover:bg-gray-100">
                                        <td class="py-2 px-4 border"><?php echo htmlspecialchars($registration['username']); ?></td>
                                        <td class="py-2 px-4 border"><?php echo htmlspecialchars($registration['email']); ?></td>
                                        <td class="py-2 px-4 border"><?php echo htmlspecialchars($registration['registration_date']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-gray-600">No participants registered for this event.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <h2 class="text-2xl font-bold mb-4">Event Not Found</h2>
                    <p class="text-gray-600">No details available for this event.</p>
                <?php endif; ?>
                </div>            
            </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
