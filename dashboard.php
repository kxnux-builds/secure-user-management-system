<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['delete_id']) && $_SESSION['role_id'] == 1) {
    $del_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $del_id);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | User Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <span class="text-xl font-bold text-gray-800">SysAdmin</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Hello, <span class="font-semibold text-gray-900"><?php echo htmlspecialchars($_SESSION['username']); ?></span></span>
                    <a href="profile.php" class="text-sm font-medium text-blue-600 hover:text-blue-800">Profile</a>
                    <a href="logout.php" class="text-sm font-medium text-red-600 hover:text-red-800">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <?php if ($_SESSION['role_id'] == 1): // Admin View ?>
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
                <a href="register.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    + Add New User
                </a>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-gray-700 uppercase font-semibold border-b border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-4">ID</th>
                                <th scope="col" class="px-6 py-4">Username</th>
                                <th scope="col" class="px-6 py-4">Email</th>
                                <th scope="col" class="px-6 py-4">Role</th>
                                <th scope="col" class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php
                            $sql = "SELECT u.user_id, u.username, u.email, r.role_name 
                                    FROM users u JOIN roles r ON u.role_id = r.role_id
                                    ORDER BY u.user_id DESC";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr class='hover:bg-gray-50 transition-colors'>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'>{$row['user_id']}</td>";
                                echo "<td class='px-6 py-4 font-medium text-gray-900'>".htmlspecialchars($row['username'])."</td>";
                                echo "<td class='px-6 py-4'>".htmlspecialchars($row['email'])."</td>";
                                
                                // Badge styling based on role
                                $badgeColor = $row['role_name'] == 'Admin' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800';
                                echo "<td class='px-6 py-4'><span class='px-2.5 py-1 rounded-full text-xs font-medium {$badgeColor}'>{$row['role_name']}</span></td>";
                                
                                echo "<td class='px-6 py-4 text-right whitespace-nowrap'>";
                                if ($row['user_id'] != $_SESSION['user_id']) {
                                    echo "<a href='dashboard.php?delete_id={$row['user_id']}' onclick='return confirm(\"Are you sure you want to permanently delete this user?\")' class='text-red-600 hover:text-red-900 font-medium'>Delete</a>";
                                } else {
                                    echo "<span class='text-gray-400 italic text-xs'>Current</span>";
                                }
                                echo "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php else: // Regular User View ?>
            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200 text-center max-w-2xl mx-auto mt-10">
                <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                    <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Welcome to your Dashboard</h2>
                <p class="text-gray-600 mb-6">You are logged in as a standard user. You can manage your personal settings from your profile page.</p>
                <a href="profile.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-lg transition-colors">
                    Go to Profile Settings
                </a>
            </div>
        <?php endif; ?>

    </main>
</body>
</html>