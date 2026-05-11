<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClearPath — Your Wellness Companion</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="page-wrap">

<!-- ── navbar ── -->
<header class="navbar">
    <div class="logo">ClearPath</div>
    <nav>
        <a href="#features">Features</a>
        <a href="#about">About Us</a>
    </nav>
    <div class="nav-actions">
        <a href="pages/login.php" class="nav-btn btn-login">Login</a>
        <a href="pages/register.php" class="nav-btn btn-register">Get Started</a>
    </div>
</header>

<!-- ── hero ── -->
<section class="hero">
    <div class="hero-content">
        <div class="hero-badge">Your Wellness Companion</div>
        <h1>Find Your Path to <span class="hero-highlight">Mental Clarity</span></h1>
        <p>ClearPath helps you build better emotional habits, track your mood daily, and access the support you need — all in one calm, supportive space.</p>
        <div class="hero-buttons">
            <a href="pages/register.php" class="btn-primary">Start for Free</a>
            <a href="#features" class="btn-secondary">See Features</a>
        </div>
    </div>
    <div class="hero-mockup">
        <div class="mockup-card mockup-card-back">
            <div class="mockup-card-label">Journal</div>
            <div class="mockup-line"></div>
            <div class="mockup-line mockup-line-short"></div>
        </div>
        <div class="mockup-card mockup-card-front">
            <div class="mockup-icon-wrap">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#b8a8d8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
            </div>
            <div class="mockup-card-label">Mood Insights</div>
            <div class="mockup-card-sub">Understand patterns in your emotional wellness</div>
            <div class="mockup-bars">
                <div class="mockup-bar" style="height:28px;background:#c8dff0"></div>
                <div class="mockup-bar" style="height:20px;background:#c8dff0"></div>
                <div class="mockup-bar" style="height:36px;background:#2e5f8a"></div>
                <div class="mockup-bar" style="height:18px;background:#c8dff0"></div>
                <div class="mockup-bar" style="height:30px;background:#c8dff0"></div>
            </div>
        </div>
    </div>
</section>

<!-- ── stats bar ── -->
<div class="stats-bar">
    <div class="stat"><div class="stat-n">100%</div><div class="stat-l">Free forever</div></div>
        <div class="stat"><div class="stat-n"> 0 Ads </div><div class="stat-l">no ads or trackers</div></div>
    <div class="stat"><div class="stat-n">6+</div><div class="stat-l">Wellness tools</div></div>
    <div class="stat"><div class="stat-n">24/7</div><div class="stat-l">Wellness Chatbot</div></div>

</div>

<!-- ── features ── -->
<section class="features" id="features">
    <div class="section-eyebrow">What We Offer</div>
    <h2 class="section-title">Everything you need for your wellness journey</h2>
    <p class="section-sub">Simple tools, meaningful results.</p>

    <div class="features-grid">

        <div class="feature-card">
            <div class="feature-icon-wrap blue-wrap">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2e5f8a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/><path d="M8 12l3 3 5-5"/></svg>
            </div>
            <div class="feature-its">It's</div>
            <div class="feature-name blue-name">Free to Use.</div>
            <div class="feature-desc">Completely free — no subscriptions, no hidden fees. Access every feature without spending a penny.</div>
        </div>

        <div class="feature-card">
            <div class="feature-icon-wrap lavender-wrap">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#b8a8d8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            </div>
            <div class="feature-its">It's</div>
            <div class="feature-name lavender-name">Personal.</div>
            <div class="feature-desc">Journal privately and track your mood daily. Every session feels uniquely yours.</div>
        </div>

        <div class="feature-card">
            <div class="feature-icon-wrap blue-wrap">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2e5f8a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="feature-its">It's</div>
            <div class="feature-name blue-name">Always Available.</div>
            <div class="feature-desc">Access ClearPath any time, from any device. Your companion is always there.</div>
        </div>

        <div class="feature-card">
            <div class="feature-icon-wrap lavender-wrap">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#b8a8d8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
            </div>
            <div class="feature-its">It's</div>
            <div class="feature-name lavender-name">Supportive.</div>
            <div class="feature-desc">Our chatbot guides you to the right resources whenever you need a gentle nudge.</div>
        </div>

        <div class="feature-card">
            <div class="feature-icon-wrap blue-wrap">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2e5f8a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="feature-its">It's</div>
            <div class="feature-name blue-name">Structured.</div>
            <div class="feature-desc">Plan your week with our self-care planner and calendar for lasting wellbeing.</div>
        </div>

        <div class="feature-card">
            <div class="feature-icon-wrap lavender-wrap">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#b8a8d8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            </div>
            <div class="feature-its">It's</div>
            <div class="feature-name lavender-name">Effective.</div>
            <div class="feature-desc">Built on evidence-informed principles that help you build real emotional habits over time.</div>
        </div>

    </div>
</section>

<!-- ── About section ── -->
<section class="about" id="about">
    <div class="section-eyebrow">Our Story</div>
    <h2 class="section-title">About Us</h2>
    <p class="section-sub">Built with purpose, designed with care.</p>

    <div class="about-content">
        <div class="about-image">
            <div class="about-image-inner">
                <div class="about-image-grid">
                    <div class="about-img-block"></div>
                    <div class="about-img-block"></div>
                    <div class="about-img-block"></div>
                    <div class="about-img-block"></div>
                </div>
            </div>
        </div>
        <div class="about-text">
            <h3>Our Mission</h3>
            <p>ClearPath was founded with a simple yet powerful vision: to make mental wellness support accessible to everyone, everywhere.</p>
            <p>We believe that emotional wellbeing shouldn't be a luxury. That's why clearpath is a completely free platform that combines evidence-based practices with tools to support you on your wellness journey.</p>
            <p> Lets help you build, better emotional habits, one day at a time.</p>
        </div>
    </div>

    <!-- Team -->
    <div class="team-section">
        <h3 class="team-title">Roles in future production team</h3>
        <p class="team-sub"> </p>
        <div class="team-grid">
            <div class="team-member">
                <div class="team-avatar" style="background:#2e5f8a"></div>
                <div class="team-name"></div>
                <div class="team-role">Developer</div>
                <div class="team-desc"></div>
            </div>
            <div class="team-member">
                <div class="team-avatar" style="background:#e8a44a"></div>
                <div class="team-name"></div>
                <div class="team-role">Clinical Advisor</div>
                <div class="team-desc"></div>
            </div>
            <div class="team-member">
                <div class="team-avatar" style="background:#b8a8d8"></div>
                <div class="team-name"></div>
                <div class="team-role">UX Designer</div>
                <div class="team-desc"></div>
            </div>
        </div>
    </div>

</section>

<!-- ── CTA ── -->
<section class="cta-banner">
    <h2>Ready to start your wellness journey?</h2>
    <p>Join ClearPath today — completely free, no subscription required.</p>
    <a href="pages/register.php" class="btn-cta">Create Your Account</a>
</section>

<!-- ── Footer ── -->
<footer>
    <div class="footer-inner">
        <div class="footer-logo">ClearPath</div>
        <nav class="footer-nav">
            <a href="#">Home</a>
            <a href="#features">Features</a>
            <a href="#about">About Us</a>
            <a href="pages/login.php">Login</a>
            <a href="pages/register.php">Register</a>
        </nav>
    </div>
    <div class="footer-copy">© 2026 ClearPath. All rights reserved.</div>
</footer>

</div>
</body>
</html>