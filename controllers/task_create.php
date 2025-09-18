<?php
// controllers/task_create.php - Create Task under a Project
require_once '../config/db.php';
require_once '../models/Task.php';
require_once '../models/User.php';
if (!isset($_GET['project_id'])) {
    header('Location: ../index.php');
    exit();
}
$project_id = (int)$_GET['project_id'];
$error = '';
$users = $pdo->query('SELECT id, username FROM users')->fetchAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];
    $due_date = $_POST['due_date'];
    $agreed_date = $_POST['agreed_date'];
    $actual_date = $_POST['actual_date'];
    $progress = (int)$_POST['progress'];
    $notes = $_POST['notes'];
    $assignees = isset($_POST['assignees']) ? $_POST['assignees'] : [];
    if (!$title) {
        $error = 'Task title is required.';
    } else {
        createTask([
            'project_id' => $project_id,
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'due_date' => $due_date,
            'agreed_date' => $agreed_date,
            'actual_date' => $actual_date,
            'progress' => $progress,
            'notes' => $notes,
            'assignees' => $assignees
        ]);
        header('Location: ../controllers/task_list.php?project_id=' . $project_id);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Task</title>
    <link rel="stylesheet" href="../public/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="bg-gray-50">
<div class="flex min-h-screen">
    <?php $active = 'tasks'; include '../views/sidebar.php'; ?>
    <main class="flex-1 p-4 md:p-8 md:ml-56 w-full">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Create Task</h2>
            <a href="task_list.php?project_id=<?= $project_id ?>" class="text-blue-700 hover:underline">Back to Task List</a>
        </div>
        <?php if ($error): ?><p class="mb-4 text-red-600"><?= $error ?></p><?php endif; ?>
        <form method="post" class="bg-white p-6 rounded-lg shadow max-w-2xl">
            <label class="block mb-3">
                <span class="block text-sm font-medium text-gray-700 mb-1">Title</span>
                <input type="text" name="title" required class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
            </label>
            <label class="block mb-3">
                <span class="block text-sm font-medium text-gray-700 mb-1">Description</span>
                <textarea name="description" rows="3" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="block">
                    <span class="block text-sm font-medium text-gray-700 mb-1">Status</span>
                    <input type="text" name="status" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                </label>
                <label class="block">
                    <span class="block text-sm font-medium text-gray-700 mb-1">Due Date</span>
                    <input type="date" name="due_date" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                </label>
                <label class="block">
                    <span class="block text-sm font-medium text-gray-700 mb-1">Agreed Date of Completion</span>
                    <input type="date" name="agreed_date" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                </label>
                <label class="block">
                    <span class="block text-sm font-medium text-gray-700 mb-1">Actual Date of Completion</span>
                    <input type="date" name="actual_date" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                </label>
                <label class="block">
                    <span class="block text-sm font-medium text-gray-700 mb-1">Progress (%)</span>
                    <input type="number" name="progress" min="0" max="100" value="0" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                </label>
            </div>
            <label class="block mt-3">
                <span class="block text-sm font-medium text-gray-700 mb-1">Notes</span>
                <textarea name="notes" rows="3" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </label>
            <label class="block mt-3">
                <span class="block text-sm font-medium text-gray-700 mb-1">Assign to (Ctrl/Cmd for multiple)</span>
                <select name="assignees[]" multiple size="4" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <div class="mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow">Create</button>
            </div>
        </form>
    </main>
</div>
</body>
</html>
