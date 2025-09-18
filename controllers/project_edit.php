<?php
// controllers/project_edit.php - Edit Project
session_start();
require_once '../config/db.php';
if (!isset($_GET['id'])) {
    header('Location: ../index.php');
    exit();
}
$id = (int)$_GET['id'];
$error = '';
$stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
$stmt->execute([$id]);
$project = $stmt->fetch();
if (!$project) {
    header('Location: ../index.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $priority = $_POST['priority'];
    if (!$name) {
        $error = 'Project name is required.';
    } else {
        $stmt = $pdo->prepare('UPDATE projects SET name=?, description=?, start_date=?, end_date=?, status=?, priority=? WHERE id=?');
        $stmt->execute([$name, $description, $start_date, $end_date, $status, $priority, $id]);
        header('Location: ../index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Project</title>
    <link rel="stylesheet" href="../public/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="bg-gray-50">
<div class="flex min-h-screen">
    <?php $active = 'projects'; include '../views/sidebar.php'; ?>
    <main class="flex-1 p-4 md:p-8 md:ml-56 w-full">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Edit Project</h2>
            <a href="../index.php" class="text-blue-700 hover:underline">Back to Dashboard</a>
        </div>
        <?php if ($error): ?><p class="mb-4 text-red-600"> <?= $error ?> </p><?php endif; ?>
        <form method="post" class="bg-white p-6 rounded-lg shadow max-w-3xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="block">
                    <span class="block text-sm font-medium text-gray-700 mb-1">Name</span>
                    <input type="text" name="name" value="<?= htmlspecialchars($project['name']) ?>" required class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                </label>
                <label class="block md:col-span-2">
                    <span class="block text-sm font-medium text-gray-700 mb-1">Description</span>
                    <textarea name="description" rows="3" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($project['description']) ?></textarea>
                </label>
                <label class="block">
                    <span class="block text-sm font-medium text-gray-700 mb-1">Start Date</span>
                    <input type="date" name="start_date" value="<?= $project['start_date'] ?>" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                </label>
                <label class="block">
                    <span class="block text-sm font-medium text-gray-700 mb-1">End Date</span>
                    <input type="date" name="end_date" value="<?= $project['end_date'] ?>" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                </label>
                <label class="block">
                    <span class="block text-sm font-medium text-gray-700 mb-1">Status</span>
                    <input type="text" name="status" value="<?= htmlspecialchars($project['status']) ?>" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                </label>
                <label class="block">
                    <span class="block text-sm font-medium text-gray-700 mb-1">Priority</span>
                    <input type="text" name="priority" value="<?= htmlspecialchars($project['priority']) ?>" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                </label>
            </div>
            <div class="mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow">Update</button>
            </div>
        </form>
    </main>
</div>
</body>
</html>
