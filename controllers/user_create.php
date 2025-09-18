<?php
// controllers/user_create.php - Add new user
require_once '../config/db.php';
$error = '';
$roles = $pdo->query('SELECT * FROM roles')->fetchAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role_id = (int)$_POST['role_id'];
    if (!$username || !$email || !$password || !$role_id) {
        $error = 'All fields are required.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = 'Username or email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)');
            $stmt->execute([$username, $email, $hash, $role_id]);
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
    <title>Add User</title>
    <link rel="stylesheet" href="../public/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="flex min-h-screen">
    <?php $active = 'users'; include '../views/sidebar.php'; ?>
    <main class="flex-1 p-4 md:p-8 md:ml-56 w-full">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Add User</h2>
        <?php if ($error): ?><p class="text-red-600 mb-4"><?= $error ?></p><?php endif; ?>
        <form method="post" class="bg-white p-6 rounded shadow max-w-md">
            <label class="block mb-2 font-semibold">Username:
                <input type="text" name="username" class="border px-3 py-2 rounded w-full" required>
            </label>
            <label class="block mb-2 font-semibold">Email:
                <input type="email" name="email" class="border px-3 py-2 rounded w-full" required>
            </label>
            <label class="block mb-2 font-semibold">Password:
                <input type="password" name="password" class="border px-3 py-2 rounded w-full" required>
            </label>
            <label class="block mb-2 font-semibold">Role:
                <select name="role_id" class="border px-3 py-2 rounded w-full" required>
                    <option value="">Select Role</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mt-4">Add User</button>
        </form>
        <a href="user_list.php" class="inline-block mt-6 text-blue-700 hover:underline">&larr; Back to Users</a>
    </main>
</div>
</body>
</html>
