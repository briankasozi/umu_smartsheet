<?php
// models/Task.php - Task database functions
require_once __DIR__ . '/../config/db.php';

function getTasksByProject($project_id) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM tasks WHERE project_id = ? ORDER BY due_date ASC');
    $stmt->execute([$project_id]);
    $tasks = $stmt->fetchAll();
    // Fetch assignees for each task
    foreach ($tasks as &$task) {
        $task['assignees'] = getTaskAssignees($task['id']);
    }
    return $tasks;
}

function getTaskById($id) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM tasks WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function createTask($data) {
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO tasks (project_id, title, description, status, due_date, agreed_date, actual_date, progress, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $data['project_id'],
        $data['title'],
        $data['description'],
        $data['status'],
        $data['due_date'],
        $data['agreed_date'],
        $data['actual_date'],
        $data['progress'],
        $data['notes']
    ]);
    $task_id = $pdo->lastInsertId();
    // Assign users if provided
    if (!empty($data['assignees']) && is_array($data['assignees'])) {
        foreach ($data['assignees'] as $user_id) {
            $stmt2 = $pdo->prepare('INSERT INTO task_assignees (task_id, user_id) VALUES (?, ?)');
            $stmt2->execute([$task_id, $user_id]);
        }
    }
    return $task_id;
}

function updateTask($id, $data) {
    global $pdo;
    $stmt = $pdo->prepare('UPDATE tasks SET title=?, description=?, status=?, due_date=?, agreed_date=?, actual_date=?, progress=?, notes=? WHERE id=?');
    $stmt->execute([
        $data['title'],
        $data['description'],
        $data['status'],
        $data['due_date'],
        $data['agreed_date'],
        $data['actual_date'],
        $data['progress'],
        $data['notes'],
        $id
    ]);
    // Update assignees
    $stmt2 = $pdo->prepare('DELETE FROM task_assignees WHERE task_id = ?');
    $stmt2->execute([$id]);
    if (!empty($data['assignees']) && is_array($data['assignees'])) {
        foreach ($data['assignees'] as $user_id) {
            $stmt3 = $pdo->prepare('INSERT INTO task_assignees (task_id, user_id) VALUES (?, ?)');
            $stmt3->execute([$id, $user_id]);
        }
    }
    return true;
}

function getTaskAssignees($task_id) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT users.id, users.username FROM task_assignees JOIN users ON users.id = task_assignees.user_id WHERE task_assignees.task_id = ?');
    $stmt->execute([$task_id]);
    return $stmt->fetchAll();
}

function deleteTask($id) {
    global $pdo;
    $stmt = $pdo->prepare('DELETE FROM tasks WHERE id = ?');
    return $stmt->execute([$id]);
}
