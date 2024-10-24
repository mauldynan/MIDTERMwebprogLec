<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registered Events</title>
    <link href="/src/output.css" rel="stylesheet">
</head>
<body class="bg-[#F0EDE3] font-serif">
    <div class="container mx-auto my-8">
        <?php include 'navbar.php'; ?>
        
        <?php
        include '../config.php';

        // Ambil ID pengguna dari sesi
        $userId = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;

        $eventName = isset($_GET['event_name']) ? $_GET['event_name'] : '';

        $sql = "SELECT r.id AS registration_id, e.name, e.description, e.location, e.date, e.time, e.picture 
                FROM registrations r 
                JOIN events e ON r.event_id = e.id 
                WHERE r.user_id = ?";

        if (!empty($eventName)) {
            $sql .= " AND e.name LIKE ?";
        }

        $stmt = $conn->prepare($sql);
        if (!empty($eventName)) {
            $eventNameParam = '%' . $eventName . '%';
            $stmt->bind_param("is", $userId, $eventNameParam);
        } else {
            $stmt->bind_param("i", $userId);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();

        echo '<h2 class="text-2xl font-bold mb-4 text-center text-gray-500 cursor-pointer hover:text-[#DBA7A7]" onclick="window.location.href=\'?\'">Registered Events</h2>';
        // Pencarian Form
        echo '<form id="searchForm" action="" method="GET" class="mb-4 text-center">';
        echo '<input type="text" name="event_name" placeholder="Search events..." class="rounded-full px-4 py-1 focus:outline-none focus:ring-2 focus:ring-[#FFC700] text-black" value="' . htmlspecialchars($eventName) . '">';
        echo '<button type="submit" class="ml-2 bg-white text-[#DBA7A7] font-semibold py-1 px-4 rounded-full hover:bg-gray-100 transition duration-300">Search</button>';
        echo '</form>';

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="flex justify-center p-6 mx-10 mt-4 mb-10">
                    <div class="flex bg-[#F0EDE3] shadow-lg rounded-lg p-6 w-full">
                        <div class="w-1/2 mr-4">
                            <img src="../../upload<?php echo htmlspecialchars($row['picture']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="w-full h-auto object-cover mb-2"> 
                        </div>
                        <div class="w-1/2 text-center">
                            <h1 class="text-4xl font-bold mb-2 text-gray-500"><?php echo htmlspecialchars($row['name']); ?></h1>
                            <p class="text-base mb-6 text-gray-400"><?php echo htmlspecialchars($row['description']); ?></p>
                            <p class="text-base mb-1 text-gray-400"><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                            <p class="text-base mb-1 text-gray-400"><strong>Date & Time:</strong> <?php echo htmlspecialchars($row['date']) . ' at ' . htmlspecialchars($row['time']); ?></p>

                            <button class="mt-2 inline-block bg-red-500 text-white font-semibold py-2 px-4 rounded-full hover:bg-red-700 transition duration-300" 
                                    onclick="confirmCancel(<?php echo $row['registration_id']; ?>)">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            if (empty($eventName)) {
                echo '<p class="text-center text-gray-500">You have not registered for any events.</p>';
            } else {
                echo '<p class="text-center text-gray-500">No events found matching your search.</p>';
            }
        }

        $stmt->close();
        $conn->close();
        ?>

        <div id="success-alert" class="hidden fixed top-24 right-10">
            <div class="bg-green-100 border border-green-400 text-green-700 p-4 rounded-lg shadow-lg max-w-xs">
                <p>Registration canceled successfully!</p>
            </div>
        </div>

        <div id="blur" class="hidden fixed inset-0 bg-black opacity-50"></div>
        <div id="modal" class="fixed inset-0 flex justify-center items-center" style="display: none;">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-lg font-bold mb-4">Confirm Cancellation</h2>
                <p>Are you sure you want to cancel this registration?</p>
                <div class="flex justify-end mt-4">
                    <button id="confirm-btn" class="bg-red-500 text-white font-semibold py-2 px-4 rounded-full hover:bg-red-700 transition duration-300">Yes, Cancel</button>
                    <button id="cancel-btn" class="ml-2 bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-full hover:bg-gray-400 transition duration-300">No, Keep it</button>
                </div>
            </div>
        </div>

        <button id="scrollToTopBtn" class="hidden fixed bottom-10 right-10 bg-white text-black p-3 rounded-full shadow-lg hover:bg-blue-300">
            ↑
        </button>

        <script>
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

            let currentRegistrationId;

            function confirmCancel(registrationId) {
                currentRegistrationId = registrationId;
                document.getElementById('blur').classList.remove('hidden'); 
                document.getElementById('modal').style.display = 'flex'; 
            }

            document.getElementById('cancel-btn').onclick = function() {
                document.getElementById('blur').classList.add('hidden');
                document.getElementById('modal').style.display = 'none'; 
            };

            document.getElementById('confirm-btn').onclick = function() {
                fetch('cancel_registration.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ registration_id: currentRegistrationId })
                })
                .then(response => {
                    if (response.ok) {
                        document.getElementById('success-alert').classList.remove('hidden');

                        document.getElementById('blur').classList.add('hidden');
                        document.getElementById('modal').style.display = 'none'; 
                        
                        setTimeout(() => {
                            location.reload(); 
                        }, 1000);
                    } else {
                        console.error('Failed to cancel registration.');
                    }
                })
                .catch(error => console.error('Error:', error));
            };
        </script>
    </div>
</body>
</html>
