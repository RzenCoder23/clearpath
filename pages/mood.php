<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../includes/db.php';

$user_name  = $_SESSION['user_name'] ?? 'User';
$first_name = explode(' ', trim($user_name))[0];

$mood_emojis = [1 => '😞', 2 => '😕', 3 => '😐', 4 => '🙂', 5 => '😄'];
$mood_labels = [1 => 'Very Low', 2 => 'Low', 3 => 'Okay', 4 => 'Good', 5 => 'Great'];
$mood_colors = [1 => '#e05c6a', 2 => '#e8a44a', 3 => '#7ab0d4', 4 => '#2e5f8a', 5 => '#4caf82'];


$mood_logged_today = false;
$today_mood        = null;
try {
    $stmt = $pdo->prepare("SELECT score FROM mood_logs WHERE user_id = ? AND DATE(created_at) = CURDATE() ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);
    $row = $stmt->fetch();
    if ($row) { $mood_logged_today = true; $today_mood = $row['score']; }
} catch (Exception $e) {}


$week_moods = [];
try {
    $stmt = $pdo->prepare("
        SELECT DATE(created_at) as log_date, AVG(score) as avg_score
        FROM mood_logs
        WHERE user_id = ? AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        GROUP BY DATE(created_at)
        ORDER BY log_date ASC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $week_moods = $stmt->fetchAll();
} catch (Exception $e) {}


$week_data  = [];
$day_labels = [];
for ($i = 6; $i >= 0; $i--) {
    $date          = date('Y-m-d', strtotime("-$i days"));
    $day_labels[]  = date('D', strtotime($date));
    $week_data[$date] = null;
}
foreach ($week_moods as $wm) {
    if (isset($week_data[$wm['log_date']])) {
        $week_data[$wm['log_date']] = round($wm['avg_score'] * 2, 1);
    }
}
$week_values = array_values($week_data);
$week_avg    = count(array_filter($week_values, fn($v) => $v !== null)) > 0
    ? round(array_sum(array_filter($week_values, fn($v) => $v !== null)) / count(array_filter($week_values, fn($v) => $v !== null)), 1)
    : null;


$total_entries  = 0;
$best_score     = null;
$streak         = 0;
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total, MAX(score) as best FROM mood_logs WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $stats         = $stmt->fetch();
    $total_entries = $stats['total'];
    $best_score    = $stats['best'];
} catch (Exception $e) {}


try {
    $stmt = $pdo->prepare("SELECT DATE(created_at) as log_date FROM mood_logs WHERE user_id = ? GROUP BY DATE(created_at) ORDER BY log_date DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $dates = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $check = date('Y-m-d');
    foreach ($dates as $d) {
        if ($d === $check) { $streak++; $check = date('Y-m-d', strtotime($check . ' -1 day')); }
        else break;
    }
} catch (Exception $e) {}


$history = [];
try {
    $stmt = $pdo->prepare("SELECT score, created_at FROM mood_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
    $stmt->execute([$_SESSION['user_id']]);
    $history = $stmt->fetchAll();
} catch (Exception $e) {}

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error   = isset($_GET['error'])   ? htmlspecialchars($_GET['error'])   : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mood Tracker — ClearPath</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="dashboard-page">

<header class="dash-navbar">
    <div class="dash-logo">ClearPath</div>
    <nav class="dash-nav">
        <a href="dashboard.php" class="dash-nav-link">Overview</a>
        <a href="mood.php"      class="dash-nav-link active">Mood Tracker</a>
        <a href="journal.php"   class="dash-nav-link">Journal</a>
        <a href="selfcare.php"  class="dash-nav-link">Self-Care</a>
        <a href="calendar.php"  class="dash-nav-link">Calendar</a>
        <a href="articles.php"  class="dash-nav-link">Articles</a>
    </nav>
    <div class="dash-profile-wrap">
        <div class="dash-profile-btn" onclick="toggleDropdown()">
            <div class="dash-avatar"><?= strtoupper(substr($first_name, 0, 2)) ?></div>
            <span><?= htmlspecialchars($first_name) ?></span>
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#7ab0d4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
        <div class="dash-dropdown" id="profileDropdown">
            <a href="settings.php" class="dash-dropdown-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#7ab0d4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                Settings
            </a>
            <a href="chatbot.php" class="dash-dropdown-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#b8a8d8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                Chatbot
            </a>
            <a href="../includes/logout.php" class="dash-dropdown-item dash-dropdown-logout">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#e05c6a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Logout
            </a>
        </div>
    </div>
</header>

<main class="dash-main">

    <div class="dash-greeting">
        <h1 class="page-title">Mood Tracker</h1>
        <p class="page-sub">Track and understand your emotional patterns</p>
    </div>

    <?php if ($success): ?>
    <div class="alert alert-success" style="margin-bottom:16px"><?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="alert alert-error" style="margin-bottom:16px"><?= $error ?></div>
    <?php endif; ?>

    <div class="mood-tracker-layout">

        <!-- Left column -->
        <div style="display:flex;flex-direction:column;gap:14px">

            <!-- Log mood -->
            <?php if (!$mood_logged_today): ?>
            <div class="mood-checkin" id="moodLogBox">
                <div class="mood-checkin-title">How are you feeling today?</div>
                <div class="mood-checkin-sub">Select your mood and add an optional note</div>
                <div class="mood-options">
                    <?php foreach ($mood_emojis as $score => $emoji): ?>
                    <div class="mood-option" onclick="selectMood(this, <?= $score ?>)">
                        <div class="mood-emoji"><?= $emoji ?></div>
                        <div class="mood-option-label"><?= $mood_labels[$score] ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <textarea id="moodNote" class="mood-note-input" placeholder="Add a note about how you're feeling... (optional)"></textarea>
                <div class="mood-checkin-footer">
                    <span id="moodSelectedLabel" class="mood-selected-label"></span>
                    <button class="mood-save-btn" onclick="saveMood()">Save Mood</button>
                </div>
            </div>
            <?php else: ?>
            <div class="mood-logged-banner">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4caf82" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Mood already logged today — <?= $mood_emojis[$today_mood] ?> <?= $mood_labels[$today_mood] ?> (<?= $today_mood * 2 ?>/10)
            </div>
            <?php endif; ?>

            <!-- Weekly chart -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-title">This week's mood</div>
                    <?php if ($week_avg !== null): ?>
                    <span style="font-size:12px;color:#7a90b8">Avg: <strong style="color:#2e5f8a"><?= $week_avg ?>/10</strong></span>
                    <?php endif; ?>
                </div>
                <?php if (empty(array_filter($week_values, fn($v) => $v !== null))): ?>
                <div style="text-align:center;padding:28px 0;font-size:13px;color:#9a9ab8">No mood logs this week yet. Start tracking above!</div>
                <?php else: ?>
                <div class="mood-chart-tall">
                    <?php foreach ($week_values as $i => $val):
                        $date_i   = date('Y-m-d', strtotime("-" . (6 - $i) . " days"));
                        $is_today = ($date_i === date('Y-m-d'));
                        $height   = $val !== null ? max(10, round($val / 10 * 100)) : 6;
                        $color    = $val !== null ? ($is_today ? '#2e5f8a' : '#c8dff0') : '#f0eef8';
                        $label    = $val !== null ? $val : '—';
                    ?>
                    <div class="mood-bar-wrap">
                        <div style="font-size:10px;color:<?= $is_today ? '#2e5f8a' : '#9a9ab8' ?>;font-weight:<?= $is_today ? '700' : '400' ?>;margin-bottom:4px"><?= $label ?></div>
                        <div class="mood-bar <?= $is_today ? 'mood-bar-today' : ($val === null ? 'mood-bar-future' : '') ?>" style="height:<?= $height ?>%"></div>
                        <div class="mood-day <?= $is_today ? 'mood-day-today' : ($val === null ? 'mood-day-future' : '') ?>"><?= $day_labels[$i] ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- Right Column -->
        <div style="display:flex;flex-direction:column;gap:14px">

            <!-- Stat Cards -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="dash-card" style="text-align:center;padding:18px">
                    <div style="font-size:11px;color:#7a90b8;margin-bottom:8px">Today's mood</div>
                    <?php if ($mood_logged_today): ?>
                    <div style="font-size:28px;margin-bottom:4px"><?= $mood_emojis[$today_mood] ?></div>
                    <div style="font-size:18px;font-weight:700;color:#2e5f8a"><?= $today_mood * 2 ?>/10</div>
                    <div style="font-size:11px;color:#4caf82;margin-top:3px"><?= $mood_labels[$today_mood] ?></div>
                    <?php else: ?>
                    <div style="font-size:28px;margin-bottom:4px">—</div>
                    <div style="font-size:13px;color:#9a9ab8">Not logged yet</div>
                    <?php endif; ?>
                </div>
                <div class="dash-card" style="text-align:center;padding:18px">
                    <div style="font-size:11px;color:#7a90b8;margin-bottom:8px">Best this week</div>
                    <?php if ($best_score): ?>
                    <div style="font-size:28px;margin-bottom:4px"><?= $mood_emojis[$best_score] ?></div>
                    <div style="font-size:18px;font-weight:700;color:#b8a8d8"><?= $best_score * 2 ?>/10</div>
                    <div style="font-size:11px;color:#7a90b8;margin-top:3px"><?= $mood_labels[$best_score] ?></div>
                    <?php else: ?>
                    <div style="font-size:28px;margin-bottom:4px">—</div>
                    <div style="font-size:13px;color:#9a9ab8">No data yet</div>
                    <?php endif; ?>
                </div>
                <div class="dash-card" style="text-align:center;padding:18px">
                    <div style="font-size:11px;color:#7a90b8;margin-bottom:8px">Total entries</div>
                    <div style="font-size:32px;font-weight:700;color:#2e5f8a;margin:4px 0"><?= $total_entries ?></div>
                    <div style="font-size:11px;color:#7a90b8">All time</div>
                </div>
                <div class="dash-card" style="text-align:center;padding:18px">
                    <div style="font-size:11px;color:#7a90b8;margin-bottom:8px">Current streak</div>
                    <div style="font-size:32px;font-weight:700;color:#e8a44a;margin:4px 0"><?= $streak ?><?= $streak > 0 ? '🔥' : '' ?></div>
                    <div style="font-size:11px;color:#7a90b8">Days in a row</div>
                </div>
            </div>

            <!-- Recent History -->
            <div class="dash-card">
                <div class="dash-card-title" style="margin-bottom:14px">Recent mood history</div>
                <?php if (empty($history)): ?>
                <div style="text-align:center;padding:20px 0;font-size:13px;color:#9a9ab8">No mood logs yet — start tracking today!</div>
                <?php else: ?>
                <div style="display:flex;flex-direction:column;gap:8px">
                    <?php foreach ($history as $h):
                        $score   = $h['score'];
                        $out10   = $score * 2;
                        $pct     = $out10 * 10;
                        $color   = $mood_colors[$score];
                        $emoji   = $mood_emojis[$score];
                        $label   = $mood_labels[$score];
                        $date_f  = date('D, j M Y', strtotime($h['created_at']));
                        $is_today = (date('Y-m-d', strtotime($h['created_at'])) === date('Y-m-d'));
                    ?>
                    <div style="display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:10px;background:#f5f3ff;border:0.5px solid #e0d8f8">
                        <div style="font-size:22px;flex-shrink:0"><?= $emoji ?></div>
                        <div style="flex:1">
                            <div style="font-size:13px;font-weight:600;color:#1a2a3a"><?= $label ?> — <?= $out10 ?>/10</div>
                            <div style="font-size:11px;color:#7a90b8;margin-top:2px"><?= $is_today ? 'Today' : $date_f ?></div>
                        </div>
                        <div style="width:60px;height:8px;background:#e8e4f8;border-radius:4px;overflow:hidden;flex-shrink:0">
                            <div style="width:<?= $pct ?>%;height:100%;background:<?= $color ?>;border-radius:4px"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

        </div>

    </div>

</main>

<script>
let selectedMood = null;

function selectMood(el, score) {
    document.querySelectorAll('.mood-option').forEach(o => o.classList.remove('mood-option-selected'));
    el.classList.add('mood-option-selected');
    selectedMood = score;
    const labels = {1:'Very Low', 2:'Low', 3:'Okay', 4:'Good', 5:'Great'};
    document.getElementById('moodSelectedLabel').textContent = 'Selected: ' + labels[score];
}

function saveMood() {
    if (!selectedMood) { alert('Please select a mood first.'); return; }
    const note = document.getElementById('moodNote') ? document.getElementById('moodNote').value : '';
    fetch('../includes/mood_handler.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'score=' + selectedMood + '&note=' + encodeURIComponent(note)
    })
    .then(res => res.json())
    .then(() => {
        const box = document.getElementById('moodLogBox');
        if (box) {
            box.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            box.style.opacity = '0';
            box.style.transform = 'translateY(-10px)';
            setTimeout(() => location.reload(), 450);
        }
    })
    .catch(() => location.reload());
}

function toggleDropdown() {
    document.getElementById('profileDropdown').classList.toggle('active');
}
document.addEventListener('click', function(e) {
    const wrap = document.querySelector('.dash-profile-wrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('profileDropdown').classList.remove('active');
    }
});
</script>

</body>
</html>