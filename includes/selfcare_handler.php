<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/selfcare.php');
    exit();
}

require_once 'db.php';

$user_id = $_SESSION['user_id'];
$action  = $_POST['action'] ?? '';

// ADD
if ($action === 'add') {

    $name      = trim($_POST['name']      ?? '');
    $frequency = trim($_POST['frequency'] ?? 'daily');

    if ($name === '') {
        header('Location: ../pages/selfcare.php?error=Please give your habit a name.');
        exit();
    }

    if (mb_strlen($name) > 100) {
        header('Location: ../pages/selfcare.php?error=Habit name is too long (max 100 characters).');
        exit();
    }

    if (!in_array($frequency, ['daily', 'weekly'], true)) {
        $frequency = 'daily';
    }

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO habits (user_id, name, frequency, created_at)
             VALUES (?, ?, ?, NOW())"
        );
        $stmt->execute([$user_id, $name, $frequency]);

        header('Location: ../pages/selfcare.php?success=Habit added.');
        exit();

    } catch (Exception $e) {
        error_log('Habit add error: ' . $e->getMessage());
        header('Location: ../pages/selfcare.php?error=Could not add your habit. Please try again.');
        exit();
    }

// DELETE
} elseif ($action === 'delete') {

    $habit_id = (int) ($_POST['habit_id'] ?? 0);

    if (!$habit_id) {
        header('Location: ../pages/selfcare.php?error=Invalid habit.');
        exit();
    }

    try {

        $pdo->beginTransaction();


        $stmt = $pdo->prepare(
            "DELETE FROM habit_logs WHERE habit_id = ? AND user_id = ?"
        );
        $stmt->execute([$habit_id, $user_id]);


        $stmt = $pdo->prepare(
            "DELETE FROM habits WHERE id = ? AND user_id = ?"
        );
        $stmt->execute([$habit_id, $user_id]);

        $pdo->commit();

        header('Location: ../pages/selfcare.php?success=Habit deleted.');
        exit();

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Habit delete error: ' . $e->getMessage());
        header('Location: ../pages/selfcare.php?error=Could not delete your habit. Please try again.');
        exit();
    }

} else {
    header('Location: ../pages/selfcare.php');
    exit();
}