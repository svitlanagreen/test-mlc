<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $name = $_POST['name'];
    $password = $_POST['password'];
    
    if (strlen($name) < 2 || strlen($name) > 50) {
        echo json_encode(['success' => false, 'message' => 'Name must be between 2 and 50 characters']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }

    if (strlen($password) < 5 || strlen($password) > 20) {
        echo json_encode(['success' => false, 'message' => 'Password must be between 5 and 20 characters']);
        exit;
    }


    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        echo json_encode(['success' => false, 'message' => 'User already exists']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (email, name, password) VALUES (?, ?, ?)");
    $stmt->execute([$email, $name, $hashedPassword]);

    echo json_encode(['success' => true, 'message' => 'Registration successful']);
    exit;
}
?>
