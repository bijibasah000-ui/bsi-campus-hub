/* ============================================================
   BSI Campus Hub — app.js
   ============================================================ */

/* ── Sidebar toggle ── */
function toggleSidebar() {
    var sb = document.getElementById('sidebar');
    if (!sb) return;
    sb.classList.toggle('collapsed');
    localStorage.setItem('sb_collapsed', sb.classList.contains('collapsed') ? '1' : '0');
}

/* ── Profile dropdown (topbar kanan atas) ── */
function toggleProfileDropdown() {
    var dd = document.getElementById('profileDropdown');
    if (dd) dd.classList.toggle('open');
}

/* ── Init on DOMContentLoaded ── */
document.addEventListener('DOMContentLoaded', function () {

    /* Restore sidebar state */
    var sb = document.getElementById('sidebar');
    if (sb && localStorage.getItem('sb_collapsed') === '1') {
        sb.classList.add('collapsed');
    }

    /* Filter buttons */
    document.querySelectorAll('.filter-bar').forEach(function (bar) {
        bar.querySelectorAll('.fb').forEach(function (btn) {
            btn.addEventListener('click', function () {
                bar.querySelectorAll('.fb').forEach(function (b) { b.classList.remove('active'); });
                this.classList.add('active');
            });
        });
    });

    /* Sync sidebar avatar/name/email dengan data dari dropdown (jika login) */
    syncSidebarProfile();
});

/**
 * Sync sidebar profile dari data yang ada di halaman.
 * Berguna agar saat login/logout sidebar langsung update
 * tanpa perlu reload manual.
 */
function syncSidebarProfile() {
    var pdName  = document.querySelector('.pd-name');
    var pdEmail = document.querySelector('.pd-email');
    var sbName  = document.getElementById('sidebarName');
    var sbEmail = document.getElementById('sidebarEmail');
    var sbAv    = document.getElementById('sidebarAvatar');
    var topAv   = document.getElementById('topbarAv');

    if (pdName && sbName) sbName.textContent = pdName.textContent.trim();
    if (pdEmail && sbEmail) sbEmail.textContent = pdEmail.textContent.trim();

    if (pdName && sbAv) {
        var initials = pdName.textContent.trim().substring(0, 2).toUpperCase();
        sbAv.textContent  = initials;
        if (topAv) topAv.textContent = initials;
    }
}
