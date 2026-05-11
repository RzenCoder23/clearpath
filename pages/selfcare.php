<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../includes/db.php';

$user_name  = $_SESSION['user_name'] ?? 'User';
$first_name = explode(' ', trim($user_name))[0];


$habits = [];
try {
    $stmt = $pdo->prepare("
        SELECT h.id, h.name, h.frequency,
               MAX(CASE WHEN DATE(hl.completed_at) = CURDATE() THEN 1 ELSE 0 END) as done_today
        FROM habits h
        LEFT JOIN habit_logs hl ON h.id = hl.habit_id AND DATE(hl.completed_at) = CURDATE()
        WHERE h.user_id = ?
        GROUP BY h.id, h.name, h.frequency
        ORDER BY h.created_at ASC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $habits = $stmt->fetchAll();
} catch (Exception $e) {}

$habits_done  = array_sum(array_column($habits, 'done_today'));
$habits_total = count($habits);

// Weekly progress per habit (last 7 days)
$weekly_progress = [];
try {
    foreach ($habits as $habit) {
        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT DATE(completed_at)) as days_done
            FROM habit_logs
            WHERE habit_id = ? AND user_id = ? AND completed_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        ");
        $stmt->execute([$habit['id'], $_SESSION['user_id']]);
        $row = $stmt->fetch();
        $weekly_progress[$habit['id']] = $row['days_done'] ?? 0;
    }
} catch (Exception $e) {}

// Best streak (all habits combined)
$best_streak = 0;
try {
    $stmt = $pdo->prepare("
        SELECT DATE(completed_at) as log_date
        FROM habit_logs
        WHERE user_id = ?
        GROUP BY DATE(completed_at)
        ORDER BY log_date DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $dates  = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $streak = 0;
    $check  = date('Y-m-d');
    foreach ($dates as $d) {
        if ($d === $check) { $streak++; $check = date('Y-m-d', strtotime($check . ' -1 day')); }
        else break;
    }
    $best_streak = $streak;
} catch (Exception $e) {}

$progress_colors = ['#2e5f8a','#b8a8d8','#4caf82','#e8a44a','#7ab0d4','#e05c6a','#d4c8f0'];

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error   = isset($_GET['error'])   ? htmlspecialchars($_GET['error'])   : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Self-Care Planner — ClearPath</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="dashboard-page">

<header class="dash-navbar">
    <div class="dash-logo">ClearPath</div>
    <nav class="dash-nav">
        <a href="dashboard.php" class="dash-nav-link">Overview</a>
        <a href="mood.php"      class="dash-nav-link">Mood Tracker</a>
        <a href="journal.php"   class="dash-nav-link">Journal</a>
        <a href="selfcare.php"  class="dash-nav-link active">Self-Care</a>
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

    <div class="page-header">
        <div>
            <h1 class="page-title">Self-Care Planner</h1>
            <p class="page-sub">Build healthy habits one day at a time</p>
        </div>
        <button class="btn-page-action" onclick="toggleHabitForm()">+ Add Habit</button>
    </div>

    <?php if ($success): ?>
    <div class="alert alert-success" style="margin-bottom:16px"><?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="alert alert-error" style="margin-bottom:16px"><?= $error ?></div>
    <?php endif; ?>

    <!-- stat cards -->
    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-bottom:20px">
        <div class="stat-card stat-card-blue">
            <div class="stat-label">Done today</div>
            <div class="stat-value"><?= $habits_done ?>/<?= $habits_total ?></div>
            <div class="stat-sub <?= ($habits_total - $habits_done) > 0 ? 'stat-sub-amber' : 'stat-sub-green' ?>">
                <?= ($habits_total - $habits_done) > 0 ? ($habits_total - $habits_done) . ' remaining' : 'All done! 🎉' ?>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Current streak</div>
            <div class="stat-value" style="color:#e8a44a"><?= $best_streak ?><?= $best_streak > 0 ? '🔥' : '' ?></div>
            <div class="stat-sub">Days in a row</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total habits</div>
            <div class="stat-value"><?= $habits_total ?></div>
            <div class="stat-sub">Active</div>
        </div>
    </div>

    <div class="selfcare-layout">

        <!-- Habits List -->
        <div style="display:flex;flex-direction:column;gap:14px">

            <!-- Add habit form -->
            <div class="dash-card" id="habitForm" style="display:none">
                <div class="dash-card-title" style="margin-bottom:14px">Add new habit</div>
                <form action="../includes/selfcare_handler.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label>Habit name</label>
                        <input type="text" name="name" placeholder="e.g. Drink 8 glasses of water" required>
                    </div>
                    <div class="form-group">
                        <label>Frequency</label>
                        <div class="emotion-tags">
                            <div class="emotion-tag emotion-tag-selected" onclick="selectFreq(this,'daily')">Daily</div>
                            <div class="emotion-tag" onclick="selectFreq(this,'weekly')">Weekly</div>
                        </div>
                        <input type="hidden" name="frequency" id="freqInput" value="daily">
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="toggleHabitForm()">Cancel</button>
                        <button type="submit" class="btn-save">Add Habit</button>
                    </div>
                </form>
            </div>

            <!-- Today's Habits -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-title">Today's habits</div>
                    <span class="dash-badge-green"><?= $habits_done ?>/<?= $habits_total ?> done</span>
                </div>

                <?php if (empty($habits)): ?>
                <div style="text-align:center;padding:30px 0">
                    <div style="font-size:36px;margin-bottom:10px">🌱</div>
                    <div style="font-size:14px;font-weight:600;color:#1a2a3a;margin-bottom:6px">No habits yet</div>
                    <div style="font-size:13px;color:#9a9ab8;margin-bottom:16px">Add your first habit to get started.</div>
                    <button class="btn-page-action" onclick="toggleHabitForm()">Add your first habit</button>
                </div>
                <?php else: ?>
                <div style="display:flex;flex-direction:column;gap:8px">
                    <?php foreach ($habits as $habit):
                        $done = (bool)$habit['done_today'];
                    ?>
                    <div class="selfcare-habit-item <?= $done ? 'selfcare-habit-done' : '' ?>"
                         id="habit-row-<?= $habit['id'] ?>">
                        <div class="selfcare-habit-check <?= $done ? 'selfcare-habit-check-done' : '' ?>"
                             onclick="toggleHabit(<?= $habit['id'] ?>, <?= $done ? 'true' : 'false' ?>)"
                             style="cursor:pointer">
                            <?php if ($done): ?>
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <?php endif; ?>
                        </div>
                        <div style="flex:1">
                            <div class="selfcare-habit-name"><?= htmlspecialchars($habit['name']) ?></div>
                            <div class="selfcare-habit-freq"><?= ucfirst($habit['frequency']) ?> · <?= $done ? 'Completed ✓' : 'Pending' ?></div>
                        </div>
                        <form action="../includes/selfcare_handler.php" method="POST" style="margin:0" onsubmit="return confirm('Delete this habit?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="habit_id" value="<?= $habit['id'] ?>">
                            <button type="submit" class="selfcare-delete-btn">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

        </div>

        <!--  Weekly progress -->
        <div style="display:flex;flex-direction:column;gap:14px">

            <div class="dash-card">
                <div class="dash-card-title" style="margin-bottom:16px">Weekly progress</div>
                <?php if (empty($habits)): ?>
                <div style="text-align:center;padding:16px 0;font-size:13px;color:#9a9ab8">Add habits to see your weekly progress.</div>
                <?php else: ?>
                <div style="display:flex;flex-direction:column;gap:12px">
                    <?php foreach ($habits as $i => $habit):
                        $days_done = $weekly_progress[$habit['id']] ?? 0;
                        $target    = $habit['frequency'] === 'weekly' ? 1 : 7;
                        $pct       = $target > 0 ? min(100, round(($days_done / $target) * 100)) : 0;
                        $color     = $progress_colors[$i % count($progress_colors)];
                    ?>
                    <div>
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px">
                            <span style="font-size:12px;color:#6a7a9a;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($habit['name']) ?></span>
                            <span style="font-size:12px;font-weight:700;color:<?= $color ?>"><?= $days_done ?>/<?= $target ?></span>
                        </div>
                        <div style="height:8px;background:#f0eef8;border-radius:4px;overflow:hidden">
                            <div style="width:<?= $pct ?>%;height:100%;background:<?= $color ?>;border-radius:4px;transition:width 0.3s"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Tips Card -->
            <div class="dash-card" style="background:#2e5f8a;border-color:#2e5f8a">
                <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:10px">💡 Habit tip</div>
                <div style="font-size:12px;color:#a8c8e8;line-height:1.7">
                    Start with habits that take less than 5 minutes. Consistency beats intensity — a 2-minute breathing exercise done every day is more powerful than a 30-minute session done once a week.
                </div>
            </div>

            <!-- Progress Summary -->
            <div class="dash-card">
                <div class="dash-card-title" style="margin-bottom:14px">Today's summary</div>
                <div style="display:flex;align-items:center;gap:14px">
                    <div style="position:relative;width:80px;height:80px;flex-shrink:0">
                        <?php
                        $pct_done = $habits_total > 0 ? round(($habits_done / $habits_total) * 100) : 0;
                        $circle_pct = $habits_total > 0 ? ($habits_done / $habits_total) * 100 : 0;
                        $stroke_dash = round(($circle_pct / 100) * 188);
                        ?>
                        <svg width="80" height="80" viewBox="0 0 80 80">
                            <circle cx="40" cy="40" r="30" fill="none" stroke="#f0eef8" stroke-width="8"/>
                            <circle cx="40" cy="40" r="30" fill="none" stroke="#2e5f8a" stroke-width="8"
                                stroke-dasharray="<?= $stroke_dash ?> 188"
                                stroke-dashoffset="47"
                                stroke-linecap="round"
                                transform="rotate(-90 40 40)"/>
                        </svg>
                        <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center">
                            <div style="font-size:14px;font-weight:700;color:#2e5f8a"><?= $pct_done ?>%</div>
                        </div>
                    </div>
                    <div>
                        <div style="font-size:14px;font-weight:700;color:#1a2a3a;margin-bottom:4px"><?= $habits_done ?> of <?= $habits_total ?> completed</div>
                        <div style="font-size:12px;color:#7a90b8;line-height:1.6">
                            <?php if ($pct_done === 100): ?>
                                Amazing! All habits completed today! 🎉
                            <?php elseif ($pct_done >= 60): ?>
                                Great progress! Keep going!
                            <?php elseif ($pct_done > 0): ?>
                                Good start — you've got this!
                            <?php else: ?>
                                Start your first habit today!
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</main>

<script>
function toggleHabitForm() {
    const form = document.getElementById('habitForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
    if (form.style.display === 'block') form.scrollIntoView({behavior:'smooth', block:'start'});
}

function selectFreq(el, freq) {
    document.querySelectorAll('.emotion-tag').forEach(t => t.classList.remove('emotion-tag-selected'));
    el.classList.add('emotion-tag-selected');
    document.getElementById('freqInput').value = freq;
}

function toggleHabit(habitId, isDone) {
    const action = isDone ? 'uncomplete' : 'complete';
    fetch('../includes/habit_handler.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'habit_id=' + habitId + '&action=' + action
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