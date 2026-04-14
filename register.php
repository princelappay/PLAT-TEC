<?php
include "config.php";

if (isset($_POST['register'])) {
    $username = trim($_POST['username'] ?? '');
    $password_raw = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($username === '' || $password_raw === '') {
        $error = "Username and password are required.";
    } elseif (strlen($password_raw) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password_raw !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $password = password_hash($password_raw, PASSWORD_DEFAULT);

        // Prepared statement to prevent SQL Injection
        $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: login.php?success=account_created");
            exit();
        }

        $error = "Error: Username already taken.";
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Elite Drive</title>
    <link rel="stylesheet" href="style.css?v=20260410">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .auth-page {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 2rem 1rem;
            background:
                radial-gradient(circle at 10% 10%, rgba(215, 174, 92, 0.18), transparent 30%),
                radial-gradient(circle at 92% 88%, rgba(61, 220, 151, 0.12), transparent 26%),
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
            min-height: 560px;
            background:
                linear-gradient(145deg, rgba(11, 15, 24, 0.94), rgba(17, 25, 40, 0.92)),
                url('https://images.unsplash.com/photo-1550355291-bbee04a92027?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;
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
            margin-bottom: 1.2rem;
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
            gap: 0.9rem;
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

        .auth-form .btn {
            margin-top: 0.35rem;
            padding: 0.88rem 1rem;
            font-size: 1rem;
        }

        .auth-login {
            margin-top: 1rem;
            color: var(--text-muted);
            text-align: center;
        }

        .auth-login a {
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
        }

        .auth-login a:hover {
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
                    <h1 class="showcase-headline">Create Your Account.<br>Start Your Ride.</h1>
                    <p class="showcase-subtext">Join Elite Drive to reserve premium vehicles, manage bookings, and get instant notifications.</p>
                    <div class="showcase-tags">
                        <span class="showcase-tag">Quick Signup</span>
                        <span class="showcase-tag">Secure Access</span>
                        <span class="showcase-tag">Premium Cars</span>
                    </div>
                </div>
                <p class="showcase-footer">Registration takes less than a minute.</p>
            </aside>

            <div class="auth-panel">
                <h2 class="panel-title">Create Account</h2>
                <p class="panel-subtitle">Set up your Elite Drive profile to continue.</p>

                <?php if (isset($error)): ?>
                    <div class="auth-alert"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" class="auth-form">
                    <div class="input-wrap">
                        <i class="fa-regular fa-user"></i>
                        <input type="text" name="username" placeholder="Username" autocomplete="username" required>
                    </div>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" placeholder="Password (min 6 chars)" autocomplete="new-password" minlength="6" required>
                    </div>
                    <div class="input-wrap">
                        <i class="fa-solid fa-check-double"></i>
                        <input type="password" name="confirm_password" placeholder="Confirm Password" autocomplete="new-password" minlength="6" required>
                    </div>
                    <button type="submit" name="register" value="1" class="btn btn-full">Create Account</button>
                </form>

                <p class="auth-login">Already have an account? <a href="login.php">Login</a></p>
            </div>
        </section>
    </main>

    <script src="script.js?v=20260410"></script>
</body>
</html>
