<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../includes/db.php';

$user_name  = $_SESSION['user_name'] ?? 'User';
$first_name = explode(' ', trim($user_name))[0];


$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
$year  = isset($_GET['year'])  ? (int)$_GET['year']  : (int)date('Y');


if ($month < 1)  { $month = 12; $year--; }
if ($month > 12) { $month = 1;  $year++; }

$prev_month = $month - 1 < 1  ? 12 : $month - 1;
$prev_year  = $month - 1 < 1  ? $year - 1 : $year;
$next_month = $month + 1 > 12 ? 1  : $month + 1;
$next_year  = $month + 1 > 12 ? $year + 1 : $year;

$month_name   = date('F', mktime(0, 0, 0, $month, 1, $year));
$days_in_month = (int)date('t', mktime(0, 0, 0, $month, 1, $year));
$first_dow     = (int)date('N', mktime(0, 0, 0, $month, 1, $year)); // 1=Mon, 7=Sun
$today_date    = date('Y-m-d');


$events_by_day = [];
try {
    $stmt = $pdo->prepare("SELECT id, title, event_date, event_time, category FROM calendar_events WHERE user_id = ? AND MONTH(event_date) = ? AND YEAR(event_date) = ? ORDER BY event_time ASC");
    $stmt->execute([$_SESSION['user_id'], $month, $year]);
    foreach ($stmt->fetchAll() as $event) {
        $day = (int)date('j', strtotime($event['event_date']));
        $events_by_day[$day][] = $event;
    }
} catch (Exception $e) {}

// Fetch upcoming events
$upcoming = [];
try {
    $stmt = $pdo->prepare("SELECT id, title, event_date, event_time, category FROM calendar_events WHERE user_id = ? AND event_date >= CURDATE() ORDER BY event_date ASC, event_time ASC LIMIT 6");
    $stmt->execute([$_SESSION['user_id']]);
    $upcoming = $stmt->fetchAll();
} catch (Exception $e) {}

$category_colors = [
    'Wellness'  => ['bg' => '#f0eef8', 'text' => '#6a4a9a', 'dot' => '#b8a8d8'],
    'Therapy'   => ['bg' => '#e8f0fc', 'text' => '#1a4a7a', 'dot' => '#7ab0d4'],
    'Exercise'  => ['bg' => '#edf8f2', 'text' => '#2a7a54', 'dot' => '#4caf82'],
    'Personal'  => ['bg' => '#fff8f0', 'text' => '#a06010', 'dot' => '#e8a44a'],
    'Other'     => ['bg' => '#f5f3ff', 'text' => '#5a4a8a', 'dot' => '#b8a8d8'],
];

$avatar_colors = ['#2e5f8a','#b8a8d8','#7ab0d4','#4caf82','#e8a44a'];

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error   = isset($_GET['error'])   ? htmlspecialchars($_GET['error'])   : '';
$categories = ['Wellness','Therapy','Exercise','Personal','Other'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar — ClearPath</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="dashboard-page">

<header class="dash-navbar">
    <div class="dash-logo">ClearPath</div>
    <nav class="dash-nav">
        <a href="dashboard.php" class="dash-nav-link">Overview</a>
        <a href="mood.php"      class="dash-nav-link">Mood Tracker</a>
        <a href="journal.php"   class="dash-nav-link">Journal</a>
        <a href="selfcare.php"  class="dash-nav-link">Self-Care</a>
        <a href="calendar.php"  class="dash-nav-link active">Calendar</a>
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
            <h1 class="page-title">Calendar</h1>
            <p class="page-sub">Plan and track your wellness events</p>
        </div>
        <button class="btn-page-action" onclick="toggleEventForm()">+ Add Event</button>
    </div>

    <?php if ($success): ?>
    <div class="alert alert-success" style="margin-bottom:16px"><?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="alert alert-error" style="margin-bottom:16px"><?= $error ?></div>
    <?php endif; ?>

    <div class="calendar-layout">

        <!-- Calendar -->
        <div style="display:flex;flex-direction:column;gap:14px">

            <div class="dash-card">

                <div class="cal-nav">
                    <div style="display:flex;align-items:center;gap:10px">
                        <a href="calendar.php?month=<?= $prev_month ?>&year=<?= $prev_year ?>" class="cal-nav-btn">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                        </a>
                        <span class="cal-month-title"><?= $month_name ?> <?= $year ?></span>
                        <a href="calendar.php?month=<?= $next_month ?>&year=<?= $next_year ?>" class="cal-nav-btn">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                    </div>
                    <a href="calendar.php" class="cal-today-btn">Today</a>
                </div>

                <!-- Day headers -->
                <div class="cal-header-grid">
                    <div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div>
                    <div class="cal-weekend">Sat</div><div class="cal-weekend">Sun</div>
                </div>

                <!-- Calendar Grid -->
                <div class="cal-body-grid">
                    <?php
                    // Empty cells before first day
                    for ($i = 1; $i < $first_dow; $i++):
                    ?>
                    <div class="cal-cell cal-cell-empty"></div>
                    <?php endfor; ?>

                    <?php for ($d = 1; $d <= $days_in_month; $d++):
                        $date_str  = sprintf('%04d-%02d-%02d', $year, $month, $d);
                        $is_today  = ($date_str === $today_date);
                        $is_past   = ($date_str < $today_date);
                        $dow       = (int)date('N', mktime(0, 0, 0, $month, $d, $year));
                        $is_weekend = ($dow >= 6);
                        $has_events = !empty($events_by_day[$d]);
                    ?>
                    <div class="cal-cell <?= $is_today ? 'cal-cell-today' : '' ?> <?= $is_past ? 'cal-cell-past' : '' ?> <?= $is_weekend ? 'cal-cell-weekend' : '' ?>"
                         onclick="selectDay(<?= $d ?>, '<?= $date_str ?>')" style="cursor:pointer">
                        <div class="cal-cell-num <?= $is_today ? 'cal-cell-num-today' : '' ?>"><?= $d ?></div>
                        <?php if ($has_events): ?>
                            <?php foreach (array_slice($events_by_day[$d], 0, 2) as $ev):
                                $cat    = $ev['category'] ?: 'Other';
                                $colors = $category_colors[$cat] ?? $category_colors['Other'];
                            ?>
                            <div class="cal-event-pill" style="background:<?= $colors['bg'] ?>;color:<?= $colors['text'] ?>"><?= htmlspecialchars(mb_substr($ev['title'], 0, 12)) ?></div>
                            <?php endforeach; ?>
                            <?php if (count($events_by_day[$d]) > 2): ?>
                            <div class="cal-event-more">+<?= count($events_by_day[$d]) - 2 ?> more</div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Add event -->
            <div class="dash-card" id="eventForm" style="display:none">
                <div class="dash-card-title" style="margin-bottom:16px">Add new event</div>
                <form action="../includes/calendar_handler.php" method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="form-group">
                        <label>Event title</label>
                        <input type="text" name="title" id="eventTitle" placeholder="e.g. Therapy session" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="event_date" id="eventDate" value="<?= $today_date ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Time (optional)</label>
                            <input type="time" name="event_time">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description (optional)</label>
                        <input type="text" name="description" placeholder="Add a note...">
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <div class="emotion-tags">
                            <?php foreach ($categories as $cat): ?>
                            <div class="emotion-tag" onclick="selectCategory(this, '<?= $cat ?>')"><?= $cat ?></div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="category" id="categoryInput" value="Wellness">
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="toggleEventForm()">Cancel</button>
                        <button type="submit" class="btn-save">Save Event</button>
                    </div>
                </form>
            </div>

        </div>


        <div style="display:flex;flex-direction:column;gap:14px">


            <div class="dash-card">
                <div class="dash-card-title" style="margin-bottom:14px">Upcoming events</div>
                <?php if (empty($upcoming)): ?>
                <div style="text-align:center;padding:20px 0">
                    <div style="font-size:32px;margin-bottom:10px">📅</div>
                    <div style="font-size:13px;color:#9a9ab8;margin-bottom:12px">No upcoming events</div>
                    <button class="btn-page-action" onclick="toggleEventForm()" style="font-size:12px;padding:8px 16px">Add your first event</button>
                </div>
                <?php else: ?>
                <div style="display:flex;flex-direction:column;gap:8px">
                    <?php foreach ($upcoming as $i => $ev):
                        $cat    = $ev['category'] ?: 'Other';
                        $colors = $category_colors[$cat] ?? $category_colors['Other'];
                        $av_col = $avatar_colors[$i % count($avatar_colors)];
                        $ev_day = date('j', strtotime($ev['event_date']));
                        $ev_mon = strtoupper(date('M', strtotime($ev['event_date'])));
                        $is_today_ev = (date('Y-m-d', strtotime($ev['event_date'])) === $today_date);
                    ?>
                    <div style="display:flex;gap:10px;align-items:flex-start;padding:12px;border-radius:10px;background:#f5f3ff;border:0.5px solid #e0d8f8">
                        <div style="width:38px;height:38px;border-radius:9px;background:<?= $av_col ?>;flex-shrink:0;display:flex;flex-direction:column;align-items:center;justify-content:center">
                            <div style="font-size:11px;font-weight:700;color:#fff"><?= $ev_day ?></div>
                            <div style="font-size:8px;color:rgba(255,255,255,0.7)"><?= $ev_mon ?></div>
                        </div>
                        <div style="flex:1">
                            <div style="font-size:12px;font-weight:700;color:#1a2a3a"><?= htmlspecialchars($ev['title']) ?></div>
                            <div style="font-size:10px;color:#7a90b8;margin-top:2px">
                                <?= $is_today_ev ? 'Today' : date('D j M', strtotime($ev['event_date'])) ?>
                                <?= $ev['event_time'] ? ' · ' . date('g:i A', strtotime($ev['event_time'])) : '' ?>
                                <?= $ev['category'] ? ' · ' . htmlspecialchars($ev['category']) : '' ?>
                            </div>
                        </div>
                        <form action="../includes/calendar_handler.php" method="POST" style="margin:0">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="event_id" value="<?= $ev['id'] ?>">
                            <button type="submit" onclick="return confirm('Delete this event?')" style="border:none;background:none;color:#e05c6a;font-size:14px;cursor:pointer;padding:0;line-height:1">✕</button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Category Legend -->
            <div class="dash-card">
                <div class="dash-card-title" style="margin-bottom:12px">Categories</div>
                <div style="display:flex;flex-direction:column;gap:8px">
                    <?php foreach ($category_colors as $cat => $colors): ?>
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="width:10px;height:10px;border-radius:50%;background:<?= $colors['dot'] ?>;flex-shrink:0"></div>
                        <span style="font-size:12px;color:#6a7a9a"><?= $cat ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Month Stats -->
            <div class="dash-card">
                <div class="dash-card-title" style="margin-bottom:12px"><?= $month_name ?> overview</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                    <?php
                    $total_month = array_sum(array_map('count', $events_by_day));
                    $past_count  = 0;
                    foreach ($events_by_day as $d => $evs) {
                        $ds = sprintf('%04d-%02d-%02d', $year, $month, $d);
                        if ($ds < $today_date) $past_count += count($evs);
                    }
                    ?>
                    <div style="background:#f5f3ff;border-radius:10px;padding:14px;text-align:center;border:0.5px solid #e0d8f8">
                        <div style="font-size:24px;font-weight:700;color:#2e5f8a"><?= $total_month ?></div>
                        <div style="font-size:11px;color:#7a90b8;margin-top:3px">Total events</div>
                    </div>
                    <div style="background:#f5f3ff;border-radius:10px;padding:14px;text-align:center;border:0.5px solid #e0d8f8">
                        <div style="font-size:24px;font-weight:700;color:#b8a8d8"><?= $total_month - $past_count ?></div>
                        <div style="font-size:11px;color:#7a90b8;margin-top:3px">Upcoming</div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</main>

<script>
function toggleEventForm() {
    const form = document.getElementById('eventForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
    if (form.style.display === 'block') form.scrollIntoView({behavior:'smooth', block:'start'});
}

function selectCategory(el, cat) {
    document.querySelectorAll('.emotion-tag').forEach(t => t.classList.remove('emotion-tag-selected'));
    el.classList.add('emotion-tag-selected');
    document.getElementById('categoryInput').value = cat;
}

function selectDay(day, dateStr) {
    document.getElementById('eventForm').style.display = 'block';
    document.getElementById('eventDate').value = dateStr;
    document.getElementById('eventForm').scrollIntoView({behavior:'smooth', block:'start'});
    document.getElementById('eventTitle').focus();
}


document.addEventListener('DOMContentLoaded', function() {
    const first = document.querySelector('.emotion-tag');
    if (first) { first.classList.add('emotion-tag-selected'); }
});

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