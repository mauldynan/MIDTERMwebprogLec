<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="/src/output.css" rel="stylesheet">
</head>
<body class="bg-[#F0EDE3] font-serif">
    <div class="container mx-auto my-8">
        <?php 
        include 'navbar.php'; 
        ?>

        <div class="flex justify-between mx-10">
            <h2 class="text-2xl font-bold text-gray-500 mb-4">
                <a href="?action=all" class="text-gray-500 hover:text-[#DBA7A7]">Available Events</a>
            </h2>

            <!-- Kolom Pencarian -->
            <form id="searchForm" action="" method="GET">
                <input type="text" name="event_name" placeholder="Search events..." class="rounded-full px-4 py-1 focus:outline-none focus:ring-2 focus:ring-[#FFC700] text-black" required>
                <button type="submit" class="ml-2 bg-white text-[#DBA7A7] font-semibold py-1 px-4 rounded-full hover:bg-gray-100 transition duration-300">Search</button>
            </form>
        </div>

        <main class="mt-8 mx-10 mb-8">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <?php
                    include '../config.php'; 

                    // Simpan ID pengguna ke dalam variabel
                    $userId = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;

                    $eventName = isset($_GET['event_name']) ? $_GET['event_name'] : '';

                    $sql = "SELECT id, name, description, location, date, time, picture FROM events WHERE status = 'open'";
                    
                    // Cek apakah pengguna mencari event
                    if (!empty($eventName)) {
                        $sql .= " AND name LIKE ?";
                        $eventName = '%' . $conn->real_escape_string($eventName) . '%';
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $eventName);
                    } else {
                        // Jika tidak ada pencarian, ambil semua event dengan status "open"
                        $stmt = $conn->prepare($sql);
                    }

                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result === false) {
                        echo '<p class="text-center text-red-500">Error fetching events: ' . htmlspecialchars($conn->error) . '</p>';
                    } elseif ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<a href="detail_event.php?id=' . htmlspecialchars($row['id']) . '" class="text-gray-400 bg-white rounded-lg shadow-md overflow-hidden transition-transform transform hover:scale-105 block">';
                            echo '<img src="../../upload' . htmlspecialchars($row['picture']) . '" alt="' . htmlspecialchars($row['name']) . '" class="w-full h-48 object-cover">'; 
                            echo '<h2 class="text-sm font-semibold mb-2 text-center mt-2">' . htmlspecialchars($row['name']) . '</h2>';
                            echo '</a>';
                        }
                    } else {
                        echo '<p class="text-center text-gray-500">No events found.</p>';
                    }

                    $stmt->close();
                    $conn->close();
                ?>
            </div>
        </main>

        <button id="scrollToTopBtn" class="hidden fixed bottom-10 right-10 bg-white text-black p-3 rounded-full shadow-lg hover:bg-blue-300">
            ↑
        </button>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const eventBanners = document.querySelectorAll('img'); 
                eventBanners.forEach(function(banner) {
                    const src = banner.getAttribute('src');
                    if (src && src !== '') {
                        banner.style.display = 'block'; 
                    } else {
                        banner.style.display = 'none'; 
                    }
                });
            });

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
        </script>
    </div>
</body>
</html>
