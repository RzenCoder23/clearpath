<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/settings.php');
    exit();
}

require_once 'db.php';

$action  = $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'];


if ($action === 'update_profile') {

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name']  ?? '');
    $email      = trim($_POST['email']      ?? '');

    if (empty($first_name) || empty($last_name) || empty($email)) {
        header('Location: ../pages/settings.php?tab=profile&error=Please fill in all fields.');
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ../pages/settings.php?tab=profile&error=Please enter a valid email address.');
        exit();
    }

    try {

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetch()) {
            header('Location: ../pages/settings.php?tab=profile&error=That email is already in use by another account.');
            exit();
        }

        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?");
        $stmt->execute([$first_name, $last_name, $email, $user_id]);


        $_SESSION['user_name']  = $first_name . ' ' . $last_name;
        $_SESSION['user_email'] = $email;

        header('Location: ../pages/settings.php?tab=profile&success=Profile updated successfully!');
        exit();

    } catch (Exception $e) {
        header('Location: ../pages/settings.php?tab=profile&error=Could not update profile. Please try again.');
        exit();
    }

//password update
} elseif ($action === 'update_password') {

    $current_password  = $_POST['current_password']  ?? '';
    $new_password      = $_POST['new_password']       ?? '';
    $confirm_password  = $_POST['confirm_password']   ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        header('Location: ../pages/settings.php?tab=password&error=Please fill in all password fields.');
        exit();
    }

    if ($new_password !== $confirm_password) {
        header('Location: ../pages/settings.php?tab=password&error=New passwords do not match.');
        exit();
    }

    if (strlen($new_password) < 8) {
        header('Location: ../pages/settings.php?tab=password&error=Password must be at least 8 characters.');
        exit();
    }

    if (!preg_match('/[A-Z]/', $new_password)) {
        header('Location: ../pages/settings.php?tab=password&error=Password must contain at least one uppercase letter.');
        exit();
    }

    if (!preg_match('/[0-9]/', $new_password)) {
        header('Location: ../pages/settings.php?tab=password&error=Password must contain at least one number.');
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($current_password, $user['password'])) {
            header('Location: ../pages/settings.php?tab=password&error=Current password is incorrect.');
            exit();
        }

        $hashed = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt   = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed, $user_id]);

        header('Location: ../pages/settings.php?tab=password&success=Password updated successfully!');
        exit();

    } catch (Exception $e) {
        header('Location: ../pages/settings.php?tab=password&error=Could not update password. Please try again.');
        exit();
    }

//Deletes account
} elseif ($action === 'delete_account') {

    $confirm_password = $_POST['confirm_password'] ?? '';
    $confirm_text     = strtoupper(trim($_POST['confirm_text'] ?? ''));

    if ($confirm_text !== 'DELETE') {
        header('Location: ../pages/settings.php?tab=delete&error=Please type DELETE to confirm account deletion.');
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($confirm_password, $user['password'])) {
            header('Location: ../pages/settings.php?tab=delete&error=Incorrect password. Account not deleted.');
            exit();
        }


        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);


        session_destroy();
        header('Location: ../pages/login.php?success=Your account has been permanently deleted.');
        exit();

    } catch (Exception $e) {
        header('Location: ../pages/settings.php?tab=delete&error=Could not delete account. Please try again.');
        exit();
    }

} else {
    header('Location: ../pages/settings.php');
    exit();
}
?>