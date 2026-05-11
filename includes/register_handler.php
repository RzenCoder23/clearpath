<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/register.php');
    exit();
}

$first_name      = trim($_POST['first_name'] ?? '');
$last_name       = trim($_POST['last_name'] ?? '');
$email           = trim($_POST['email'] ?? '');
$password        = $_POST['password'] ?? '';
$confirm         = $_POST['confirm_password'] ?? '';
$terms           = $_POST['terms'] ?? '';

// validation
if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
    header('Location: ../pages/register.php?error=Please fill in all fields.');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../pages/register.php?error=Please enter a valid email address.');
    exit();
}

if (strlen($password) < 8) {
    header('Location: ../pages/register.php?error=Password must be at least 8 characters.');
    exit();
}

if (!preg_match('/[A-Z]/', $password)) {
    header('Location: ../pages/register.php?error=Password must contain at least one uppercase letter.');
    exit();
}

if (!preg_match('/[0-9]/', $password)) {
    header('Location: ../pages/register.php?error=Password must contain at least one number.');
    exit();
}

if ($password !== $confirm) {
    header('Location: ../pages/register.php?error=Passwords do not match.');
    exit();
}

if (empty($terms)) {
    header('Location: ../pages/register.php?error=You must agree to the Terms of Service.');
    exit();
}

try {
    // Checks if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header('Location: ../pages/register.php?error=An account with that email already exists.');
        exit();
    }

    // Hash password and insert user
    $hashed = password_hash($password, PASSWORD_BCRYPT);
    $stmt   = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$first_name, $last_name, $email, $hashed]);

    $user_id = $pdo->lastInsertId();

    // Add default habits for the new user
    $default_habits = ['Morning meditation', '30 min walk', 'Journal entry', 'Evening reading', 'Breathing exercise'];
    $habit_stmt     = $pdo->prepare("INSERT INTO habits (user_id, name, frequency) VALUES (?, ?, 'daily')");
    foreach ($default_habits as $habit) {
        $habit_stmt->execute([$user_id, $habit]);
    }

    // Logs in automatically
    $_SESSION['user_id']   = $user_id;
    $_SESSION['user_name'] = $first_name . ' ' . $last_name;
    $_SESSION['user_email'] = $email;

    header('Location: ../pages/dashboard.php');
    exit();

} catch (PDOException $e) {
    header('Location: ../pages/register.php?error=Something went wrong. Please try again.');
    exit();
}
?>