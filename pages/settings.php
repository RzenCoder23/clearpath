<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../includes/db.php';

$user_name  = $_SESSION['user_name'] ?? 'User';
$first_name = explode(' ', trim($user_name))[0];


$user = null;
try {
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, created_at FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch (Exception $e) {}

if (!$user) {
    header('Location: ../includes/logout.php');
    exit();
}

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';
$success    = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error      = isset($_GET['error'])   ? htmlspecialchars($_GET['error'])   : '';

$member_since = date('F Y', strtotime($user['created_at']));
$initials     = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings — ClearPath</title>
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
        <h1 class="page-title">Settings</h1>
        <p class="page-sub">Manage your account and preferences</p>
    </div>

    <?php if ($success): ?>
    <div class="alert alert-success" style="margin-bottom:16px"><?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="alert alert-error" style="margin-bottom:16px"><?= $error ?></div>
    <?php endif; ?>

    <div class="settings-layout">

        <!-- Sidebar -->
        <div class="settings-sidebar">
            <a href="settings.php?tab=profile"      class="settings-tab <?= $active_tab === 'profile'      ? 'settings-tab-active' : '' ?>">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Profile
            </a>
            <a href="settings.php?tab=password"     class="settings-tab <?= $active_tab === 'password'     ? 'settings-tab-active' : '' ?>">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                Password
            </a>
            <a href="settings.php?tab=preferences"  class="settings-tab <?= $active_tab === 'preferences'  ? 'settings-tab-active' : '' ?>">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                Preferences
            </a>
            <div class="settings-sidebar-divider"></div>
            <a href="settings.php?tab=delete"       class="settings-tab settings-tab-danger <?= $active_tab === 'delete' ? 'settings-tab-danger-active' : '' ?>">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                Delete Account
            </a>
        </div>

        <!-- Content -->
        <div class="settings-content">

            <!-- ── PROFILE TAB ── -->
            <?php if ($active_tab === 'profile'): ?>
            <div class="dash-card">
                <!-- Avatar banner -->
                <div class="settings-profile-banner">
                    <div class="settings-avatar"><?= $initials ?></div>
                    <div>
                        <div class="settings-fullname"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
                        <div class="settings-email-display"><?= htmlspecialchars($user['email']) ?></div>
                        <div class="settings-member-since">Member since <?= $member_since ?></div>
                    </div>
                </div>

                <div class="settings-section-title">Profile Information</div>
                <form action="../includes/settings_handler.php" method="POST">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="form-row">
                        <div class="form-group">
                            <label>First name</label>
                            <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Last name</label>
                            <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email address</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-save">Save Changes</button>
                    </div>
                </form>
            </div>

            <!-- ── PASSWORD TAB ── -->
            <?php elseif ($active_tab === 'password'): ?>
            <div class="dash-card">
                <div class="settings-section-title" style="margin-bottom:16px">Change Password</div>
                <form action="../includes/settings_handler.php" method="POST">
                    <input type="hidden" name="action" value="update_password">
                    <div class="form-group">
                        <label>Current password</label>
                        <div class="input-icon-wrap">
                            <input type="password" name="current_password" placeholder="Enter your current password" required>
                            <span class="input-icon" onclick="togglePw('current_password',this)">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>New password</label>
                            <div class="input-icon-wrap">
                                <input type="password" name="new_password" id="new_password" placeholder="New password" required>
                                <span class="input-icon" onclick="togglePw('new_password',this)">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Confirm new password</label>
                            <div class="input-icon-wrap">
                                <input type="password" name="confirm_password" placeholder="Confirm password" required>
                                <span class="input-icon" onclick="togglePw('confirm_password',this)">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="password-rules">
                        <div class="password-rules-title">Password must include:</div>
                        <div class="password-rules-list">
                            <span class="rule" id="rule-length"><span class="rule-dot"></span>At least 8 characters</span>
                            <span class="rule" id="rule-upper"><span class="rule-dot"></span>One uppercase letter</span>
                            <span class="rule" id="rule-number"><span class="rule-dot"></span>One number</span>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-auth btn-auth-lavender" style="width:auto;padding:10px 24px;margin:0">Update Password</button>
                    </div>
                </form>
            </div>

            <!-- ── Preferences tab ── -->
            <?php elseif ($active_tab === 'preferences'): ?>
            <div class="dash-card">
                <div class="settings-section-title" style="margin-bottom:16px">Preferences</div>

                <div class="settings-pref-row">
                    <div>
                        <div class="settings-pref-title">Daily mood reminder</div>
                        <div class="settings-pref-sub">Get a gentle nudge to log your mood each day</div>
                    </div>
                    <label class="settings-toggle">
                        <input type="checkbox" checked>
                        <span class="settings-toggle-slider"></span>
                    </label>
                </div>

                <div class="settings-pref-row">
                    <div>
                        <div class="settings-pref-title">Journal reminder</div>
                        <div class="settings-pref-sub">Reminder to write in your journal each evening</div>
                    </div>
                    <label class="settings-toggle">
                        <input type="checkbox">
                        <span class="settings-toggle-slider"></span>
                    </label>
                </div>

                <div class="settings-pref-row">
                    <div>
                        <div class="settings-pref-title">Weekly wellness summary</div>
                        <div class="settings-pref-sub">Receive a summary of your weekly mood and habits</div>
                    </div>
                    <label class="settings-toggle">
                        <input type="checkbox" checked>
                        <span class="settings-toggle-slider"></span>
                    </label>
                </div>

                <div class="settings-pref-row" style="border-bottom:none">
                    <div>
                        <div class="settings-pref-title">Data privacy</div>
                        <div class="settings-pref-sub">Your data is stored securely and never shared with third parties</div>
                    </div>

                </div>

                <div style="margin-top:16px;padding-top:16px;border-top:0.5px solid #e0d8f8">
                    <div class="settings-section-title" style="margin-bottom:12px">Account information</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                        <div style="background:#f5f3ff;border-radius:10px;padding:14px;border:0.5px solid #e0d8f8">
                            <div style="font-size:11px;color:#7a90b8;margin-bottom:4px">Account ID</div>
                            <div style="font-size:13px;font-weight:700;color:#1a2a3a">#<?= str_pad($_SESSION['user_id'], 5, '0', STR_PAD_LEFT) ?></div>
                        </div>
                        <div style="background:#f5f3ff;border-radius:10px;padding:14px;border:0.5px solid #e0d8f8">
                            <div style="font-size:11px;color:#7a90b8;margin-bottom:4px">Member since</div>
                            <div style="font-size:13px;font-weight:700;color:#1a2a3a"><?= $member_since ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Delte tab ── -->
            <?php elseif ($active_tab === 'delete'): ?>
            <div class="dash-card">
                <div style="text-align:center;padding:20px 0 10px">
                    <div style="font-size:40px;margin-bottom:12px">⚠️</div>
                    <div style="font-size:18px;font-weight:700;color:#e05c6a;margin-bottom:8px">Delete Account</div>
                    <div style="font-size:13px;color:#7a90b8;max-width:420px;margin:0 auto;line-height:1.7">This will permanently delete your account and all your data including mood logs, journal entries, habits and calendar events. This action cannot be undone.</div>
                </div>

                <div style="background:#fdeef0;border-radius:10px;border-left:3px solid #e05c6a;padding:14px 16px;margin:20px 0">
                    <div style="font-size:12px;font-weight:700;color:#a03040;margin-bottom:4px">You will lose all of the following permanently:</div>
                    <div style="font-size:12px;color:#a03040;line-height:1.8">
                        All mood logs and history &nbsp;·&nbsp; All journal entries &nbsp;·&nbsp; All self-care habits &nbsp;·&nbsp; All calendar events &nbsp;·&nbsp; Your account and profile
                    </div>
                </div>

                <form action="../includes/settings_handler.php" method="POST">
                    <input type="hidden" name="action" value="delete_account">
                    <div class="form-group">
                        <label>Enter your password to confirm</label>
                        <input type="password" name="confirm_password" placeholder="Your current password" required>
                    </div>
                    <div class="form-group">
                        <label>Type <strong>DELETE</strong> to confirm</label>
                        <input type="text" name="confirm_text" placeholder="Type DELETE here" required>
                    </div>
                    <div class="form-actions">
                        <a href="settings.php" class="btn-cancel" style="text-decoration:none;display:inline-flex;align-items:center">Cancel</a>
                        <button type="submit" style="padding:10px 22px;border-radius:8px;border:none;background:#e05c6a;font-size:13px;font-weight:700;color:#fff;cursor:pointer">Permanently Delete Account</button>
                    </div>
                </form>
            </div>
            <?php endif; ?>

        </div>
    </div>

</main>

<script>
function togglePw(id, icon) {
    const input = document.getElementById(id) || document.querySelector(`[name="${id}"]`);
    if (!input) return;
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.style.color = input.type === 'text' ? '#2e5f8a' : '#9a9ab8';
}

document.addEventListener('DOMContentLoaded', function() {
    const pw = document.getElementById('new_password');
    if (pw) {
        pw.addEventListener('input', function() {
            const v = this.value;
            const rl = document.getElementById('rule-length');
            const ru = document.getElementById('rule-upper');
            const rn = document.getElementById('rule-number');
            if (rl) rl.classList.toggle('rule-pass', v.length >= 8);
            if (ru) ru.classList.toggle('rule-pass', /[A-Z]/.test(v));
            if (rn) rn.classList.toggle('rule-pass', /[0-9]/.test(v));
        });
    }
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