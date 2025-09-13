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
</head>
<body>
    <h2>Edit Task</h2>
    <?php if ($error): ?><p style="color:red;"> <?= $error ?> </p><?php endif; ?>
    <form method="post">
        <label>Title: <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" required></label><br>
        <label>Description: <textarea name="description"><?= htmlspecialchars($task['description']) ?></textarea></label><br>
        <label>Status: <input type="text" name="status" value="<?= htmlspecialchars($task['status']) ?>"></label><br>
        <label>Due Date: <input type="date" name="due_date" value="<?= $task['due_date'] ?>"></label><br>
        <label>Progress (%): <input type="number" name="progress" min="0" max="100" value="<?= $task['progress'] ?>"></label><br>
        <button type="submit">Update</button>
    </form>
    <a href="task_list.php?project_id=<?= $task['project_id'] ?>">Back to Task List</a>
</body>
</html>
