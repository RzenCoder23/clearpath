<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/calendar.php');
    exit();
}

require_once 'db.php';

$action   = $_POST['action'] ?? '';
$user_id  = $_SESSION['user_id'];
$event_id = (int)($_POST['event_id'] ?? 0);

if ($action === 'create') {

    $title       = trim($_POST['title'] ?? '');
    $event_date  = trim($_POST['event_date'] ?? '');
    $event_time  = trim($_POST['event_time'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category    = trim($_POST['category'] ?? 'Other');

    if (empty($title) || empty($event_date)) {
        header('Location: ../pages/calendar.php?error=Please fill in the event title and date.');
        exit();
    }

    // validate date format
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $event_date)) {
        header('Location: ../pages/calendar.php?error=Invalid date format.');
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO calendar_events (user_id, title, description, event_date, event_time, category, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $user_id,
            $title,
            $description ?: null,
            $event_date,
            $event_time ?: null,
            $category
        ]);


        $m = date('n', strtotime($event_date));
        $y = date('Y', strtotime($event_date));
        header("Location: ../pages/calendar.php?month=$m&year=$y&success=Event added successfully!");
        exit();

    } catch (Exception $e) {
        header('Location: ../pages/calendar.php?error=Could not save event. Please try again.');
        exit();
    }

} elseif ($action === 'delete') {

    if (!$event_id) {
        header('Location: ../pages/calendar.php?error=Invalid event.');
        exit();
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM calendar_events WHERE id = ? AND user_id = ?");
        $stmt->execute([$event_id, $user_id]);
        header('Location: ../pages/calendar.php?success=Event deleted.');
        exit();
    } catch (Exception $e) {
        header('Location: ../pages/calendar.php?error=Could not delete event. Please try again.');
        exit();
    }

} else {
    header('Location: ../pages/calendar.php');
    exit();
}
?>