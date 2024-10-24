<?php 
include 'config.php';

$updateSuccess = false;
$errorMessage = ""; 

if (isset($_GET['id'])) {
    $eventId = intval($_GET['id']);

    // Fetch the event details from the database
    $sql = "SELECT * FROM events WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if an event was found
        if ($result->num_rows > 0) {
            $event = $result->fetch_assoc();
        } else {
            echo "Event not found.";
            exit;
        }
        $stmt->close();
    }
}

// Handle form submission to update the event
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get updated event details from the form
    $name = $_POST['name'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $max_participants = $_POST['max_participants'];
    $status = $_POST['status'];

    $picture = $event['picture']; 

    // Handle picture upload
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $fileType = mime_content_type($_FILES['picture']['tmp_name']);

        // Validate file type
        if (in_array($fileType, $allowedTypes)) {
            // Generate a unique name for the uploaded file
            $fileName = uniqid() . '-' . basename($_FILES['picture']['name']);
            $targetDir = '../../upload/';  // Ensure this path is correct
            $targetFilePath = $targetDir . $fileName;

            // Move the uploaded file to the server directory
            if (move_uploaded_file($_FILES['picture']['tmp_name'], $targetFilePath)) {
                $picture = '../../upload/' . $fileName;
            } else {
                echo "Error uploading the file.";
                exit();
            }
        } else {
            echo "Invalid file type. Only JPG, JPEG, and PNG files are allowed.";
            exit();
        }
    }

    // Prepare the SQL statement to update the event
    $sql = "UPDATE events SET name=?, description=?, location=?, date=?, time=?, max_participants=?, status=?, picture=? WHERE id=?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssssissi", $name, $description, $location, $date, $time, $max_participants, $status, $picture, $eventId);
        if ($stmt->execute()) {
            $updateSuccess = true; 
            header("Location: event_management.php"); 
            exit;
        } else {
            $errorMessage = "Error updating event.";
        }
        $stmt->close();
    } else {
        $errorMessage = "Error preparing the statement.";
    }
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link href="/src/output.css" rel="stylesheet">
    
</head>
<body class="bg-[#F0EDE3] font-serif">

<?php include 'navbar.php'; ?>

<div class="container mx-auto p-12 text-gray-500">
    <h1 class="text-center text-xl font-bold mb-6">Edit Event</h1>

    <?php if ($updateSuccess): ?>
        <div class="bg-green-500 text-white p-4 rounded mb-4">Event updated successfully!</div>
    <?php elseif ($errorMessage): ?>
        <div class="bg-red-500 text-white p-4 rounded mb-4"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>

    <form method="POST" action="?id=<?php echo $eventId; ?>" enctype="multipart/form-data">
        <div class="mb-4">
            <label for="name" class="block mb-2">Event Name</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($event['name']); ?>" required class="border px-4 py-2 w-full">            
        </div>
        <div class="mb-4">
            <label for="description" class="block mb-2">Description</label>
            <textarea name="description" id="description" required class="border px-4 py-2 w-full"><?php echo htmlspecialchars($event['description']); ?></textarea>
        </div>
        
        <div class="mb-4 flex items-center">
            <div class="mr-4 w-full">
                <label for="location" class="block mb-2">Location</label>
                <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($event['location']); ?>" required class="border px-4 py-2 w-full">
            </div>
            <div class="w-full">
                <label for="max_participants" class="block mb-2">Max Participants</label>
                <input type="number" name="max_participants" id="max_participants" value="<?php echo htmlspecialchars($event['max_participants']); ?>" required class="border px-4 py-2 w-full">                
            </div>
        </div>
        
        <div class="mb-4 flex items-center">
            <div class="mr-4 w-full">
                <label for="date" class="block mb-2">Date</label>
                <input type="date" name="date" id="date" value="<?php echo htmlspecialchars($event['date']); ?>" required class="border px-4 py-2 w-full">
            </div>
            <div class="w-full">
                <label for="time" class="block mb-2">Time</label>
                <input type="time" name="time" id="time" value="<?php echo htmlspecialchars($event['time']); ?>" required class="border px-4 py-2 w-full">
            </div>
        </div>
        
        <div class="mb-4 flex items-center">
            <div class="mr-4 w-full">
                <label for="picture" class="block mb-2">Picture</label>
                <input type="file" name="picture" id="picture" accept=".jpg, .jpeg, .png" class="px-4 py-2 w-full">
            </div>
            <div class="w-full">
                <label for="status" class="block mb-2">Status</label>
                <select name="status" id="status" class="border px-4 py-2 w-full">
                    <option value="open" <?php if ($event['status'] === 'open') echo 'selected'; ?>>Open</option>
                    <option value="closed" <?php if ($event['status'] === 'closed') echo 'selected'; ?>>Closed</option>
                    <option value="canceled" <?php if ($event['status'] === 'canceled') echo 'selected'; ?>>Canceled</option>
                </select>
            </div>
        </div>
        
        <!-- Menampilkan gambar saat ini jika ada -->
        <div class="mb-4">
            <label class="block mb-2">Current Picture:</label>
            <?php if (!empty($event['picture'])): ?>
                <img src="<?php echo htmlspecialchars($event['picture']); ?>" alt="Event Picture" class="w-32 h-32 object-cover mb-4">
            <?php else: ?>
                <p>No picture available.</p>
            <?php endif; ?>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update Event</button>
        <a href="event_management.php" class="bg-gray-400 text-white px-4 py-2 rounded inline-block hover:bg-gray-600">Back</a>
    </form>
</div>
</body>
</html>
