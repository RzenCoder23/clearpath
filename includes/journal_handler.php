<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/journal.php');
    exit();
}

require_once 'db.php';

$user_id = $_SESSION['user_id'];
$action  = $_POST['action'] ?? '';


$allowed_emotions = [
    '', 'Content', 'Happy', 'Anxious', 'Sad', 'Grateful',
    'Stressed', 'Resilient', 'Calm', 'Overwhelmed', 'Proud'
];


if ($action === 'create') {

    $title       = trim($_POST['title']       ?? '');
    $content     = trim($_POST['content']     ?? '');
    $emotion_tag = trim($_POST['emotion_tag'] ?? '');

    if ($title === '' || $content === '') {
        header('Location: ../pages/journal.php?error=Please fill in both the title and content.');
        exit();
    }

    if (mb_strlen($title) > 255) {
        header('Location: ../pages/journal.php?error=Title is too long (max 255 characters).');
        exit();
    }

    if (mb_strlen($content) > 10000) {
        header('Location: ../pages/journal.php?error=Entry is too long (max 10000 characters).');
        exit();
    }

    if (!in_array($emotion_tag, $allowed_emotions, true)) {
        $emotion_tag = '';
    }

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO journal_entries (user_id, title, content, emotion_tag, created_at)
             VALUES (?, ?, ?, ?, NOW())"
        );
        $stmt->execute([
            $user_id,
            $title,
            $content,
            $emotion_tag !== '' ? $emotion_tag : null,
        ]);

        header('Location: ../pages/journal.php?success=Journal entry saved.');
        exit();

    } catch (Exception $e) {
        error_log('Journal create error: ' . $e->getMessage());
        header('Location: ../pages/journal.php?error=Could not save your entry. Please try again.');
        exit();
    }


} elseif ($action === 'edit') {

    $entry_id    = (int) ($_POST['entry_id'] ?? 0);
    $title       = trim($_POST['title']       ?? '');
    $content     = trim($_POST['content']     ?? '');
    $emotion_tag = trim($_POST['emotion_tag'] ?? '');

    if (!$entry_id) {
        header('Location: ../pages/journal.php?error=Invalid entry.');
        exit();
    }

    if ($title === '' || $content === '') {
        header('Location: ../pages/journal.php?error=Please fill in both the title and content.');
        exit();
    }

    if (mb_strlen($title) > 255 || mb_strlen($content) > 10000) {
        header('Location: ../pages/journal.php?error=Entry is too long.');
        exit();
    }

    if (!in_array($emotion_tag, $allowed_emotions, true)) {
        $emotion_tag = '';
    }

    try {
        // Verify entry belongs to the logged-in user before updating.
        $stmt = $pdo->prepare(
            "UPDATE journal_entries
             SET title = ?, content = ?, emotion_tag = ?
             WHERE id = ? AND user_id = ?"
        );
        $stmt->execute([
            $title,
            $content,
            $emotion_tag !== '' ? $emotion_tag : null,
            $entry_id,
            $user_id,
        ]);

        if ($stmt->rowCount() === 0) {
            header('Location: ../pages/journal.php?error=Entry not found.');
            exit();
        }

        header('Location: ../pages/journal.php?success=Journal entry updated.');
        exit();

    } catch (Exception $e) {
        error_log('Journal edit error: ' . $e->getMessage());
        header('Location: ../pages/journal.php?error=Could not update your entry. Please try again.');
        exit();
    }


} elseif ($action === 'delete') {

    $entry_id = (int) ($_POST['entry_id'] ?? 0);

    if (!$entry_id) {
        header('Location: ../pages/journal.php?error=Invalid entry.');
        exit();
    }

    try {
        $stmt = $pdo->prepare(
            "DELETE FROM journal_entries WHERE id = ? AND user_id = ?"
        );
        $stmt->execute([$entry_id, $user_id]);

        header('Location: ../pages/journal.php?success=Journal entry deleted.');
        exit();

    } catch (Exception $e) {
        error_log('Journal delete error: ' . $e->getMessage());
        header('Location: ../pages/journal.php?error=Could not delete your entry. Please try again.');
        exit();
    }

} else {
    header('Location: ../pages/journal.php');
    exit();
}