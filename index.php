<?php
// index.php - Dashboard/Landing Page
require_once 'config/db.php';
// Fetch projects for dashboard
$stmt = $pdo->query('SELECT * FROM projects ORDER BY created_at DESC');
$projects = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TagDev2.0 - Dashboard</title>
    <link rel="stylesheet" href="public/style.css">
</head>
<body>
<div class="sidebar">
    <h2>Menu</h2>
    <ul>
        <li><a href="index.php">Projects</a></li>
        <li><a href="controllers/project_create.php">Create Project</a></li>
        <li><a href="#" onclick="alert('Select a project to view tasks.');return false;">Tasks</a></li>
    </ul>
</div>
<div class="main-content">
    <h1>Welcome to TagDev2.0 Project Management</h1>
    <h2>Projects</h2>
    <table border="1">
        <tr>
            <th>Name</th><th>Description</th><th>Status</th><th>Priority</th><th>Actions</th><th>Tasks</th>
        </tr>
        <?php foreach ($projects as $project): ?>
        <tr>
            <td><?= htmlspecialchars($project['name']) ?></td>
            <td><?= htmlspecialchars($project['description']) ?></td>
            <td><?= htmlspecialchars($project['status']) ?></td>
            <td><?= htmlspecialchars($project['priority']) ?></td>
            <td>
                <a href="controllers/project_edit.php?id=<?= $project['id'] ?>">Edit</a> |
                <a href="controllers/project_delete.php?id=<?= $project['id'] ?>" onclick="return confirm('Delete this project?')">Delete</a>
            </td>
            <td>
                <a href="controllers/task_list.php?project_id=<?= $project['id'] ?>">View Tasks</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
