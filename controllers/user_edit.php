<?php
// controllers/user_edit.php - Edit user details
require_once '../config/db.php';
$error = '';
$roles = $pdo->query('SELECT * FROM roles')->fetchAll();
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: user_list.php');
    exit();
}
$id = (int)$_GET['id'];
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) {
    header('Location: user_list.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role_id = (int)$_POST['role_id'];
    if (!$username || !$email || !$role_id) {
        $error = 'All fields are required.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?');
        $stmt->execute([$username, $email, $id]);
        if ($stmt->fetch()) {
            $error = 'Username or email already exists.';
        } else {
            $update = $pdo->prepare('UPDATE users SET username = ?, email = ?, role_id = ? WHERE id = ?');
            $update->execute([$username, $email, $role_id, $id]);
            if (!empty($_POST['password'])) {
                $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $pdo->prepare('UPDATE users SET password = ? WHERE id = ?')->execute([$hash, $id]);
            }
            header('Location: user_list.php');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="../public/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="flex min-h-screen">
    <aside class="hidden md:flex flex-col w-56 bg-gray-800 text-white py-8 px-4 fixed h-full z-10">
        <h2 class="text-xl font-bold mb-8">Menu</h2>
        <ul class="space-y-4">
            <li><a href="../index.php" class="hover:text-yellow-400 font-semibold">Projects</a></li>
            <li><a href="../controllers/project_create.php" class="hover:text-yellow-400 font-semibold">Create Project</a></li>
            <li><a href="../controllers/task_list.php?project_id=1" class="hover:text-yellow-400 font-semibold">Tasks</a></li>
            <li><a href="user_list.php" class="text-blue-300 font-semibold cursor-default">Manage Users</a></li>
        </ul>
    </aside>
    <main class="flex-1 p-4 md:p-8 md:ml-56 w-full">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Edit User</h2>
        <?php if ($error): ?><p class="text-red-600 mb-4"><?= $error ?></p><?php endif; ?>
        <form method="post" class="bg-white p-6 rounded shadow max-w-md">
            <label class="block mb-2 font-semibold">Username:
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="border px-3 py-2 rounded w-full" required>
            </label>
            <label class="block mb-2 font-semibold">Email:
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="border px-3 py-2 rounded w-full" required>
            </label>
            <label class="block mb-2 font-semibold">Password (leave blank to keep current):
                <input type="password" name="password" class="border px-3 py-2 rounded w-full">
            </label>
            <label class="block mb-2 font-semibold">Role:
                <select name="role_id" class="border px-3 py-2 rounded w-full" required>
                    <option value="">Select Role</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['id'] ?>" <?= $user['role_id'] == $role['id'] ? 'selected' : '' ?>><?= htmlspecialchars($role['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mt-4">Update User</button>
        </form>
        <a href="user_list.php" class="inline-block mt-6 text-blue-700 hover:underline">&larr; Back to Users</a>
    </main>
</div>
</body>
</html>
