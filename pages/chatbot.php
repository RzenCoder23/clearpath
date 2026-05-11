<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_name  = $_SESSION['user_name'] ?? 'User';
$first_name = explode(' ', trim($user_name))[0];

$suggested = [
    'How can I manage my anxiety?',
    'Tips for better sleep',
    'How do I start meditating?',
    'I\'m feeling overwhelmed',
    'Help me build a self-care routine',
    'What features does ClearPath have?',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wellness Assistant — ClearPath</title>
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

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
        <div>
            <h1 class="page-title">Wellness Assistant</h1>
            <p class="page-sub">Your personal mental wellness companion</p>
        </div>
        <div style="display:flex;align-items:center;gap:8px">
            <div style="width:8px;height:8px;border-radius:50%;background:#4caf82"></div>
            <span style="font-size:12px;color:#4caf82;font-weight:600">Online</span>
        </div>
    </div>

    <div class="chatbot-layout">

  
        <div class="chatbot-window">

            <div class="chatbot-messages" id="chatMessages">

                <!-- welcome message -->
                <div class="chat-msg chat-msg-ai">
                    <div class="chat-avatar chat-avatar-ai">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                    </div>
                    <div class="chat-bubble chat-bubble-ai">
                        <div class="chat-sender">ClearPath Assistant</div>
                        <div class="chat-text">Hi <?= htmlspecialchars($first_name) ?>! 👋 I'm your ClearPath wellness assistant. I'm here to support your mental wellbeing journey, whether you need tips on managing stress, building better habits, or just want someone to talk to.<br><br>How are you feeling today, and is there anything I can help you with?</div>
                    </div>
                </div>

            </div>


            <div class="chat-typing" id="typingIndicator" style="display:none">
                <div class="chat-avatar chat-avatar-ai">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                </div>
                <div class="chat-bubble chat-bubble-ai" style="padding:12px 16px">
                    <div class="typing-dots">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            </div>

            <!-- input -->
            <div class="chatbot-input-wrap">
                <input type="text" id="chatInput" class="chatbot-input" placeholder="Ask me anything about your wellness..." onkeydown="if(event.key==='Enter') sendMessage()">
                <button class="chatbot-send-btn" onclick="sendMessage()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                </button>
            </div>

        </div>

        <!-- right panel -->
        <div style="display:flex;flex-direction:column;gap:12px">

            <!-- suggested questions -->
            <div class="dash-card">
                <div class="dash-card-title" style="margin-bottom:12px">Suggested questions</div>
                <div style="display:flex;flex-direction:column;gap:7px">
                    <?php foreach ($suggested as $q): ?>
                    <div class="chatbot-suggestion" onclick="sendSuggestion(this.textContent.trim())"><?= htmlspecialchars($q) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>


            <div style="background:#fff;border-radius:14px;border:0.5px solid #e0d8f8;padding:16px 18px">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#b8a8d8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <div style="font-size:13px;font-weight:700;color:#1a2a3a">Important notice</div>
                </div>
                <div style="font-size:12px;color:#7a90b8;line-height:1.7">
                    This assistant provides general wellness guidance only and is not a substitute for professional mental health support.
                </div>
                <div style="margin-top:12px;padding:10px 12px;background:#f5f3ff;border-radius:8px;display:flex;align-items:center;gap:8px">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#6a4a9a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0">
                        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/>
                    </svg>
                    <span style="font-size:12px;color:#1a2a3a">In crisis? Call <strong>116 123</strong> · Samaritans</span>
                </div>
            </div>

        </div>
    </div>

</main>

<script>
// Conversation history for context
let conversationHistory = [];
const userName = '<?= htmlspecialchars($first_name) ?>';

function sendSuggestion(text) {
    document.getElementById('chatInput').value = text;
    sendMessage();
}

function sendMessage() {
    const input  = document.getElementById('chatInput');
    const text   = input.value.trim();
    if (!text) return;
    input.value  = '';

    appendMessage('user', text);
    conversationHistory.push({ role: 'user', content: text });

    showTyping();

    fetch('../includes/chatbot_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            message: text,
            history: conversationHistory,
            user_name: userName
        })
    })
    .then(res => res.json())
    .then(data => {
        hideTyping();
        const reply = data.reply || 'Sorry, I had trouble responding. Please try again.';
        appendMessage('ai', reply);
        conversationHistory.push({ role: 'assistant', content: reply });
    })
    .catch(() => {
        hideTyping();
        appendMessage('ai', 'Sorry, I\'m having trouble connecting right now. Please try again in a moment.');
    });
}

function appendMessage(role, text) {
    const container = document.getElementById('chatMessages');
    const div = document.createElement('div');
    div.className = `chat-msg chat-msg-${role}`;

    const avatar = document.createElement('div');
    avatar.className = `chat-avatar chat-avatar-${role}`;

    if (role === 'ai') {
        avatar.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>`;
    } else {
        avatar.textContent = userName.substring(0, 2).toUpperCase();
    }

    const bubble = document.createElement('div');
    bubble.className = `chat-bubble chat-bubble-${role}`;

    if (role === 'ai') {
        const sender = document.createElement('div');
        sender.className = 'chat-sender';
        sender.textContent = 'ClearPath Assistant';
        bubble.appendChild(sender);
    }

    const textDiv = document.createElement('div');
    textDiv.className = 'chat-text';
    textDiv.innerHTML = text.replace(/\n/g, '<br>');
    bubble.appendChild(textDiv);

    if (role === 'ai') {
        div.appendChild(avatar);
        div.appendChild(bubble);
    } else {
        div.appendChild(bubble);
        div.appendChild(avatar);
    }

    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
}

function showTyping() {
    document.getElementById('typingIndicator').style.display = 'flex';
    const container = document.getElementById('chatMessages');
    container.scrollTop = container.scrollHeight;
}

function hideTyping() {
    document.getElementById('typingIndicator').style.display = 'none';
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