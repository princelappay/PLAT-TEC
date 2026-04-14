<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elite Drive | Premium Car Rentals</title>
    <link rel="stylesheet" href="style.css?v=20260410">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .home-page {
            min-height: 100vh;
            background:
                radial-gradient(circle at 12% 10%, rgba(215, 174, 92, 0.22), transparent 24%),
                radial-gradient(circle at 90% 18%, rgba(61, 220, 151, 0.12), transparent 26%),
                radial-gradient(circle at 70% 92%, rgba(93, 125, 255, 0.12), transparent 28%),
                var(--bg);
        }

        .home-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(15, 15, 17, 0.9);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
        }

        .home-brand {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.4px;
            color: var(--text);
            text-decoration: none;
        }

        .home-brand .logo {
            width: 42px;
            height: 42px;
            object-fit: cover;
            border-radius: 10px;
        }

        .home-login {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.85rem 1.2rem;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 800;
            color: #17181d;
            background: linear-gradient(120deg, var(--primary), var(--primary-strong));
            box-shadow: 0 16px 34px rgba(215, 174, 92, 0.25);
        }

        .home-hero {
            width: min(1180px, calc(100% - 2rem));
            margin: 0 auto;
            padding: 4.25rem 0 2.25rem;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 2rem;
            align-items: center;
        }

        .hero-copy {
            display: grid;
            gap: 1rem;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            width: fit-content;
            padding: 0.45rem 0.8rem;
            border-radius: 999px;
            border: 1px solid rgba(215, 174, 92, 0.25);
            background: rgba(215, 174, 92, 0.08);
            color: #f3dfac;
            font-size: 0.9rem;
            font-weight: 700;
        }

        .hero-copy h1 {
            margin: 0;
            font-size: clamp(2.8rem, 6vw, 5.4rem);
            line-height: 0.95;
            letter-spacing: -0.04em;
            color: var(--text);
        }

        .hero-copy p {
            max-width: 620px;
            color: var(--text-muted);
            font-size: 1.06rem;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.9rem;
            margin-top: 0.5rem;
        }

        .home-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.9rem 1.2rem;
            border-radius: 999px;
            border: 1px solid var(--border);
            color: var(--text);
            text-decoration: none;
            background: rgba(255, 255, 255, 0.03);
        }

        .hero-card {
            padding: 1.5rem;
            border-radius: 24px;
            border: 1px solid var(--border);
            background:
                linear-gradient(160deg, rgba(23, 24, 29, 0.96), rgba(16, 18, 24, 0.94)),
                url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;
            box-shadow: var(--shadow);
            min-height: 520px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .hero-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.08), rgba(0, 0, 0, 0.5));
            pointer-events: none;
        }

        .hero-card, .hero-card > * {
            position: relative;
            z-index: 1;
        }

        .hero-stat-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.8rem;
        }

        .mini-stat {
            padding: 1rem;
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(8, 10, 14, 0.4);
            backdrop-filter: blur(8px);
        }

        .mini-stat strong {
            display: block;
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 0.2rem;
        }

        .mini-stat span {
            color: #ccd4e4;
            font-size: 0.9rem;
        }

        .home-sections {
            width: min(1180px, calc(100% - 2rem));
            margin: 0 auto;
            padding: 0 0 4rem;
            display: grid;
            gap: 1.2rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }

        .info-card {
            padding: 1.35rem;
            border-radius: 20px;
            border: 1px solid var(--border);
            background: rgba(23, 24, 29, 0.8);
        }

        .info-card i {
            color: var(--primary);
            font-size: 1.15rem;
            margin-bottom: 0.8rem;
        }

        .info-card h3 {
            margin-bottom: 0.45rem;
            color: var(--text);
        }

        .info-card p {
            color: var(--text-muted);
        }

        .cta-strip {
            margin-top: 0.4rem;
            padding: 1.35rem;
            border-radius: 22px;
            border: 1px solid rgba(215, 174, 92, 0.18);
            background: linear-gradient(135deg, rgba(215, 174, 92, 0.12), rgba(255, 255, 255, 0.03));
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .cta-strip h2 {
            margin: 0 0 0.35rem;
            color: var(--text);
        }

        .cta-strip p {
            margin: 0;
            color: var(--text-muted);
        }

        .footer-note {
            width: min(1180px, calc(100% - 2rem));
            margin: 0 auto;
            padding: 0 0 2rem;
            color: var(--text-muted);
            text-align: center;
        }

        @media (max-width: 900px) {
            .home-hero {
                grid-template-columns: 1fr;
                padding-top: 2.5rem;
            }

            .hero-card {
                min-height: 420px;
            }

            .info-grid,
            .hero-stat-grid {
                grid-template-columns: 1fr;
            }

            .cta-strip {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="home-page">
        <header class="home-nav">
            <a href="index.php" class="home-brand">
                <img src="logo.png" alt="Elite Drive Logo" class="logo">
                <span>ELITE DRIVE</span>
            </a>
            <a href="login.php" class="home-login"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
        </header>

        <main>
            <section class="home-hero">
                <div class="hero-copy">
                    <span class="eyebrow"><i class="fa-solid fa-sparkles"></i> Premium car rental made simple</span>
                    <h1>Drive something worth remembering.</h1>
                    <p>Elite Drive gives you quick access to a curated fleet, streamlined booking, and a clean dashboard once you log in.</p>
                    <div class="hero-actions">
                        <a href="login.php" class="home-login"><i class="fa-solid fa-arrow-right"></i> Login now</a>
                        <a href="#why" class="home-secondary">See why people book here</a>
                    </div>
                </div>

                <div class="hero-card">
                    <div>
                        <div style="color: #f3dfac; font-weight: 700; margin-bottom: 0.65rem;">Available today</div>
                        <div style="color: #fff; font-size: clamp(2rem, 4vw, 3.2rem); font-weight: 800; line-height: 1.05; max-width: 10ch;">Luxury rentals with a faster path to checkout.</div>
                    </div>
                    <div class="hero-stat-grid">
                        <div class="mini-stat">
                            <strong>24/7</strong>
                            <span>Support</span>
                        </div>
                        <div class="mini-stat">
                            <strong>150+</strong>
                            <span>Cars</span>
                        </div>
                        <div class="mini-stat">
                            <strong>1 click</strong>
                            <span>To login</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="home-sections" id="why">
                <div class="info-grid">
                    <article class="info-card">
                        <i class="fa-solid fa-car-side"></i>
                        <h3>Curated fleet</h3>
                        <p>Choose from practical daily drivers to premium weekend rides, all in one place.</p>
                    </article>
                    <article class="info-card">
                        <i class="fa-solid fa-bolt"></i>
                        <h3>Fast booking flow</h3>
                        <p>Log in once and move straight into browsing, booking, and tracking your rentals.</p>
                    </article>
                    <article class="info-card">
                        <i class="fa-solid fa-shield-halved"></i>
                        <h3>Secure access</h3>
                        <p>Your account keeps bookings, receipts, and updates organized in one secure dashboard.</p>
                    </article>
                </div>

                <div class="cta-strip">
                    <div>
                        <h2>Ready to start?</h2>
                        <p>Use your account to access the booking dashboard and reserve a car.</p>
                    </div>
                    <a href="login.php" class="home-login"><i class="fa-solid fa-lock-open"></i> Go to login</a>
                </div>
            </section>
        </main>

        <div class="footer-note">
            Elite Drive Rentals | Premium car rentals in the Philippines
        </div>
    </div>
</body>
</html>
