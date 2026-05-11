<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../includes/db.php';

$user_name  = $_SESSION['user_name'] ?? 'User';
$first_name = explode(' ', trim($user_name))[0];


$entries = [];
try {
    $stmt = $pdo->prepare("SELECT id, title, content, emotion_tag, created_at FROM journal_entries WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $entries = $stmt->fetchAll();
} catch (Exception $e) {}



$emotion_summary = [];
try {
    $stmt = $pdo->prepare("SELECT emotion_tag, COUNT(*) as count FROM journal_entries WHERE user_id = ? AND emotion_tag IS NOT NULL AND emotion_tag != '' GROUP BY emotion_tag ORDER BY count DESC LIMIT 5");
    $stmt->execute([$_SESSION['user_id']]);
    $emotion_summary = $stmt->fetchAll();
} catch (Exception $e) {}

$max_count = !empty($emotion_summary) ? $emotion_summary[0]['count'] : 1;

$emotion_colors = [
    'Content'   => '#b8a8d8',
    'Happy'     => '#4caf82',
    'Grateful'  => '#2e5f8a',
    'Anxious'   => '#e8a44a',
    'Stressed'  => '#e05c6a',
    'Sad'       => '#7ab0d4',
    'Resilient' => '#4caf82',
];


$emotion_tags = ['Content','Happy','Anxious','Sad','Grateful','Stressed','Resilient','Calm','Overwhelmed','Proud'];


$edit_entry = null;
$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
if ($edit_id) {
    try {
        $stmt = $pdo->prepare("SELECT id, title, content, emotion_tag FROM journal_entries WHERE id = ? AND user_id = ?");
        $stmt->execute([$edit_id, $_SESSION['user_id']]);
        $edit_entry = $stmt->fetch();
    } catch (Exception $e) {}
}

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error   = isset($_GET['error'])   ? htmlspecialchars($_GET['error'])   : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal — ClearPath</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="dashboard-page">

<header class="dash-navbar">
    <div class="dash-logo">ClearPath</div>
    <nav class="dash-nav">
        <a href="dashboard.php" class="dash-nav-link">Overview</a>
        <a href="mood.php"      class="dash-nav-link">Mood Tracker</a>
        <a href="journal.php"   class="dash-nav-link active">Journal</a>
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

    <div class="page-header">
        <div>
            <h1 class="page-title">My Journal</h1>
            <p class="page-sub">Your private space to reflect and grow</p>
        </div>
        <button class="btn-page-action" onclick="toggleForm()">+ New Entry</button>
    </div>

    <?php if ($success): ?>
    <div class="alert alert-success" style="margin-bottom:16px"><?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="alert alert-error" style="margin-bottom:16px"><?= $error ?></div>
    <?php endif; ?>

    <div class="journal-layout">

        <!-- Left column -->
        <div class="journal-left">

            <!-- write/dit entry Form -->
            <div class="dash-card" id="entryForm" style="<?= (!$edit_entry && !isset($_GET['new'])) ? 'display:none' : '' ?>">
                <div class="dash-card-title" style="margin-bottom:14px"><?= $edit_entry ? 'Edit Entry' : 'Write a new entry' ?></div>

                <form action="../includes/journal_handler.php" method="POST">
                    <?php if ($edit_entry): ?>
                    <input type="hidden" name="entry_id" value="<?= $edit_entry['id'] ?>">
                    <input type="hidden" name="action" value="edit">
                    <?php else: ?>
                    <input type="hidden" name="action" value="create">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Entry Title</label>
                        <input type="text" name="title" placeholder="Give your entry a title..." required value="<?= $edit_entry ? htmlspecialchars($edit_entry['title']) : '' ?>">
                    </div>

                    <div class="form-group">
                        <label>Write about your day</label>
                        <textarea name="content" class="journal-textarea" placeholder="How are you feeling? What happened today? What are you grateful for?" required><?= $edit_entry ? htmlspecialchars($edit_entry['content']) : '' ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>How are you feeling?</label>
                        <div class="emotion-tags">
                            <?php foreach ($emotion_tags as $tag):
                                $selected = ($edit_entry && $edit_entry['emotion_tag'] === $tag);
                            ?>
                            <div class="emotion-tag <?= $selected ? 'emotion-tag-selected' : '' ?>" onclick="selectEmotion(this, '<?= $tag ?>')"><?= $tag ?></div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="emotion_tag" id="emotionInput" value="<?= $edit_entry ? htmlspecialchars($edit_entry['emotion_tag']) : '' ?>">
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="toggleForm()">Cancel</button>
                        <button type="submit" class="btn-save"><?= $edit_entry ? 'Update Entry' : 'Save Entry' ?></button>
                    </div>
                </form>
            </div>

            <!-- Emotion Summary -->
            <div class="dash-card">
                <div class="dash-card-title" style="margin-bottom:14px">Emotion summary</div>
                <?php if (empty($emotion_summary)): ?>
                    <div style="text-align:center;padding:16px 0;font-size:13px;color:#9a9ab8">No entries yet — start writing to see your emotion patterns!</div>
                <?php else: ?>
                    <div style="display:flex;flex-direction:column;gap:10px">
                        <?php foreach ($emotion_summary as $em):
                            $pct   = round(($em['count'] / $max_count) * 100);
                            $color = $emotion_colors[$em['emotion_tag']] ?? '#b8a8d8';
                        ?>
                        <div style="display:flex;align-items:center;gap:10px">
                            <span style="font-size:12px;color:#6a7a9a;width:80px;flex-shrink:0"><?= htmlspecialchars($em['emotion_tag']) ?></span>
                            <div style="flex:1;height:8px;background:#f0eef8;border-radius:4px;overflow:hidden">
                                <div style="width:<?= $pct ?>%;height:100%;background:<?= $color ?>;border-radius:4px"></div>
                            </div>
                            <span style="font-size:11px;color:#9a9ab8;width:24px;text-align:right"><?= $em['count'] ?>x</span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>


            <div class="dash-card">
                <div class="dash-card-title" style="margin-bottom:14px">Journal stats</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                    <div style="background:#f5f3ff;border-radius:10px;padding:14px;text-align:center;border:0.5px solid #e0d8f8">
                        <div style="font-size:24px;font-weight:700;color:#2e5f8a"><?= count($entries) ?></div>
                        <div style="font-size:11px;color:#7a90b8;margin-top:3px">Total entries</div>
                    </div>
                    <div style="background:#f5f3ff;border-radius:10px;padding:14px;text-align:center;border:0.5px solid #e0d8f8">
                        <?php
                        $this_month = 0;
                        foreach ($entries as $e) {
                            if (date('Y-m', strtotime($e['created_at'])) === date('Y-m')) $this_month++;
                        }
                        ?>
                        <div style="font-size:24px;font-weight:700;color:#b8a8d8"><?= $this_month ?></div>
                        <div style="font-size:11px;color:#7a90b8;margin-top:3px">This month</div>
                    </div>
                </div>
            </div>

        </div>


        <div class="journal-right">
            <div style="font-size:14px;font-weight:700;color:#1a2a3a;margin-bottom:14px">
                Past entries <span style="font-size:12px;font-weight:400;color:#9a9ab8">(<?= count($entries) ?>)</span>
            </div>

            <?php if (empty($entries)): ?>
            <div class="dash-card" style="text-align:center;padding:40px 20px">
                <div style="font-size:36px;margin-bottom:12px">📝</div>
                <div style="font-size:14px;font-weight:600;color:#1a2a3a;margin-bottom:6px">No entries yet</div>
                <div style="font-size:13px;color:#9a9ab8;margin-bottom:16px">Start writing to track your emotional journey.</div>
                <button class="btn-page-action" onclick="toggleForm()">Write your first entry</button>
            </div>
            <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:10px">
                <?php foreach ($entries as $entry):
                    $tag_color = $emotion_colors[$entry['emotion_tag']] ?? '#b8a8d8';
                    $tag_bg    = in_array($entry['emotion_tag'], ['Anxious','Stressed']) ? '#fff8f0' : '#f0eef8';
                    $tag_text  = in_array($entry['emotion_tag'], ['Anxious','Stressed']) ? '#a06010' : '#6a4a9a';
                    $preview   = mb_substr(strip_tags($entry['content']), 0, 120) . (mb_strlen($entry['content']) > 120 ? '...' : '');
                ?>
                <div class="journal-entry-card" id="entry-<?= $entry['id'] ?>">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px">
                        <div style="font-size:13px;font-weight:700;color:#1a2a3a;flex:1;margin-right:10px"><?= htmlspecialchars($entry['title']) ?></div>
                        <?php if ($entry['emotion_tag']): ?>
                        <span style="font-size:10px;background:<?= $tag_bg ?>;color:<?= $tag_text ?>;padding:2px 9px;border-radius:8px;flex-shrink:0;font-weight:600"><?= htmlspecialchars($entry['emotion_tag']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div style="font-size:12px;color:#7a90b8;line-height:1.6;margin-bottom:10px"><?= htmlspecialchars($preview) ?></div>
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <span style="font-size:11px;color:#b8b8c8"><?= date('D j M Y', strtotime($entry['created_at'])) ?></span>
                        <div style="display:flex;gap:10px">
                            <a href="journal.php?edit=<?= $entry['id'] ?>" style="font-size:11px;color:#2e5f8a;font-weight:600;text-decoration:none">Edit</a>
                            <span style="font-size:11px;color:#e05c6a;font-weight:600;cursor:pointer" onclick="deleteEntry(<?= $entry['id'] ?>)">Delete</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

    </div>

</main>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.4);z-index:500;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:16px;padding:28px 32px;max-width:380px;width:90%;text-align:center">
        <div style="font-size:32px;margin-bottom:12px">🗑️</div>
        <div style="font-size:16px;font-weight:700;color:#1a2a3a;margin-bottom:8px">Delete this entry?</div>
        <div style="font-size:13px;color:#7a90b8;margin-bottom:24px">This action cannot be undone.</div>
        <div style="display:flex;gap:10px;justify-content:center">
            <button onclick="closeModal()" style="padding:10px 24px;border-radius:8px;border:1.5px solid #e0d8f8;background:#fff;font-size:13px;font-weight:600;color:#6a7a9a;cursor:pointer">Cancel</button>
            <button id="confirmDelete" style="padding:10px 24px;border-radius:8px;border:none;background:#e05c6a;font-size:13px;font-weight:700;color:#fff;cursor:pointer">Delete</button>
        </div>
    </div>
</div>

<script>
let pendingDeleteId = null;

function toggleForm() {
    const form = document.getElementById('entryForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
    if (form.style.display === 'block') form.scrollIntoView({behavior:'smooth', block:'start'});
}

function selectEmotion(el, tag) {
    document.querySelectorAll('.emotion-tag').forEach(t => t.classList.remove('emotion-tag-selected'));
    el.classList.add('emotion-tag-selected');
    document.getElementById('emotionInput').value = tag;
}

function deleteEntry(id) {
    pendingDeleteId = id;
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'flex';
}

function closeModal() {
    document.getElementById('deleteModal').style.display = 'none';
    pendingDeleteId = null;
}

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (!pendingDeleteId) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '../includes/journal_handler.php';
    form.innerHTML = `<input name="action" value="delete"><input name="entry_id" value="${pendingDeleteId}">`;
    document.body.appendChild(form);
    form.submit();
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

<?php if ($edit_entry): ?>
document.getElementById('entryForm').style.display = 'block';
<?php endif; ?>
</script>

</body>
</html>