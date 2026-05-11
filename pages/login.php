<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — ClearPath</title>
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
    <div class="auth-box">

        <div class="auth-icon auth-icon-blue">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2e5f8a" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>

        <h2>Welcome back</h2>
        <p class="subtitle">Sign in to your ClearPath account</p>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <form action="../includes/login_handler.php" method="POST">
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" placeholder="your@email.com" required>
            </div>

            <div class="form-group">
                <div class="form-label-row">
                    <label for="password">Password</label>
                    <a href="forgot_password.php" class="form-link">Forgot password?</a>
                </div>
                <div class="input-icon-wrap">
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <span class="input-icon" onclick="togglePassword('password', this)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </span>
                </div>
            </div>

            <div class="form-check">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Keep me signed in</label>
            </div>

            <button type="submit" class="btn-auth btn-auth-blue">Sign In</button>
        </form>

        <div class="auth-divider"><span>or</span></div>

        <button class="btn-google">
            <svg width="16" height="16" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
            Continue with Google
        </button>

        <p class="auth-switch">Don't have an account? <a href="register.php">Register here</a></p>

    </div>
</div>

<script>
function togglePassword(id, icon) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.style.color = input.type === 'text' ? '#2e5f8a' : '#9a9ab8';
}
</script>

</body>
</html>