<?php
// controllers/project_delete.php - Delete Project
require_once '../config/db.php';
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare('DELETE FROM projects WHERE id = ?');
    $stmt->execute([$id]);
}
header('Location: ../index.php');
exit();
