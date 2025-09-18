<?php
// models/Project.php - Project database functions
require_once __DIR__ . '/../config/db.php';

function getAllProjects() {
    global $pdo;
    $stmt = $pdo->query('SELECT * FROM projects ORDER BY created_at DESC');
    return $stmt->fetchAll();
}

function getProjectById($id) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function createProject($data) {
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO projects (name, description, start_date, end_date, status, priority) VALUES (?, ?, ?, ?, ?, ?)');
    return $stmt->execute([
        $data['name'],
        $data['description'],
        $data['start_date'],
        $data['end_date'],
        $data['status'],
        $data['priority']
    ]);
}

function updateProject($id, $data) {
    global $pdo;
    $stmt = $pdo->prepare('UPDATE projects SET name=?, description=?, start_date=?, end_date=?, status=?, priority=? WHERE id=?');
    return $stmt->execute([
        $data['name'],
        $data['description'],
        $data['start_date'],
        $data['end_date'],
        $data['status'],
        $data['priority'],
        $id
    ]);
}

function deleteProject($id) {
    global $pdo;
    $stmt = $pdo->prepare('DELETE FROM projects WHERE id = ?');
    return $stmt->execute([$id]);
}
