// js/animations.js - purely visual enhancements (scroll-reveal, ripple, counters).
// Does not touch any form validation or submission logic.
document.addEventListener('DOMContentLoaded', function () {

    // 1. Scroll-reveal for cards / stats.
    var revealTargets = document.querySelectorAll('.card, .stat');
    if ('IntersectionObserver' in window && revealTargets.length) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry, i) {
                if (entry.isIntersecting) {
                    entry.target.style.animationDelay = (i % 6) * 0.06 + 's';
                    entry.target.classList.add('in-view');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        revealTargets.forEach(function (el) { observer.observe(el); });
    } else {
        revealTargets.forEach(function (el) { el.classList.add('in-view'); });
    }

    // 2. Ripple effect on any .btn click.
    document.querySelectorAll('.btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            var rect = btn.getBoundingClientRect();
            var ripple = document.createElement('span');
            ripple.className = 'ripple';
            ripple.style.left = (e.clientX - rect.left) + 'px';
            ripple.style.top = (e.clientY - rect.top) + 'px';
            ripple.style.width = ripple.style.height = Math.max(rect.width, rect.height) + 'px';
            btn.appendChild(ripple);
            setTimeout(function () { ripple.remove(); }, 600);
        });
    });

    // 3. Animated count-up for admin dashboard stat numbers.
    document.querySelectorAll('.stat .num').forEach(function (el) {
        var target = parseInt(el.textContent, 10);
        if (isNaN(target)) return;
        var current = 0;
        var step = Math.max(1, Math.ceil(target / 30));
        el.textContent = '0';
        var timer = setInterval(function () {
            current += step;
            if (current >= target) { current = target; clearInterval(timer); }
            el.textContent = current;
        }, 25);
    });

    // 4. FAQ accordion toggle (about.php).
    document.querySelectorAll('.faq-question').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var item = btn.closest('.faq-item');
            if (!item) return;
            var wasOpen = item.classList.contains('open');
            item.parentNode.querySelectorAll('.faq-item.open').forEach(function (el) {
                el.classList.remove('open');
            });
            if (!wasOpen) { item.classList.add('open'); }
        });
    });

    // 5. Navbar shadow intensifies on scroll.
    var nav = document.querySelector('.navbar');
    if (nav) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 8) {
                nav.style.boxShadow = '0 6px 22px rgba(37,99,235,0.35)';
            } else {
                nav.style.boxShadow = '0 2px 16px rgba(37,99,235,0.25)';
            }
        });
    }
});
