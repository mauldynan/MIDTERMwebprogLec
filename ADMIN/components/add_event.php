<?php
include 'config.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data from the form submission
    $name = $_POST['name'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $max_participants = $_POST['max_participants'];
    $status = $_POST['status'];

    // Handle picture upload
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $fileType = mime_content_type($_FILES['picture']['tmp_name']);

        // Validate file type
        if (in_array($fileType, $allowedTypes)) {
            $fileName = uniqid() . '-' . basename($_FILES['picture']['name']);
            $targetDir = '../../upload/';  
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
    } else {
        $picture = '';
    }

    $query = "INSERT INTO events (name, description, location, date, time, max_participants, picture, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$name, $description, $location, $date, $time, $max_participants, $picture, $status]);

    header('Location: event_management.php');
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Event</title>
    <link href="/src/output.css" rel="stylesheet">
</head>
<body class="bg-[#F0EDE3] font-serif">

    <?php include 'navbar.php'; ?>

    <div class="container mx-auto p-12 text-gray-500">
        <h1 class="text-center text-xl font-bold mb-6">Add New Event</h1>
        
        <form method="POST" action="add_event.php" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="name" class="block mb-2">Event Name</label>
                <input type="text" name="name" id="name" required class="border px-4 py-2 w-full">
            </div>
            <div class="mb-4">
                <label for="description" class="block mb-2">Description</label>
                <textarea name="description" id="description" required class="border px-4 py-2 w-full"></textarea>
            </div>

            <div class="mb-4 flex items-center">
                <div class="mr-4 w-full">
                    <label for="location" class="block mb-2">Location</label>
                    <input type="text" name="location" id="location" required class="border px-4 py-2 w-full">
                </div>
                <div class="w-full">
                    <label for="max_participants" class="block mb-2">Max Participants</label>
                    <input type="number" name="max_participants" id="max_participants" required class="border px-4 py-2 w-full">
                </div>
            </div>

            <div class="mb-4 flex items-center">
                <div class="mr-4 w-full">
                    <label for="date" class="block mb-2">Date</label>
                    <input type="date" name="date" id="date" required class="border px-4 py-2 w-full">
                </div>
                <div class="w-full">
                    <label for="time" class="block mb-2">Time</label>
                    <input type="time" name="time" id="time" required class="border px-4 py-2 w-full">
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
                        <option value="open">Open</option>
                        <option value="closed">Closed</option>
                        <option value="canceled">Canceled</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-950">Add Event</button>
            <a href="event_management.php" class="bg-gray-400 text-white px-4 py-2 rounded mt-4 inline-block hover:bg-gray-600">Back</a>
        </form>
    </div>
</body>
</html>
