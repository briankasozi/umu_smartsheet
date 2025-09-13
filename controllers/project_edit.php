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
</head>
<body>
    <h2>Edit Project</h2>
    <?php if ($error): ?><p style="color:red;"> <?= $error ?> </p><?php endif; ?>
    <form method="post">
        <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($project['name']) ?>" required></label><br>
        <label>Description: <textarea name="description"><?= htmlspecialchars($project['description']) ?></textarea></label><br>
        <label>Start Date: <input type="date" name="start_date" value="<?= $project['start_date'] ?>"></label><br>
        <label>End Date: <input type="date" name="end_date" value="<?= $project['end_date'] ?>"></label><br>
        <label>Status: <input type="text" name="status" value="<?= htmlspecialchars($project['status']) ?>"></label><br>
        <label>Priority: <input type="text" name="priority" value="<?= htmlspecialchars($project['priority']) ?>"></label><br>
        <button type="submit">Update</button>
    </form>
    <a href="../index.php">Back to Dashboard</a>
</body>
</html>
