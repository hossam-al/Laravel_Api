(function () {
    const btn = document.getElementById('profileToggle');
    const menu = document.getElementById('profileMenu');
    if (!btn || !menu) return;
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        menu.classList.toggle('open');
    });
    document.addEventListener('click', function (e) {
        if (!menu.contains(e.target) && !btn.contains(e.target)) {
            menu.classList.remove('open');
        }
    });
})();
