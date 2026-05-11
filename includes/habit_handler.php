<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

$habit_id = (int) ($_POST['habit_id'] ?? 0);
$action   = $_POST['action'] ?? '';

if (!$habit_id || !in_array($action, ['complete', 'uncomplete'])) {
    echo json_encode(['success' => false]);
    exit();
}

try {
    require_once 'db.php';

    // Verify if habit belongs to user
    $stmt = $pdo->prepare("SELECT id FROM habits WHERE id = ? AND user_id = ?");
    $stmt->execute([$habit_id, $_SESSION['user_id']]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false]);
        exit();
    }

    if ($action === 'complete') {
        $stmt = $pdo->prepare("INSERT IGNORE INTO habit_logs (habit_id, user_id, completed_at) VALUES (?, ?, NOW())");
        $stmt->execute([$habit_id, $_SESSION['user_id']]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM habit_logs WHERE habit_id = ? AND user_id = ? AND DATE(completed_at) = CURDATE()");
        $stmt->execute([$habit_id, $_SESSION['user_id']]);
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false]);
}
?>