<?php
// models/User.php - User database functions
require_once __DIR__ . '/../config/db.php';

function getUserByUsername($username) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    return $stmt->fetch();
}

function getUserById($id) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function createUser($username, $email, $password, $role_id = 4) {
    global $pdo;
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)');
    return $stmt->execute([$username, $email, $hash, $role_id]);
}

function getAllUsers() {
    global $pdo;
    $stmt = $pdo->query('SELECT users.*, roles.name AS role_name FROM users LEFT JOIN roles ON users.role_id = roles.id ORDER BY users.id ASC');
    return $stmt->fetchAll();
}

function updateUser($id, $username, $email, $role_id, $password = null) {
    global $pdo;
    if ($password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ?, role_id = ?, password = ? WHERE id = ?');
        return $stmt->execute([$username, $email, $role_id, $hash, $id]);
    } else {
        $stmt = $pdo->prepare('UPDATE users SET username = ?, email = ?, role_id = ? WHERE id = ?');
        return $stmt->execute([$username, $email, $role_id, $id]);
    }
}

function userExists($username, $email, $excludeId = null) {
    global $pdo;
    if ($excludeId) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?');
        $stmt->execute([$username, $email, $excludeId]);
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $email]);
    }
    return $stmt->fetch() !== false;
}
