<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

if (!isset($_SESSION['user_name'])) {
    $_SESSION['user_name'] = 'User';
}

require_once '../includes/db.php';

$user_name  = $_SESSION['user_name'] ?? 'User';
$first_name = explode(' ', trim($user_name))[0];
$today      = date('l, j F Y');
$week_num   = date('W');
$month      = date('F Y');

// Check if mood already logged today
$mood_logged_today  = false;
$today_mood         = null;
$score_out_of_10    = null;
$mood_label         = '';
$mood_emojis        = [1 => '😞', 2 => '😕', 3 => '😐', 4 => '🙂', 5 => '😄'];
$mood_labels        = [1 => 'Very Low', 2 => 'Low', 3 => 'Okay', 4 => 'Good', 5 => 'Great'];

try {
    $stmt = $pdo->prepare("SELECT score FROM mood_logs WHERE user_id = ? AND DATE(created_at) = CURDATE() ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);
    $mood_row = $stmt->fetch();
    if ($mood_row) {
        $mood_logged_today = true;
        $today_mood        = $mood_row['score'];
        $score_out_of_10   = $today_mood * 2;
        $mood_label        = $mood_labels[$today_mood] ?? '';
    }
} catch (Exception $e) {}

// Get journal streak
$journal_streak = 0;
try {
    $stmt = $pdo->prepare("SELECT DATE(created_at) as entry_date FROM journal_entries WHERE user_id = ? GROUP BY DATE(created_at) ORDER BY entry_date DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $dates   = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $check   = date('Y-m-d');
    foreach ($dates as $date) {
        if ($date === $check) {
            $journal_streak++;
            $check = date('Y-m-d', strtotime($check . ' -1 day'));
        } else break;
    }
} catch (Exception $e) {}

// Get habits
$habits      = [];
$habits_done = 0;
try {
    $stmt = $pdo->prepare("SELECT h.id, h.name, MAX(CASE WHEN DATE(hl.completed_at) = CURDATE() THEN 1 ELSE 0 END) as done_today FROM habits h LEFT JOIN habit_logs hl ON h.id = hl.habit_id AND DATE(hl.completed_at) = CURDATE() WHERE h.user_id = ? GROUP BY h.id, h.name");
    $stmt->execute([$_SESSION['user_id']]);
    $habits = $stmt->fetchAll();
    foreach ($habits as $h) { if ($h['done_today']) $habits_done++; }
} catch (Exception $e) {}

$habits_total = count($habits) ?: 5;

// Get recent journal entries
$journal_entries = [];
try {
    $stmt = $pdo->prepare("SELECT title, emotion_tag, created_at FROM journal_entries WHERE user_id = ? ORDER BY created_at DESC LIMIT 3");
    $stmt->execute([$_SESSION['user_id']]);
    $journal_entries = $stmt->fetchAll();
} catch (Exception $e) {}

// Get next calendar event
$next_event = null;
try {
    $stmt = $pdo->prepare("SELECT title, event_date, event_time FROM calendar_events WHERE user_id = ? AND event_date >= CURDATE() ORDER BY event_date ASC, event_time ASC LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);
    $next_event = $stmt->fetch();
} catch (Exception $e) {}

// Get upcoming events for calendar
$upcoming_events = [];
try {
    $stmt = $pdo->prepare("SELECT title, event_date, event_time, category FROM calendar_events WHERE user_id = ? AND event_date >= CURDATE() ORDER BY event_date ASC LIMIT 3");
    $stmt->execute([$_SESSION['user_id']]);
    $upcoming_events = $stmt->fetchAll();
} catch (Exception $e) {}

// Get mood data for this week
$week_moods = array_fill(0, 7, 0);
try {
    $stmt = $pdo->prepare("SELECT DAYOFWEEK(created_at) as dow, AVG(score) as avg_score FROM mood_logs WHERE user_id = ? AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DAYOFWEEK(created_at)");
    $stmt->execute([$_SESSION['user_id']]);
    foreach ($stmt->fetchAll() as $row) {
        $idx = ($row['dow'] + 5) % 7;
        $week_moods[$idx] = round($row['avg_score'], 1);
    }
} catch (Exception $e) {}

$dot_colors = ['#b8a8d8','#7ab0d4','#4caf82','#e8a44a','#2e5f8a'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — ClearPath</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="dashboard-page">

<header class="dash-navbar">
    <div class="dash-logo">ClearPath</div>
    <nav class="dash-nav">
        <a href="dashboard.php" class="dash-nav-link active">Overview</a>
        <a href="mood.php"      class="dash-nav-link">Mood Tracker</a>
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

    <!-- Greeting -->
    <div class="dash-greeting">
        <h1>Good morning, <?= htmlspecialchars($first_name) ?>!</h1>
        <p><?= $today ?> &nbsp;—&nbsp; Week <?= $week_num ?></p>
    </div>

    <!-- Mood Check-in -->
    <?php if (!$mood_logged_today): ?>
    <div class="mood-checkin" id="moodCheckin">
        <div class="mood-checkin-title">How are you feeling today?</div>
        <div class="mood-checkin-sub">Select the option that best describes your mood right now.</div>
        <div class="mood-options">
            <div class="mood-option" onclick="selectMood(this, 1)">
                <div class="mood-emoji">😞</div>
                <div class="mood-option-label">Very Low</div>
            </div>
            <div class="mood-option" onclick="selectMood(this, 2)">
                <div class="mood-emoji">😕</div>
                <div class="mood-option-label">Low</div>
            </div>
            <div class="mood-option" onclick="selectMood(this, 3)">
                <div class="mood-emoji">😐</div>
                <div class="mood-option-label">Okay</div>
            </div>
            <div class="mood-option" onclick="selectMood(this, 4)">
                <div class="mood-emoji">🙂</div>
                <div class="mood-option-label">Good</div>
            </div>
            <div class="mood-option" onclick="selectMood(this, 5)">
                <div class="mood-emoji">😄</div>
                <div class="mood-option-label">Great</div>
            </div>
        </div>
        <div class="mood-checkin-footer">
            <span id="moodSelectedLabel" class="mood-selected-label"></span>
            <button class="mood-save-btn" onclick="saveMood()">Save &amp; Update</button>
        </div>
    </div>
    <?php else: ?>
    <div class="mood-logged-banner">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4caf82" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        Mood logged today &mdash; <?= $mood_emojis[$today_mood] ?? '' ?> <?= htmlspecialchars($mood_label) ?>
    </div>
    <?php endif; ?>

    <!-- Stat Cards -->
    <div class="dash-stats">
        <div class="stat-card stat-card-blue">
            <div class="stat-label">Today's Mood</div>
            <div class="stat-value"><?= $mood_logged_today ? $score_out_of_10 . '/10' : '—' ?></div>
            <div class="stat-sub <?= $mood_logged_today ? 'stat-sub-green' : '' ?>"><?= $mood_logged_today ? htmlspecialchars($mood_label) : 'Not logged yet' ?></div>
        </div>
        <div class="stat-card stat-card-lavender">
            <div class="stat-label">Journal Streak</div>
            <div class="stat-value"><?= $journal_streak ?> <?= $journal_streak === 1 ? 'day' : 'days' ?></div>
            <div class="stat-sub"><?= $journal_streak > 0 ? 'Keep it going!' : 'Start writing today' ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Habits Done</div>
            <div class="stat-value"><?= $habits_done ?>/<?= $habits_total ?></div>
            <div class="stat-sub <?= ($habits_total - $habits_done) > 0 ? 'stat-sub-amber' : 'stat-sub-green' ?>"><?= ($habits_total - $habits_done) > 0 ? ($habits_total - $habits_done) . ' remaining today' : 'All done! 🎉' ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Next Event</div>
            <?php if ($next_event): ?>
                <div class="stat-value-sm"><?= htmlspecialchars($next_event['title']) ?></div>
                <div class="stat-sub"><?= date('D j M', strtotime($next_event['event_date'])) ?><?= $next_event['event_time'] ? ' at ' . date('g:i A', strtotime($next_event['event_time'])) : '' ?></div>
            <?php else: ?>
                <div class="stat-value-sm">No events</div>
                <div class="stat-sub"><a href="calendar.php" style="color:#2e5f8a;text-decoration:none;font-weight:600">Add one →</a></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main -->
    <div class="dash-grid">
        <div class="dash-grid-left">

            <!-- Mood chart -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-title">Mood this week</div>
                    <a href="mood.php" class="dash-card-link">View all</a>
                </div>
                <div class="mood-chart">
                    <?php
                    $days = ['M','T','W','T','F','S','S'];
                    $today_dow = (date('N') - 1);
                    foreach ($days as $i => $day):
                        $score   = $week_moods[$i];
                        $height  = $score > 0 ? max(12, round($score / 5 * 90)) : 8;
                        $is_today   = ($i === $today_dow);
                        $is_future  = ($i > $today_dow);
                    ?>
                    <div class="mood-bar-wrap">
                        <div class="mood-bar <?= $is_today ? 'mood-bar-today' : ($is_future ? 'mood-bar-future' : '') ?>" style="height:<?= $height ?>%"></div>
                        <div class="mood-day <?= $is_today ? 'mood-day-today' : ($is_future ? 'mood-day-future' : '') ?>"><?= $day ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Journal -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-title">Journal entries</div>
                    <a href="journal.php" class="dash-card-link">+ New</a>
                </div>
                <div class="journal-list">
                    <?php if (empty($journal_entries)): ?>
                        <div style="text-align:center;padding:20px 0">
                            <div style="font-size:13px;color:#9a9ab8;margin-bottom:10px">No journal entries yet</div>
                            <a href="journal.php" class="dash-card-link">Write your first entry →</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($journal_entries as $entry): ?>
                        <div class="journal-item">
                            <div class="journal-item-title"><?= htmlspecialchars($entry['title']) ?></div>
                            <div class="journal-item-footer">
                                <span class="journal-date"><?= date('D j M', strtotime($entry['created_at'])) ?></span>
                                <?php if ($entry['emotion_tag']): ?>
                                <span class="journal-tag"><?= htmlspecialchars($entry['emotion_tag']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Habits -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-title">Today's habits</div>
                    <span class="dash-badge-green"><?= $habits_done ?>/<?= $habits_total ?> done</span>
                </div>
                <div class="habits-list">
                    <?php if (empty($habits)): ?>
                        <div style="text-align:center;padding:20px 0">
                            <div style="font-size:13px;color:#9a9ab8;margin-bottom:10px">No habits set up yet</div>
                            <a href="selfcare.php" class="dash-card-link">Set up habits →</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($habits as $habit): ?>
                        <div class="habit-item <?= $habit['done_today'] ? 'habit-done' : '' ?>" onclick="toggleHabit(this, <?= $habit['id'] ?>)" style="cursor:pointer">
                            <div class="habit-check <?= $habit['done_today'] ? 'habit-check-done' : '' ?>">
                                <?php if ($habit['done_today']): ?>
                                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <?php endif; ?>
                            </div>
                            <span><?= htmlspecialchars($habit['name']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Articles -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-title">Recommended for you</div>
                    <a href="articles.php" class="dash-card-link">View all</a>
                </div>
                <div class="article-list">
                    <a href="https://www.nhs.uk/every-mind-matters/mental-wellbeing-tips/top-tips-to-improve-your-mental-wellbeing/" target="_blank" rel="noopener noreferrer" class="article-list-item">
                        <div class="article-list-icon" style="background:#2e5f8a">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.8)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                        </div>
                        <div class="article-list-title">Top tips to improve your mental wellbeing</div>
                    </a>
                    <a href="https://bestforyou.org.uk/guides/self-care-and-wellbeing/journalling/" target="_blank" rel="noopener noreferrer" class="article-list-item">
                        <div class="article-list-icon" style="background:#b8a8d8">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        </div>
                        <div class="article-list-title">Journalling for mental health</div>
                    </a>
                    <a href="https://www.mentalhealth.org.uk/explore-mental-health/blogs/self-care-tips" target="_blank" rel="noopener noreferrer" class="article-list-item">
                        <div class="article-list-icon" style="background:#7ab0d4">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div class="article-list-title">Self-care tips</div>
                    </a>
                </div>
            </div>

        </div>

        <!-- Calendar -->
        <div class="dash-calendar" onclick="window.location.href='calendar.php'" style="cursor:pointer">
            <div class="dash-card-header">
                <div class="dash-card-title" style="color:#fff"><?= $month ?></div>
                <a href="calendar.php" class="dash-calendar-link" onclick="event.stopPropagation()">Open full view →</a>
            </div>
            <div class="cal-days-header">
                <span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span><span>S</span>
            </div>
            <div class="cal-grid">
                <?php
                $first_day  = (int) date('N', strtotime(date('Y-m-01')));
                $days_month = (int) date('t');
                $today_num  = (int) date('j');
                for ($i = 1; $i < $first_day; $i++) echo '<div></div>';
                for ($d = 1; $d <= $days_month; $d++) {
                    if ($d === $today_num) echo "<div class='cal-today'><div>$d</div></div>";
                    else echo "<div class='cal-day'>$d</div>";
                }
                ?>
            </div>
            <div class="cal-upcoming">
                <div class="cal-upcoming-title">Upcoming events</div>
                <?php if (empty($upcoming_events)): ?>
                    <div style="font-size:11px;color:#4a7a9a;padding:8px 0">No upcoming events. <a href="calendar.php" style="color:#7ab0d4" onclick="event.stopPropagation()">Add one →</a></div>
                <?php else: ?>
                    <?php foreach ($upcoming_events as $i => $event): ?>
                    <div class="cal-event">
                        <span class="cal-dot" style="background:<?= $dot_colors[$i % count($dot_colors)] ?>"></span>
                        <?= date('D j M', strtotime($event['event_date'])) ?><?= $event['event_time'] ? ' ' . date('g:i A', strtotime($event['event_time'])) : '' ?> — <?= htmlspecialchars($event['title']) ?>
                    </div>
                    <?php endforeach; ?>
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
    fetch('../includes/mood_handler.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'score=' + selectedMood
    })
    .then(res => res.json())
    .then(data => {
        const box = document.getElementById('moodCheckin');
        box.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
        box.style.opacity = '0';
        box.style.transform = 'translateY(-10px)';
        setTimeout(() => location.reload(), 450);
    })
    .catch(() => location.reload());
}

function toggleHabit(el, habitId) {
    const isDone = el.classList.contains('habit-done');
    fetch('../includes/habit_handler.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'habit_id=' + habitId + '&action=' + (isDone ? 'uncomplete' : 'complete')
    })
    .then(() => location.reload())
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