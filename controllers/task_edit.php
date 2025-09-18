<?php
// controllers/task_edit.php - Edit Task
require_once '../config/db.php';
require_once '../models/Task.php';
if (!isset($_GET['id'])) {
    header('Location: ../index.php');
    exit();
}
$id = (int)$_GET['id'];
$task = getTaskById($id);
if (!$task) {
    header('Location: ../index.php');
    exit();
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];
    $due_date = $_POST['due_date'];
    $progress = (int)$_POST['progress'];
    if (!$title) {
        $error = 'Task title is required.';
    } else {
        updateTask($id, [
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'due_date' => $due_date,
            'progress' => $progress
        ]);
        header('Location: task_list.php?project_id=' . $task['project_id']);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Task</title>
    <link rel="stylesheet" href="../public/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="bg-gray-50">
<div class="flex min-h-screen">
    <?php $active = 'tasks'; include '../views/sidebar.php'; ?>
    <main class="flex-1 p-4 md:p-8 md:ml-56 w-full">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Edit Task</h2>
            <a href="task_list.php?project_id=<?= $task['project_id'] ?>" class="text-blue-700 hover:underline">Back to Task List</a>
        </div>
        <?php if ($error): ?><p class="mb-4 text-red-600"> <?= $error ?> </p><?php endif; ?>
        <form method="post" class="bg-white p-6 rounded-lg shadow max-w-2xl">
            <label class="block mb-3">
                <span class="block text-sm font-medium text-gray-700 mb-1">Title</span>
                <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" required class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
            </label>
            <label class="block mb-3">
                <span class="block text-sm font-medium text-gray-700 mb-1">Description</span>
                <textarea name="description" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($task['description']) ?></textarea>
            </label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <label class="block">
                    <span class="block text-sm font-medium text-gray-700 mb-1">Status</span>
                    <input type="text" name="status" value="<?= htmlspecialchars($task['status']) ?>" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                </label>
                <label class="block">
                    <span class="block text-sm font-medium text-gray-700 mb-1">Due Date</span>
                    <input type="date" name="due_date" value="<?= $task's['due_date'] ?? $task['due_date'] ?>" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                </label>
                <label class="block">
                    <span class="block text-sm font-medium text-gray-700 mb-1">Progress (%)</span>
                    <input type="number" name="progress" min="0" max="100" value="<?= $task['progress'] ?>" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
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
