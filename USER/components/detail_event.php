<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Detail</title>
    <link href="/src/output.css" rel="stylesheet">
</head>
<body class="bg-[#F0EDE3] font-serif">
    <div class="container mx-auto my-8">
        
        <?php include 'navbar.php'; ?>

        <?php
        include '../config.php';

        // Pastikan ID acara ada dalam URL
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $eventId = $_GET['id'];

            // Ambil ID pengguna dari sesi
            $userId = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;

            // Query untuk mengambil detail acara berdasarkan ID
            $sql = "SELECT name, description, location, date, time, picture FROM events WHERE id = ? AND status = 'open'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $eventId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                // Cek apakah pengguna sudah mendaftar untuk acara ini
                $checkRegistrationSql = "SELECT * FROM registrations WHERE event_id = ? AND user_id = ?";
                $checkStmt = $conn->prepare($checkRegistrationSql);
                $checkStmt->bind_param("ii", $eventId, $userId);
                $checkStmt->execute();
                $registrationResult = $checkStmt->get_result();
                $isRegistered = $registrationResult->num_rows > 0; 

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

                            <!-- Form untuk mendaftar acara -->
                            <form action="register_event.php" method="POST" id="registerForm">
                                <input type="hidden" name="event_id" value="<?php echo $eventId; ?>"> <!-- Kirim ID acara -->
                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userId); ?>"> <!-- Kirim ID pengguna -->
                                
                                <div class="flex justify-between items-center p-4">
                                    <div>
                                        <a href="dashboard.php" class="text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="2em" height="2em" viewBox="0 0 24 24" class="text-gray-500 hover:text-gray-700">
                                                <path fill="currentColor" d="M15 18l-6-6 6-6v12z"/>
                                            </svg>
                                        </a>
                                    </div>

                                    <div>
                                        <button type="submit" class="bg-white text-gray-500 font-semibold mr-40 py-2 px-4 rounded-full hover:bg-[#DBA7A7] transition duration-300" id="registerButton" <?php echo $isRegistered ? 'disabled' : ''; ?>>
                                            <?php echo $isRegistered ? 'Already Registered' : 'Register'; ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Success Alert -->
                <div id="success-alert" class="hidden fixed top-24 right-10">
                    <div class="bg-green-100 border border-green-400 text-green-700 p-4 rounded-lg shadow-lg max-w-xs">
                        <p>Registration successful!</p>
                    </div>
                </div>

                <script>
                    const registerForm = document.getElementById('registerForm');
                    const successAlert = document.getElementById('success-alert');

                    registerForm.addEventListener('submit', function(event) {
                        event.preventDefault(); 

                        const formData = new FormData(registerForm);

                        fetch(registerForm.action, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json()) 
                        .then(data => {
                            // Check if the registration was successful
                            if (data.success) {
                                successAlert.classList.remove('hidden');
                                
                                // Change button text and disable it
                                const registerButton = document.getElementById('registerButton');
                                registerButton.textContent = 'Already Registered';
                                registerButton.disabled = true;

                                setTimeout(() => {
                                    successAlert.classList.add('hidden');
                                }, 2000);
                            } else {
                                console.error(data.message);
                            }
                        })
                        .catch(error => console.error('Error:', error)); 
                    });
                </script>

                <?php
                $checkStmt->close();
            } else {
                echo '<p class="text-center text-gray-500">Event not found or not available.</p>';
            }

            $stmt->close();
        } else {
            echo '<p class="text-center text-red-500">Invalid event ID.</p>';
        }

        $conn->close();
        ?>
    </div>
    
</body>
</html>
