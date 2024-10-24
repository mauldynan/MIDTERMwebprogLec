<?php
session_start();
require 'config.php';

$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';

$stmt = $conn->prepare("
    SELECT u.id, u.username, u.email 
    FROM users u
    WHERE u.role = 0
");

$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    // Filter users by search keyword
    if (empty($searchKeyword) || stripos($row['username'], $searchKeyword) !== false) {
        $users[] = $row;
    }
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
    <link href="/src/output.css" rel="stylesheet">
</head>
<body class="bg-[#F0EDE3] font-serif">
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto mt-10 px-10">
        <div class="flex justify-between mb-4">
            <div>
                <a href="user_management.php" class="text-xl font-bold text-gray-500 hover:text-[#DBA7A7] text-center">Registered Users</a>
            </div>
        
            <div>
                <form method="GET" action="" >
                    <input type="text" name="search" placeholder="Search users..." value="<?php echo htmlspecialchars($searchKeyword); ?>" class="rounded-full px-4 py-1 focus:outline-none focus:ring-2 focus:ring-[#FFC700] text-black">
                    <button type="submit" class="ml-2 bg-white text-[#DBA7A7] font-semibold py-1 px-4 rounded-full hover:bg-gray-100 transition duration-300">Search</button>
                </form>
            </div>
        </div>

        <table class="min-w-full bg-white border border-gray-300 rounded-lg mt-6">
            <thead>
                <tr class="bg-gray-200 text-gray-600">
                    <th class="py-2 px-4 border text-start">Username</th>
                    <th class="py-2 px-4 border text-start">Email</th>
                    <th class="py-2 px-4 border text-start">Registered Events</th>
                    <th class="py-2 px-4 border text-start">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="py-2 px-4 border"><?php echo htmlspecialchars($user['username']); ?></td>
                            <td class="py-2 px-4 border"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="py-2 px-4 border">
                                <button class="text-blue-500" onclick="showEvents(<?php echo $user['id']; ?>)">View Events</button>
                            </td>
                            <td class="py-2 px-4 border">
                                <button class="text-red-500" onclick="openDeleteModal(<?php echo $user['id']; ?>)">Delete Account</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="py-2 px-4 border text-center">No user found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal for showing events -->
    <div id="eventModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg p-5 w-full max-w-lg mx-auto absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
            <div class="flex justify-between items-center border-b border-gray-300 pb-2 mb-4">
                <h3 class="text-xl font-bold">Registered Events</h3>
                <button onclick="closeModal()" class="text-gray-600 text-2xl font-bold">&times;</button>
            </div>
            <ul id="eventList" class="mb-4"></ul>
        </div>
    </div>

    <!-- Modal for delete confirmation -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg p-5 w-full max-w-md absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
            <h3 class="text-xl font-bold mb-4 text-center">Confirm Account Deletion</h3>
            <p class="text-center mb-6">Are you sure you want to delete this account? This action cannot be undone.</p>
            <div class="flex justify-between">
                <button onclick="closeDeleteModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                <button id="confirmDeleteBtn" class="bg-red-500 text-white px-4 py-2 rounded">Delete</button>
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
            if (window.pageYOffset > 200) { 
                scrollToTopBtn.classList.remove('hidden');
            } else {
                scrollToTopBtn.classList.add('hidden');
            }
        };
    
        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        function showEvents(userId) {
            fetch(`get_events.php?user_id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    const eventList = document.getElementById('eventList');
                    eventList.innerHTML = '';

                    if (data.length > 0) {
                        data.forEach(event => {
                            const li = document.createElement('li');
                            li.textContent = event.name;
                            eventList.appendChild(li);
                        });
                    } else {
                        const li = document.createElement('li');
                        li.textContent = 'No events registered';
                        eventList.appendChild(li);
                    }

                    document.getElementById('eventModal').classList.remove('hidden');
                });
        }

        function closeModal() {
            document.getElementById('eventModal').classList.add('hidden');
        }

        let userIdToDelete = null;

        function openDeleteModal(userId) {
            userIdToDelete = userId; 
            document.getElementById('deleteModal').classList.remove('hidden'); 
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden'); 
            userIdToDelete = null; 
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (userIdToDelete) {
                window.location.href = 'delete_user.php?id=' + userIdToDelete; 
            }
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
