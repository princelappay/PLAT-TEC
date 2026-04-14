
// Elite Drive Pro Design - Global Script v1.0
// Features: Particles bg, AOS animations, smooth scroll, lazy load, mobile nav, luxury effects

// 1. Particles.js for hero luxury particles (gold/speed lines)
document.addEventListener('DOMContentLoaded', function() {
    // Particles.js for hero (luxury gold particles)
    if (document.querySelector('.hero')) {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js';
        script.onload = function() {
            particlesJS('hero-particles', {
                particles: {
                    number: { value: 60, density: { enable: true, value_area: 800 } },
                    color: { value: ['#d7ae5c', '#c9993f', '#f4e4bc'] },
                    shape: { type: 'circle', stroke: { width: 0, color: '#000000' } },
                    opacity: { value: 0.3, random: true },
                    size: { value: 3, random: true },
                    line_linked: { enable: true, distance: 120, color: '#d7ae5c', opacity: 0.2, width: 1 },
                    move: { enable: true, speed: 2, direction: 'none', random: true, straight: false }
                },
                interactivity: {
                    detect_on: 'canvas',
                    events: { onhover: { enable: true, mode: 'grab' }, onclick: { enable: true, mode: 'push' } }
                },
                retina_detect: true
            });
        };
        document.head.appendChild(script);
        
        // Add canvas for particles
        const canvas = document.createElement('div');
        canvas.id = 'hero-particles';
        canvas.style.cssText = 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;';
        document.querySelector('.hero').style.position = 'relative';
        document.querySelector('.hero').appendChild(canvas);
    }

    // 2. AOS - Animate On Scroll (CDN)
    const aosScript = document.createElement('script');
    aosScript.src = 'https://unpkg.com/aos@2.3.1/dist/aos.js';
    aosScript.onload = function() {
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 100
        });
    };
    document.head.appendChild(aosScript);

    const aosCss = document.createElement('link');
    aosCss.rel = 'stylesheet';
    aosCss.href = 'https://unpkg.com/aos@2.3.1/dist/aos.css';
    document.head.appendChild(aosCss);

    // 3. Smooth scroll for anchor links
    document.querySelectorAll('a[href^=\"#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });
    });

    // 4. Lazy load images (native)
    if ('loading' in HTMLImageElement.prototype) {
        document.querySelectorAll('img').forEach(img => img.loading = 'lazy');
    } else {
        // Fallback
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    observer.unobserve(img);
                }
            });
        });
        document.querySelectorAll('.lazy').forEach(img => observer.observe(img));
    }

    // 5. Mobile nav toggle (hamburger for small screens)
    const navbar = document.querySelector('.navbar');
    const navLinks = document.querySelector('.nav-links');
    let mobileMenuOpen = false;

    function toggleMobileMenu() {
        mobileMenuOpen = !mobileMenuOpen;
        navLinks.style.display = mobileMenuOpen ? 'flex' : 'none';
        navbar.classList.toggle('mobile-open', mobileMenuOpen);
    }

    // Add hamburger button
    const hamburger = document.createElement('div');
    hamburger.className = 'hamburger';
    hamburger.innerHTML = '<span></span><span></span><span></span>';
    navbar.appendChild(hamburger);

    hamburger.addEventListener('click', toggleMobileMenu);

    // Close on resize >768px
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768 && mobileMenuOpen) {
            navLinks.style.display = 'flex';
            mobileMenuOpen = false;
        }
    });

    // 6. Luxury effects: Car card hover glow + rotation
    document.querySelectorAll('.car-card, .car-card-large').forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-8px) rotateX(2deg)';
            card.style.boxShadow = '0 20px 40px rgba(215, 174, 92, 0.3)';
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0) rotateX(0)';
            card.style.boxShadow = '';
        });
    });

    // 7. Form validation + loading spinner
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type=\"submit\"]');
            if (submitBtn) {
                    // Preserve submit button name/value because disabled controls are not submitted.
                    if (submitBtn.name) {
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = submitBtn.name;
                        hidden.value = submitBtn.value || '1';
                        this.appendChild(hidden);
                    }
                    submitBtn.innerHTML = '<i class=\"fas fa-spinner fa-spin\"></i> Processing...';
                    submitBtn.disabled = true;
            }
        });
    });

    // 8. Notification dismiss animation
    document.querySelectorAll('.notification-item .mark-read-form').forEach(form => {
        form.addEventListener('submit', function() {
            const item = this.closest('.notification-item');
            item.style.animation = 'fadeOut 0.5s ease-out forwards';
            setTimeout(() => item.remove(), 500);
        });
    });

    // 9. Back to top button (luxury floating)
    const backToTop = document.createElement('button');
    backToTop.id = 'back-to-top';
    backToTop.innerHTML = '<i class="fas fa-chevron-up"></i>';
    backToTop.style.cssText = `
        position: fixed; bottom: 2rem; right: 2rem; width: 50px; height: 50px;
        background: linear-gradient(120deg, #d7ae5c, #c9993f); border: none; border-radius: 50%;
        color: #1a1a1d; font-size: 1.2rem; cursor: pointer; opacity: 0; transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(215, 174, 92, 0.4); z-index: 1000;
    `;
    document.body.appendChild(backToTop);

    const scrollObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            backToTop.style.opacity = entry.isIntersecting ? '0' : '1';
        });
    }, { rootMargin: '-100px' });

    scrollObserver.observe(document.querySelector('.main-content') || document.body);

    backToTop.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // 10. Price counter animation (for dashboard/stats)
    const counters = document.querySelectorAll('.stat-card h3, .booking-price');
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = parseInt(entry.target.textContent.replace(/[^\d]/g, ''));
                let count = 0;
                const increment = target / 100;
                const timer = setInterval(() => {
                    count += increment;
                    if (count >= target) {
                        entry.target.textContent = entry.target.textContent.replace(/\d+/, Math.floor(target));
                        clearInterval(timer);
                    } else {
                        entry.target.textContent = entry.target.textContent.replace(/\d+/, Math.floor(count));
                    }
                }, 20);
                counterObserver.unobserve(entry.target);
            }
        });
    });
    counters.forEach(counter => counterObserver.observe(counter));
});

// Add CSS keyframes if not in style.css (fadeOut for notifications)
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(-10px); }
    }
    @media (max-width: 768px) {
        .nav-links { display: none; flex-direction: column; gap: 0.5rem; }
        .navbar.mobile-open .nav-links { display: flex !important; }
        .hamburger { display: flex; flex-direction: column; justify-content: space-around; width: 30px; height: 25px; cursor: pointer; }
        .hamburger span { display: block; height: 3px; width: 100%; background: var(--primary); transition: 0.3s; }
        .hamburger.active span:nth-child(1) { transform: rotate(-45deg) translate(-5px, 6px); }
        .hamburger.active span:nth-child(2) { opacity: 0; }
        .hamburger.active span:nth-child(3) { transform: rotate(45deg) translate(-5px, -6px); }
    }
    #back-to-top:hover { transform: scale(1.1) rotate(360deg); }
`;
document.head.appendChild(style);

