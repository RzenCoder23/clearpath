<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — ClearPath</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="auth-page">

<header class="navbar">
    <div class="logo">ClearPath</div>
    <nav>
        <a href="../index.php">Home</a>
        <a href="../index.php#features">Features</a>
        <a href="../index.php#about">About Us</a>
        <a href="login.php" class="nav-btn btn-login">Login</a>
        <a href="register.php" class="nav-btn btn-register">Get Started</a>
    </nav>
</header>

<div class="auth-wrapper">
    <div class="auth-box auth-box-wide">

        <div class="auth-icon auth-icon-lavender">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#b8a8d8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
        </div>

        <h2>Create your account</h2>
        <p class="subtitle">Start your wellness journey today — it's free</p>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <form action="../includes/register_handler.php" method="POST">

            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First name</label>
                    <input type="text" id="first_name" name="first_name" placeholder="Jane" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last name</label>
                    <input type="text" id="last_name" name="last_name" placeholder="Doe" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" placeholder="your@email.com" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-icon-wrap">
                        <input type="password" id="password" name="password" placeholder="Create password" required>
                        <span class="input-icon" onclick="togglePassword('password', this)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm password</label>
                    <div class="input-icon-wrap">
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat password" required>
                        <span class="input-icon" onclick="togglePassword('confirm_password', this)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </span>
                    </div>
                </div>
            </div>

            <div class="password-rules">
                <div class="password-rules-title">Password must include:</div>
                <div class="password-rules-list">
                    <span class="rule" id="rule-length">
                        <span class="rule-dot"></span> At least 8 characters
                    </span>
                    <span class="rule" id="rule-upper">
                        <span class="rule-dot"></span> One uppercase letter
                    </span>
                    <span class="rule" id="rule-number">
                        <span class="rule-dot"></span> One number
                    </span>
                </div>
            </div>

            <div class="form-check">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">I agree to ClearPath's <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
            </div>

            <button type="submit" class="btn-auth btn-auth-lavender">Create Account</button>

        </form>

        <p class="auth-switch">Already have an account? <a href="login.php">Sign in here</a></p>

    </div>
</div>

<script>
function togglePassword(id, icon) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.style.color = input.type === 'text' ? '#b8a8d8' : '#9a9ab8';
}

document.getElementById('password').addEventListener('input', function() {
    const val = this.value;
    const length = document.getElementById('rule-length');
    const upper  = document.getElementById('rule-upper');
    const number = document.getElementById('rule-number');
    length.classList.toggle('rule-pass', val.length >= 8);
    upper.classList.toggle('rule-pass',  /[A-Z]/.test(val));
    number.classList.toggle('rule-pass', /[0-9]/.test(val));
});
</script>

</body>
</html>