<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/login.php');
    exit();
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';


if (empty($email) || empty($password)) {
    header('Location: ../pages/login.php?error=Please fill in all fields.');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../pages/login.php?error=Please enter a valid email address.');
    exit();
}

try {


    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        header('Location: ../pages/login.php?error=Incorrect email or password.');
        exit();
    }


    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_name']  = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['user_email'] = $user['email'];

    // Redirects to dashboard
    header('Location: ../pages/dashboard.php');
    exit();

} catch (PDOException $e) {
    header('Location: ../pages/login.php?error=Something went wrong. Please try again.');
    exit();
}
?>