/* ============================================================
   BSI Campus Hub — dashboard.js  (public/js/dashboard.js)
   ============================================================ */

(function () {
    var track  = document.getElementById('carouselTrack');
    var dotsEl = document.querySelectorAll('#carouselDots .dot');
    var wrap   = document.getElementById('carouselWrap');
    if (!track || !wrap) return;

    var total   = dotsEl.length;
    var current = 0;
    var timer   = null;

    function goSlide(n) {
        current = (n + total) % total;
        track.style.transform = 'translateX(-' + (current * 100) + '%)';
        dotsEl.forEach(function (d, i) { d.classList.toggle('active', i === current); });
    }

    function next() { goSlide(current + 1); }
    function prev() { goSlide(current - 1); }

    function startAuto() { timer = setInterval(next, 3500); }
    function stopAuto()  { clearInterval(timer); }

    dotsEl.forEach(function (d) {
        d.addEventListener('click', function () { goSlide(+this.dataset.index); });
    });

    var btnPrev = document.getElementById('prevBtn');
    var btnNext = document.getElementById('nextBtn');
    if (btnPrev) btnPrev.addEventListener('click', prev);
    if (btnNext) btnNext.addEventListener('click', next);

    wrap.addEventListener('mouseenter', stopAuto);
    wrap.addEventListener('mouseleave', startAuto);

    startAuto();
})();
