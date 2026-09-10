/* Dernek Sitesi — ön yüz etkileşimleri */
(function () {
    'use strict';

    // Mobil menü
    var toggle = document.querySelector('.nav-toggle');
    var nav = document.querySelector('.main-nav');
    if (toggle && nav) {
        toggle.addEventListener('click', function () {
            nav.classList.toggle('open');
        });
    }

    // Hero slider
    var hero = document.getElementById('heroSlider');
    if (hero) {
        var slides = Array.prototype.slice.call(hero.querySelectorAll('.slide'));
        var dots = Array.prototype.slice.call(hero.querySelectorAll('.slider-dot'));
        var hIdx = 0;
        var hTimer = null;
        var interval = parseInt(hero.getAttribute('data-interval'), 10) || 6000;

        function heroGo(n) {
            if (slides[hIdx]) slides[hIdx].classList.remove('is-active');
            if (dots[hIdx]) dots[hIdx].classList.remove('active');
            hIdx = (n + slides.length) % slides.length;
            if (slides[hIdx]) slides[hIdx].classList.add('is-active');
            if (dots[hIdx]) dots[hIdx].classList.add('active');
        }
        function heroPlay() {
            if (slides.length < 2) return;
            heroStop();
            hTimer = setInterval(function () { heroGo(hIdx + 1); }, interval);
        }
        function heroStop() {
            if (hTimer) { clearInterval(hTimer); hTimer = null; }
        }

        var prevBtn = hero.querySelector('.slider-prev');
        var nextBtn = hero.querySelector('.slider-next');
        if (prevBtn) prevBtn.addEventListener('click', function () { heroGo(hIdx - 1); heroPlay(); });
        if (nextBtn) nextBtn.addEventListener('click', function () { heroGo(hIdx + 1); heroPlay(); });
        dots.forEach(function (d) {
            d.addEventListener('click', function () { heroGo(parseInt(d.getAttribute('data-index'), 10) || 0); heroPlay(); });
        });
        hero.addEventListener('mouseenter', heroStop);
        hero.addEventListener('mouseleave', heroPlay);

        // Basit dokunmatik kaydırma
        var touchX = null;
        hero.addEventListener('touchstart', function (e) {
            touchX = e.changedTouches && e.changedTouches[0] ? e.changedTouches[0].clientX : null;
        }, { passive: true });
        hero.addEventListener('touchend', function (e) {
            if (touchX === null) return;
            var endX = e.changedTouches && e.changedTouches[0] ? e.changedTouches[0].clientX : touchX;
            var dx = endX - touchX;
            if (Math.abs(dx) > 40) heroGo(hIdx + (dx < 0 ? 1 : -1));
            touchX = null;
            heroPlay();
        }, { passive: true });

        heroPlay();
    }

    // Lightbox
    var lb = document.querySelector('.lightbox');
    if (lb) {
        var img = lb.querySelector('img');
        var caption = lb.querySelector('.lightbox-caption');
        var items = [];
        var idx = 0;

        function show() {
            var a = items[idx];
            if (!a) return;
            img.src = a.getAttribute('href');
            caption.textContent = a.getAttribute('data-caption') || '';
        }
        function open(a, list) {
            items = list;
            idx = list.indexOf(a);
            show();
            lb.classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        function close() {
            lb.classList.remove('open');
            img.src = '';
            document.body.style.overflow = '';
        }
        function step(delta) {
            if (items.length === 0) return;
            idx = (idx + delta + items.length) % items.length;
            show();
        }

        document.querySelectorAll('[data-lightbox]').forEach(function (grid) {
            var links = Array.prototype.slice.call(grid.querySelectorAll('a.gallery-item'));
            links.forEach(function (a) {
                a.addEventListener('click', function (e) {
                    e.preventDefault();
                    open(a, links);
                });
            });
        });

        lb.querySelector('.lightbox-close').addEventListener('click', close);
        lb.querySelector('.lightbox-prev').addEventListener('click', function (e) { e.stopPropagation(); step(-1); });
        lb.querySelector('.lightbox-next').addEventListener('click', function (e) { e.stopPropagation(); step(1); });
        lb.addEventListener('click', function (e) {
            if (e.target === lb) close();
        });
        document.addEventListener('keydown', function (e) {
            if (!lb.classList.contains('open')) return;
            if (e.key === 'Escape') close();
            if (e.key === 'ArrowLeft') step(-1);
            if (e.key === 'ArrowRight') step(1);
        });
    }
})();
