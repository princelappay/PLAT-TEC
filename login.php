<?php
session_start();
include "config.php";

if (isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT id, password, role FROM users WHERE username = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        $password_ok = password_verify($password, $user['password']);

        // Legacy fallback: support plain-text values and upgrade them to a hash.
        if (!$password_ok && hash_equals((string)$user['password'], (string)$password)) {
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            $rehash_stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
            mysqli_stmt_bind_param($rehash_stmt, "si", $new_hash, $user['id']);
            mysqli_stmt_execute($rehash_stmt);
            mysqli_stmt_close($rehash_stmt);
            $password_ok = true;
        }

        if ($password_ok) {
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['role'] = $user['role'];

            if ($_SESSION['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        }
    }
    mysqli_stmt_close($stmt);

    // Admin compatibility fallback: some setups only have credentials in admins table.
    $admin_stmt = mysqli_prepare($conn, "SELECT id, password FROM admins WHERE username = ? LIMIT 1");
    if ($admin_stmt) {
        mysqli_stmt_bind_param($admin_stmt, "s", $username);
        mysqli_stmt_execute($admin_stmt);
        $admin_result = mysqli_stmt_get_result($admin_stmt);

        if ($admin = mysqli_fetch_assoc($admin_result)) {
            if (password_verify($password, $admin['password']) || hash_equals((string)$admin['password'], (string)$password)) {
                // Ensure matching admin exists in users table for the rest of the app.
                $admin_hash = password_hash($password, PASSWORD_DEFAULT);
                $upsert_sql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'admin') ON DUPLICATE KEY UPDATE password = VALUES(password), role = 'admin'";
                $upsert_stmt = mysqli_prepare($conn, $upsert_sql);
                mysqli_stmt_bind_param($upsert_stmt, "ss", $username, $admin_hash);
                mysqli_stmt_execute($upsert_stmt);
                mysqli_stmt_close($upsert_stmt);

                $user_stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? LIMIT 1");
                mysqli_stmt_bind_param($user_stmt, "s", $username);
                mysqli_stmt_execute($user_stmt);
                $user_row = mysqli_fetch_assoc(mysqli_stmt_get_result($user_stmt));
                mysqli_stmt_close($user_stmt);

                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = (int)($user_row['id'] ?? 0);
                $_SESSION['role'] = 'admin';
                header("Location: admin_dashboard.php");
                exit();
            }
        }
        mysqli_stmt_close($admin_stmt);
    }

    $error = "Invalid credentials.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Elite Drive</title>
    <link rel="stylesheet" href="style.css?v=20260410">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .auth-page {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 2rem 1rem;
            background:
                radial-gradient(circle at 12% 8%, rgba(215, 174, 92, 0.18), transparent 28%),
                radial-gradient(circle at 90% 92%, rgba(61, 220, 151, 0.12), transparent 26%),
                var(--bg);
        }

        .auth-shell {
            width: min(980px, 100%);
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            background: rgba(20, 22, 29, 0.86);
            border: 1px solid var(--border);
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(8px);
        }

        .auth-showcase {
            position: relative;
            padding: 2.2rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 520px;
            background:
                linear-gradient(145deg, rgba(11, 15, 24, 0.94), rgba(17, 25, 40, 0.92)),
                url('https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;
        }

        .auth-showcase::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.08), rgba(0, 0, 0, 0.52));
            pointer-events: none;
        }

        .showcase-content,
        .showcase-footer {
            position: relative;
            z-index: 1;
        }

        .brand-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .brand-row .logo {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .brand-title {
            color: #f8e2a8;
            letter-spacing: 0.6px;
            font-weight: 800;
        }

        .showcase-headline {
            color: #ffffff;
            font-size: clamp(1.8rem, 3.2vw, 2.5rem);
            line-height: 1.2;
            margin: 0.2rem 0 0.7rem;
        }

        .showcase-subtext {
            color: #d2d9e8;
            max-width: 420px;
            margin-bottom: 1.2rem;
        }

        .showcase-tags {
            display: flex;
            gap: 0.6rem;
            flex-wrap: wrap;
        }

        .showcase-tag {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #f4f7ff;
            font-size: 0.82rem;
            padding: 0.38rem 0.7rem;
            border-radius: 999px;
        }

        .showcase-footer {
            color: #c6cfdf;
            font-size: 0.92rem;
        }

        .auth-panel {
            padding: 2.2rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .panel-title {
            color: var(--text);
            font-size: 2rem;
            margin-bottom: 0.35rem;
        }

        .panel-subtitle {
            color: var(--text-muted);
            margin-bottom: 1.4rem;
        }

        .auth-alert {
            margin-bottom: 1rem;
            border-radius: 12px;
            padding: 0.8rem 1rem;
            border: 1px solid rgba(255, 107, 107, 0.5);
            background: rgba(255, 107, 107, 0.12);
            color: #ffd6d6;
        }

        .auth-form {
            display: grid;
            gap: 0.95rem;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9aa4bb;
            font-size: 0.95rem;
        }

        .input-wrap input {
            margin: 0;
            padding: 0.9rem 0.9rem 0.9rem 2.45rem;
            background: rgba(25, 28, 37, 0.9);
            border-color: rgba(255, 255, 255, 0.12);
        }

        .password-wrap input {
            padding-right: 3.6rem;
        }

        .password-toggle {
            position: absolute;
            right: 2.15rem;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #9aa4bb;
            cursor: pointer;
            padding: 0.3rem;
            line-height: 1;
            font-size: 1rem;
        }

        .password-toggle:hover,
        .password-toggle:focus-visible {
            color: var(--primary);
            outline: none;
        }

        .auth-form .btn {
            margin-top: 0.35rem;
            padding: 0.88rem 1rem;
            font-size: 1rem;
        }

        .auth-register {
            margin-top: 1rem;
            color: var(--text-muted);
            text-align: center;
        }

        .auth-register a {
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
        }

        .auth-register a:hover {
            text-decoration: underline;
        }

        @media (max-width: 900px) {
            .auth-shell {
                grid-template-columns: 1fr;
            }

            .auth-showcase {
                min-height: 280px;
                padding: 1.5rem;
            }

            .auth-panel {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <main class="auth-page">
        <section class="auth-shell" data-aos="fade-up">
            <aside class="auth-showcase">
                <div class="showcase-content">
                    <div class="brand-row">
                        <img src="logo.png" alt="Elite Drive Logo" class="logo">
                        <span class="brand-title">ELITE DRIVE</span>
                    </div>
                    <h1 class="showcase-headline">Drive Premium.<br>Book In Seconds.</h1>
                    <p class="showcase-subtext">Access your account to unlock luxury rentals, instant booking updates, and seamless trip management.</p>
                    <div class="showcase-tags">
                        <span class="showcase-tag">Premium Fleet</span>
                        <span class="showcase-tag">Fast Booking</span>
                        <span class="showcase-tag">Trusted Service</span>
                    </div>
                </div>
                <p class="showcase-footer">Your next drive starts here.</p>
            </aside>

            <div class="auth-panel">
                <h2 class="panel-title">Welcome Back</h2>
                <p class="panel-subtitle">Sign in to continue your Elite Drive experience.</p>

                <?php if (isset($error)): ?>
                    <div class="auth-alert"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" class="auth-form">
                    <div class="input-wrap">
                        <i class="fa-regular fa-user"></i>
                        <input type="text" name="username" placeholder="Username" autocomplete="username" required>
                    </div>
                    <div class="input-wrap password-wrap">
                        <i class="fa-solid fa-lock"></i>
                        <input id="password-field" type="password" name="password" placeholder="Password" autocomplete="current-password" required>
                        <button type="button" class="password-toggle" id="password-toggle" aria-label="Show password" aria-pressed="false">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                    <button type="submit" name="login" value="1" class="btn btn-full">Login</button>
                </form>

                <p class="auth-register">New here? <a href="register.php">Create an account</a></p>
            </div>
        </section>
    </main>

    <script src="script.js?v=20260410"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordField = document.getElementById('password-field');
            const passwordToggle = document.getElementById('password-toggle');

            if (!passwordField || !passwordToggle) {
                return;
            }

            passwordToggle.addEventListener('click', function () {
                const isHidden = passwordField.type === 'password';
                passwordField.type = isHidden ? 'text' : 'password';
                passwordToggle.setAttribute('aria-pressed', String(isHidden));
                passwordToggle.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
                passwordToggle.innerHTML = isHidden
                    ? '<i class="fa-regular fa-eye-slash"></i>'
                    : '<i class="fa-regular fa-eye"></i>';
            });
        });
    </script>
</body>
</html>

