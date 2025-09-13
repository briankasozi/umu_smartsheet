<?php
// controllers/project_create.php - Create Project
session_start();
require_once '../config/db.php';
$error = '';
require_once '../models/Task.php';
require_once '../models/User.php';
// Fetch all users for assignment
$all_users = getAllUsers();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];
    $priority = $_POST['priority'];
    $tasks = isset($_POST['tasks']) ? $_POST['tasks'] : [];
    if (!$name) {
        $error = 'Project name is required.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO projects (name, description, start_date, end_date, status, priority) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $description, $start_date, $end_date, $status, $priority]);
        $project_id = $pdo->lastInsertId();
        // Insert tasks if provided
        foreach ($tasks as $task) {
            if (!empty($task['title'])) {
                $assignees = isset($task['assignees']) ? $task['assignees'] : [];
                createTask([
                    'project_id' => $project_id,
                    'title' => $task['title'],
                    'description' => $task['description'],
                    'status' => $task['status'],
                    'due_date' => $task['due_date'],
                    'progress' => $task['progress'],
                    'agreed_date' => isset($task['agreed_date']) ? $task['agreed_date'] : null,
                    'actual_date' => isset($task['actual_date']) ? $task['actual_date'] : null,
                    'notes' => isset($task['notes']) ? $task['notes'] : null,
                    'assignees' => $assignees
                ]);
            }
        }
        header('Location: ../index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Project</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>
    <h2>Create Project</h2>
    <?php if ($error): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>
    <form method="post" id="projectForm">
        <label>Name: <input type="text" name="name" required></label><br>
        <label>Description: <textarea name="description"></textarea></label><br>
        <label>Start Date: <input type="date" name="start_date"></label><br>
        <label>End Date: <input type="date" name="end_date"></label><br>
        <label>Status: <input type="text" name="status"></label><br>
        <label>Priority: <input type="text" name="priority"></label><br>
        <hr>
        <h3>Assign Tasks</h3>
        <div id="tasksContainer">
            <div class="task-block">
                <label>Title: <input type="text" name="tasks[0][title]"></label><br>
                <label>Description: <input type="text" name="tasks[0][description]"></label><br>
                <label>Status: <input type="text" name="tasks[0][status]"></label><br>
                <label>Due Date: <input type="date" name="tasks[0][due_date]"></label><br>
                <label>Progress (%): <input type="number" name="tasks[0][progress]" min="0" max="100" value="0"></label><br>
                <label>Agreed Date: <input type="date" name="tasks[0][agreed_date]"></label><br>
                <label>Actual Date: <input type="date" name="tasks[0][actual_date]"></label><br>
                <label>Notes: <input type="text" name="tasks[0][notes]"></label><br>
                <label>Assign To:<br>
                    <select name="tasks[0][assignees][]" multiple size="3">
                        <?php foreach ($all_users as $user): ?>
                            <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['role_name']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </label><br>
            </div>
        </div>
        <button type="button" onclick="addTaskBlock()">Add Another Task</button><br><br>
        <button type="submit">Create</button>
    </form>
    <script>
    let taskIndex = 1;
    function addTaskBlock() {
        const container = document.getElementById('tasksContainer');
        const block = document.createElement('div');
        block.className = 'task-block';
        let userOptions = `<?php foreach ($all_users as $user): ?>` +
            `<option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['role_name']) ?>)</option>` +
        `<?php endforeach; ?>`;
        block.innerHTML = `
            <label>Title: <input type="text" name="tasks[${taskIndex}][title]"></label><br>
            <label>Description: <input type="text" name="tasks[${taskIndex}][description]"></label><br>
            <label>Status: <input type="text" name="tasks[${taskIndex}][status]"></label><br>
            <label>Due Date: <input type="date" name="tasks[${taskIndex}][due_date]"></label><br>
            <label>Progress (%): <input type="number" name="tasks[${taskIndex}][progress]" min="0" max="100" value="0"></label><br>
            <label>Agreed Date: <input type="date" name="tasks[${taskIndex}][agreed_date]"></label><br>
            <label>Actual Date: <input type="date" name="tasks[${taskIndex}][actual_date]"></label><br>
            <label>Notes: <input type="text" name="tasks[${taskIndex}][notes]"></label><br>
            <label>Assign To:<br>
                <select name="tasks[${taskIndex}][assignees][]" multiple size="3">${userOptions}</select>
            </label><br>
            <button type="button" onclick="this.parentNode.remove()">Remove Task</button><br><br>
        `;
        container.appendChild(block);
        taskIndex++;
    }
    </script>
    <a href="../index.php">Back to Dashboard</a>
</body>
</html>
