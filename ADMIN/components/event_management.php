<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management</title>
    <link href="/src/output.css" rel="stylesheet">
</head>
<body class="bg-[#F0EDE3] font-serif">

    <?php 
    include 'config.php';

    if (isset($_GET['delete_id'])) {
        $eventId = intval($_GET['delete_id']); 

        $sql = "DELETE FROM events WHERE id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $eventId);
            if ($stmt->execute()) {
                $message = "Event deleted successfully.";
            } else {
                $error = "Error deleting event.";
            }
            $stmt->close();
        }
    }

    $searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';

    $statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';

    $sql = "SELECT id, name, description, location, date, time, max_participants, picture, status FROM events WHERE 1=1";
    
    if (!empty($searchKeyword)) {
        $sql .= " AND name LIKE '%" . $conn->real_escape_string($searchKeyword) . "%'";
    }

    if ($statusFilter === 'open') {
        $sql .= " AND status = 'Open'";
    } elseif ($statusFilter === 'closed') {
        $sql .= " AND status = 'Closed'";
    } elseif ($statusFilter === 'canceled') {
        $sql .= " AND status = 'Canceled'";
    }

    $result = $conn->query($sql);
    ?>

    <?php include 'navbar.php'; ?>
    
    <div class="container mx-auto mb-14 mt-2 p-10 text-gray-500">
        <h1 class="text-center text-xl font-bold mb-6">
            <a href="?search=" class="text-gray-500 hover:text-[#DBA7A7] ">Event Management</a>
        </h1>
        
        <div class="mb-4 text-xs flex justify-between items-center">
            <!-- Add New Event Button -->
            <a href="add_event.php" class="bg-white text-gray-400 px-10 py-2 rounded hover:text-blue-600">Add New Event</a>
    
            <!-- Search Bar -->
            <form method="GET" action="" class="flex items-center">
                <input type="text" name="search" placeholder="Search events..." value="<?php echo htmlspecialchars($searchKeyword); ?>" class="rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#FFC700] text-black" required>
                <button type="submit" class="ml-2 bg-white text-[#DBA7A7] font-semibold py-2 px-4 rounded-full hover:bg-gray-100 transition duration-300">Search</button>
            </form>
        </div>


        <div id="alertMessage" class="hidden fixed z-50 top-5 right-5 bg-green-500 text-white p-4 rounded">
            <span id="alertText"></span>
            <span id="alertClose" style="cursor-pointer ml-4">&times;</span>
        </div>

        <table class="w-full border-collapse border border-gray-200">
            <thead>
                <tr class="bg-white text-gray-400 text-xs">
                    <th class="border p-2">Name</th>
                    <th class="border p-2">Description</th>
                    <th class="border p-2">Location</th>
                    <th class="border p-2">Date & Time</th>
                    <th class="border p-2">Participants</th>
                    <th class="border p-2">Picture</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody id="eventTableBody" class="bg-gray-100">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $dateTime = $row['date'] . ' ' . $row['time']; 
                        $description = htmlspecialchars(substr($row['description'], 0, 100));
                        if (strlen($row['description']) > 100) {
                            $description .= '...';
                        }
                        echo "<tr>
                                <td class='border p-2 text-xs text-center'>" . htmlspecialchars($row['name']) . "</td>
                                <td class='border p-2 text-xs'>{$description}</td>
                                <td class='border p-2 text-xs text-center'>" . htmlspecialchars($row['location']) . "</td>
                                <td class='border p-2 text-xs text-center'>{$dateTime}</td>
                                <td class='border p-2 text-xs text-center'>" . htmlspecialchars($row['max_participants']) . "</td>
                                <td class='border p-2 text-xs text-center'><img src='" . htmlspecialchars($row['picture']) . "' alt='" . htmlspecialchars($row['name']) . "' class='w-16 h-16 object-cover'></td>
                                <td class='border p-2 text-xs text-center'>" . htmlspecialchars($row['status']) . "</td>
                                <td class='border p-2 text-center'>
                                    <div class='flex justify-center space-x-2'>
                                        <a href='edit_event.php?id=" . $row['id'] . "' class='bg-blue-500 text-white px-2 py-1 rounded text-xs'>Edit</a>
                                        <button class='bg-red-500 text-white px-2 py-1 rounded text-xs' onclick='openModal(" . $row['id'] . ")'>Delete</button>
                                    </div>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='border p-2 text-center'>No events found.</td></tr>";
                }
                $conn->close(); 
                ?>
            </tbody>
        </table>
    </div>


    <!-- Modal -->
    <div id="deleteModal" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.5);">
        <div class="modal-content rounded-xl" style="background-color:#fefefe; margin:15% auto; padding:20px; border:1px solid #888; width:80%; max-width:400px;">
            <span class="close" onclick="closeModal()" style="float:right; cursor:pointer;">&times;</span>
            <h2 class="text-sm font-bold mb-4">Are you sure you want to delete this event?</h2>
            <div class="flex justify-center">
                <button id="confirmDelete" class="bg-red-500 text-white px-4 py-1 rounded">Yes</button>
            </div>
        </div>
    </div>

    <!-- Scroll to Top Button -->
    <button id="scrollToTopBtn" class="hidden fixed bottom-10 right-10 bg-white text-black p-3 rounded-full shadow-lg hover:bg-blue-300">
        ↑
    </button>

    <script>
        function scrollToSection(sectionId) {
            document.getElementById(sectionId).scrollIntoView({ behavior: 'smooth' });
        }

        const scrollToTopBtn = document.getElementById('scrollToTopBtn');
        window.onscroll = function() {
            if (window.pageYOffset > 100) { 
                scrollToTopBtn.classList.remove('hidden');
            } else {
                scrollToTopBtn.classList.add('hidden');
            }
        };

        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        let deleteEventId = null;

        function openModal(eventId) {
            deleteEventId = eventId;
            document.getElementById("deleteModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("deleteModal").style.display = "none";
        }

            document.getElementById("confirmDelete").addEventListener("click", function() {
                if (deleteEventId !== null) {
                    document.getElementById("alertText").innerText = "Event deleted successfully.";
                    document.getElementById("alertMessage").style.display = "block";

                    closeModal();

                    setTimeout(function() {
                        window.location.href = "?delete_id=" + deleteEventId;
                    }, 1500); 
                }
        });


        document.getElementById("alertClose").addEventListener("click", function() {
            document.getElementById("alertMessage").style.display = "none";
        });

        window.onclick = function(event) {
            const modal = document.getElementById("deleteModal");
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
