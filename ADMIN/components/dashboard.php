<?php
session_start();
require 'config.php';

$sql = "SELECT id, name, picture, max_participants, status FROM events";
$result = $conn->query($sql);

$openEvents = [];
$closedEvents = [];
$canceledEvents = [];
$allEvents = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $event_id = $row['id'];
        $stmt = $conn->prepare("
            SELECT COUNT(user_id) as total_participants 
            FROM registrations 
            WHERE event_id = ?
        ");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $registration_result = $stmt->get_result();
        $registration = $registration_result->fetch_assoc();
        
        $row['total_participants'] = isset($registration['total_participants']) ? $registration['total_participants'] : 0;

        $allEvents[] = $row;

        if ($row['status'] == 'open') {
            $openEvents[] = $row;
        } elseif ($row['status'] == 'closed') {
            $closedEvents[] = $row;
        } elseif ($row['status'] == 'canceled') {
            $canceledEvents[] = $row;
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="/src/output.css" rel="stylesheet">
</head>
<body class="bg-[#F0EDE3] font-serif">

    <?php include 'navbar.php'; ?>
    
    <div class="flex justify-between mt-14 mx-12">
        <div>
            <button onclick="scrollToSection('all')" class="text-2xl font-bold text-gray-500 hover:text-[#DBA7A7]">All Events</button>
        </div>

        <!-- Filter Buttons -->
        <div class="text-xs">
            <button onclick="scrollToSection('open')" class="bg-white hover:text-blue-600 text-gray-400 py-1 px-4 rounded mr-2">
                Open
            </button>
            <button onclick="scrollToSection('closed')" class="bg-white hover:text-blue-600 text-gray-400 py-1 px-4 rounded mr-2">
                Closed
            </button>
            <button onclick="scrollToSection('canceled')" class="bg-white hover:text-blue-600 text-gray-400 py-1 px-4 rounded">
                Canceled
            </button>
        </div>
    </div>
    

    <section id="all" class="p-10">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            <?php foreach ($allEvents as $event): ?>
                <a href="event_registration.php?event_id=<?php echo urlencode($event['id']); ?>" class="block">
                    <div class="bg-white rounded-lg shadow-md p-4 transition-transform transform hover:scale-105">
                        <img src="<?php echo htmlspecialchars($event['picture']); ?>" alt="Event Image" class="w-full h-48 object-cover rounded-md">
                        <h3 class="mt-4 text-lg font-bold text-gray-500"><?php echo htmlspecialchars($event['name']); ?></h3>
                        <p class="text-gray-400">
                            <?php echo $event['total_participants'] . "/" . htmlspecialchars($event['max_participants']); ?> Participants
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>


    <!-- Open Events Section -->
    <section id="open" class="p-10">
        <h2 class="text-2xl font-bold text-gray-500 mb-4">Open Events</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            <?php foreach ($openEvents as $event): ?>
                <a href="event_registration.php?event_id=<?php echo urlencode($event['id']); ?>" class="block">
                    <div class="bg-white rounded-lg shadow-md p-4 transition-transform transform hover:scale-105">
                        <img src="<?php echo htmlspecialchars($event['picture']); ?>" alt="Event Image" class="w-full h-48 object-cover rounded-md">
                        <h3 class="mt-4 text-lg font-bold text-gray-500"><?php echo htmlspecialchars($event['name']); ?></h3>
                        <p class="text-gray-400">
                            <?php echo $event['total_participants'] . "/" . htmlspecialchars($event['max_participants']); ?> Participants
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Closed Events Section -->
    <section id="closed" class="p-10">
        <h2 class="text-2xl font-bold text-gray-500 mb-4">Closed Events</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            <?php foreach ($closedEvents as $event): ?>
                <a href="event_registration.php?event_id=<?php echo urlencode($event['id']); ?>" class="block">
                    <div class="bg-white rounded-lg shadow-md p-4 transition-transform transform hover:scale-105">
                        <img src="<?php echo htmlspecialchars($event['picture']); ?>" alt="Event Image" class="w-full h-48 object-cover rounded-md">
                        <h3 class="mt-4 text-lg font-bold text-gray-500"><?php echo htmlspecialchars($event['name']); ?></h3>
                        <p class="text-gray-400">
                            <?php echo $event['total_participants'] . "/" . htmlspecialchars($event['max_participants']); ?> Participants
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Canceled Events Section -->
    <section id="canceled" class="p-10">
        <h2 class="text-2xl font-bold text-gray-500 mb-4">Canceled Events</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            <?php foreach ($canceledEvents as $event): ?>
                <a href="event_registration.php?event_id=<?php echo urlencode($event['id']); ?>" class="block">
                    <div class="bg-white rounded-lg shadow-md p-4 transition-transform transform hover:scale-105">
                        <img src="<?php echo htmlspecialchars($event['picture']); ?>" alt="Event Image" class="w-full h-48 object-cover rounded-md">
                        <h3 class="mt-4 text-lg font-bold text-gray-500"><?php echo htmlspecialchars($event['name']); ?></h3>
                        <p class="text-gray-400">
                            <?php echo $event['total_participants'] . "/" . htmlspecialchars($event['max_participants']); ?> Participants
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Scroll to Top Button -->
    <button id="scrollToTopBtn" class="hidden fixed bottom-10 right-10 bg-white text-black p-3 rounded-full shadow-lg hover:bg-blue-300">
        ↑
    </button>

    <script>
        // Function to scroll to specific section
        function scrollToSection(sectionId) {
            document.getElementById(sectionId).scrollIntoView({ behavior: 'smooth' });
        }

        // Scroll to top function
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
    </script>

</body>
</html>
