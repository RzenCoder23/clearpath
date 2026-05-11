<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_name  = $_SESSION['user_name'] ?? 'User';
$first_name = explode(' ', trim($user_name))[0];

$articles = [
    [
        'id'         => 1,
        'title'      => 'Top tips to improve your mental wellbeing',
        'excerpt'    => 'Practical, evidence-based tips from the NHS covering how to challenge unhelpful thoughts, build mindfulness into daily life, improve sleep, and connect with others.',
        'category'   => 'Mental Wellness',
        'read_time'  => '6 min read',
        'source'     => 'NHS Every Mind Matters',
        'stripe'     => '#2e5f8a',
        'icon_bg'    => '#f0f5fb',
        'accent'     => '#1a4a7a',
        'image'      => 'article1.jpg',
        'url'        => 'https://www.nhs.uk/every-mind-matters/mental-wellbeing-tips/top-tips-to-improve-your-mental-wellbeing/',
    ],
    [
        'id'         => 2,
        'title'      => 'Journalling for mental health',
        'excerpt'    => 'An NHS-approved guide to how journalling can support self-awareness, reduce stress and help process difficult emotions, with practical advice for getting started.',
        'category'   => 'Journalling',
        'read_time'  => '7 min read',
        'source'     => 'Best for You (NHS)',
        'stripe'     => '#b8a8d8',
        'icon_bg'    => '#f0eef8',
        'accent'     => '#6a4a9a',
        'image'      => 'article2.jpg',
        'url'        => 'https://bestforyou.org.uk/guides/self-care-and-wellbeing/journalling/',
    ],
    [
        'id'         => 3,
        'title'      => 'Self-care tips',
        'excerpt'    => 'Mental Health Foundation guidance on building a self-care routine through movement, sleep, balanced diet, mindfulness and social connection — small changes that support mental wellbeing.',
        'category'   => 'Self-Care',
        'read_time'  => '5 min read',
        'source'     => 'Mental Health Foundation',
        'stripe'     => '#7ab0d4',
        'icon_bg'    => '#eef5fa',
        'accent'     => '#1a4a7a',
        'image'      => 'article3.jpg',
        'url'        => 'https://www.mentalhealth.org.uk/explore-mental-health/blogs/self-care-tips',
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wellbeing Articles — ClearPath</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .filter-pill {
            padding: 6px 13px;
            border-radius: 20px;
            font-size: 12px;
            cursor: pointer;
            background: white;
            color: #6a7a9a;
            border: 0.5px solid #e0d8f8;
            transition: all 0.15s ease;
            user-select: none;
        }
        .filter-pill:hover {
            border-color: #b8a8d8;
            color: #1a2a3a;
        }
        .filter-pill.active {
            background: #2e5f8a;
            color: white;
            border-color: #2e5f8a;
            font-weight: 600;
        }
        .empty-state {
            background: white;
            border-radius: 14px;
            border: 0.5px solid #e0d8f8;
            padding: 40px 20px;
            text-align: center;
            color: #9a9ab8;
            font-size: 13px;
        }
        .article-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .source-tag {
            font-size: 11px;
            color: #9a9ab8;
            font-weight: 500;
        }
        .external-icon {
            width: 11px;
            height: 11px;
            opacity: 0.7;
        }
    </style>
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
        <a href="articles.php"  class="dash-nav-link active">Articles</a>
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
        <h1 class="page-title">Wellbeing Articles</h1>
        <p class="page-sub">Curated reading from trusted UK mental health sources</p>
    </div>


    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:10px">
        <div style="display:flex;gap:6px;flex-wrap:wrap" id="filterPills">
            <span class="filter-pill active" data-filter="all">All</span>
            <span class="filter-pill" data-filter="Mental Wellness">Mental Wellness</span>
            <span class="filter-pill" data-filter="Journalling">Journalling</span>
            <span class="filter-pill" data-filter="Self-Care">Self-Care</span>
        </div>
        <div style="font-size:12px;color:#9a9ab8" id="articleCount"><?= count($articles) ?> articles</div>
    </div>

    <!-- articles list -->
    <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:24px" id="articlesList">
        <?php foreach ($articles as $article): ?>
        <div class="article-card" data-category="<?= htmlspecialchars($article['category']) ?>" style="background:white;border-radius:14px;border:0.5px solid #e0d8f8;overflow:hidden;display:grid;grid-template-columns:6px 110px 1fr">
            <div style="background:<?= $article['stripe'] ?>"></div>
            <div style="background:<?= $article['icon_bg'] ?>;overflow:hidden">
                <img src="../assets/article-images/<?= $article['image'] ?>" alt="" class="article-image" loading="lazy">
            </div>
            <div style="padding:18px 20px">
                <div style="display:flex;gap:10px;align-items:center;margin-bottom:8px;flex-wrap:wrap">
                    <span style="font-size:11px;font-weight:700;color:<?= $article['accent'] ?>;letter-spacing:0.5px;text-transform:uppercase"><?= $article['category'] ?></span>
                    <span style="color:#d4c8f0">·</span>
                    <span style="font-size:11px;color:#9a9ab8"><?= $article['read_time'] ?></span>
                    <span style="color:#d4c8f0">·</span>
                    <span class="source-tag"><?= htmlspecialchars($article['source']) ?></span>
                </div>
                <h3 style="font-size:16px;font-weight:700;color:#1a2a3a;line-height:1.4;margin:0 0 8px"><?= htmlspecialchars($article['title']) ?></h3>
                <p style="font-size:13px;color:#7a90b8;line-height:1.65;margin:0 0 12px"><?= htmlspecialchars($article['excerpt']) ?></p>
                <div style="display:flex;justify-content:flex-end">
                    <a href="<?= htmlspecialchars($article['url']) ?>" target="_blank" rel="noopener noreferrer" style="font-size:12px;font-weight:600;color:<?= $article['accent'] ?>;text-decoration:none;display:inline-flex;align-items:center;gap:4px">
                        Read article
                        <svg class="external-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>


        <div class="empty-state" id="emptyState" style="display:none">
            No articles in this category yet — check back soon.
        </div>
    </div>

    <!-- disclaimer -->
    <div style="background:#fff;border-radius:12px;border:0.5px solid #e0d8f8;padding:14px 18px;display:flex;align-items:flex-start;gap:10px">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#b8a8d8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <p style="font-size:12px;color:#7a90b8;line-height:1.7;margin:0;font-style:italic">Articles do not represent medical or clinical advice; they are merely links to external sources for general wellbeing information. The publications are not linked to ClearPath and may change. Contact your GP or a qualified expert if you have mental health issues. In an emergency, call 116 123 for Samaritans.</p>
    </div>

</main>

<script>

const pills      = document.querySelectorAll('.filter-pill');
const cards      = document.querySelectorAll('.article-card');
const countEl    = document.getElementById('articleCount');
const emptyState = document.getElementById('emptyState');

function applyFilter(filter) {
    let visibleCount = 0;
    cards.forEach(card => {
        const category = card.getAttribute('data-category');
        const show = (filter === 'all') || (category === filter);
        card.style.display = show ? 'grid' : 'none';
        if (show) visibleCount++;
    });

    countEl.textContent = visibleCount + (visibleCount === 1 ? ' article' : ' articles');
    emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
}

pills.forEach(pill => {
    pill.addEventListener('click', () => {
        pills.forEach(p => p.classList.remove('active'));
        pill.classList.add('active');
        applyFilter(pill.getAttribute('data-filter'));
    });
});

// profile> dropdown menu
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
