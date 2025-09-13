<?php
// controllers/task_delete.php - Delete Task
require_once '../config/db.php';
require_once '../models/Task.php';
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $task = getTaskById($id);
    if ($task) {
        deleteTask($id);
        header('Location: task_list.php?project_id=' . $task['project_id']);
        exit();
    }
}
header('Location: ../index.php');
exit();
