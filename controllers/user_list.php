<?php
// controllers/user_list.php - List and manage users
require_once '../config/db.php';
$stmt = $pdo->query('SELECT users.*, roles.name AS role_name FROM users LEFT JOIN roles ON users.role_id = roles.id ORDER BY users.id ASC');
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../public/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <?php $active = 'users'; include '../views/sidebar.php'; ?>
    <!-- Main Content -->
    <main class="flex-1 p-4 md:p-8 md:ml-56 w-full">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Users</h2>
            <a href="user_create.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">Add User</a>
        </div>
        <div class="overflow-x-auto rounded-lg shadow">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border-b text-left">#</th>
                <th class="px-4 py-2 border-b text-left">Username</th>
                <th class="px-4 py-2 border-b text-left">Email</th>
                <th class="px-4 py-2 border-b text-left">Role</th>
                <th class="px-4 py-2 border-b text-left">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php $num = 1; foreach ($users as $user): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 border-b text-gray-700 font-semibold"><?= $num++ ?></td>
                <td class="px-4 py-2 border-b text-gray-700"><?= htmlspecialchars($user['username']) ?></td>
                <td class="px-4 py-2 border-b text-gray-700"><?= htmlspecialchars($user['email']) ?></td>
                <td class="px-4 py-2 border-b text-gray-700"><?= htmlspecialchars($user['role_name']) ?></td>
                <td class="px-4 py-2 border-b">
                    <a href="user_edit.php?id=<?= $user['id'] ?>" class="text-blue-600 hover:underline mr-2">Edit</a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <a href="../index.php" class="inline-block mt-6 text-blue-700 hover:underline">&larr; Back to Dashboard</a>
    </main>
</div>
</body>
</html>
