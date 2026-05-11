<?php

session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

$score = isset($_POST['score']) ? (int) $_POST['score'] : 0;

if ($score < 1 || $score > 5) {
    echo json_encode(['success' => false, 'error' => 'Invalid score']);
    exit();
}

try {
    require_once 'db.php';
    $stmt = $pdo->prepare(
        "INSERT INTO mood_logs (user_id, score, created_at) VALUES (?, ?, NOW())"
    );
    $stmt->execute([$_SESSION['user_id'], $score]);
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    error_log('Mood log error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Could not save mood.']);
}