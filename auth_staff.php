<?php

header('Content-Type: application/json');
include('includes/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];

    $stmt = $dbh->query("SELECT password_hash FROM staff WHERE username='staff'");
    $row = $stmt->fetch();

    if ($row && password_verify($password, $row['password_hash'])) {
        $_SESSION['role'] = 'staff';
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid password']);
    }
}
