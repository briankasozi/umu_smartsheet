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
</head>
<body>
    <h2>Create Task</h2>
    <?php if ($error): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>
    <form method="post">
        <label>Title: <input type="text" name="title" required></label><br>
        <label>Description: <textarea name="description"></textarea></label><br>
        <label>Status: <input type="text" name="status"></label><br>
        <label>Due Date: <input type="date" name="due_date"></label><br>
        <label>Agreed Date of Completion: <input type="date" name="agreed_date"></label><br>
        <label>Actual Date of Completion: <input type="date" name="actual_date"></label><br>
        <label>Progress (%): <input type="number" name="progress" min="0" max="100" value="0"></label><br>
        <label>Notes: <textarea name="notes"></textarea></label><br>
        <label>Assign to (hold Ctrl for multiple):
            <select name="assignees[]" multiple size="4">
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <button type="submit">Create</button>
    </form>
    <a href="task_list.php?project_id=<?= $project_id ?>">Back to Task List</a>
</body>
</html>
