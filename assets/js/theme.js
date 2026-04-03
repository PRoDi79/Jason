function setTheme(theme) {
    let themeCss = document.getElementById('theme-css');
    if (!themeCss) return;
    if (theme === 'dark') {
        themeCss.setAttribute('href', 'assets/css/dark-theme.css');
        localStorage.setItem('ofishal_theme', 'dark');
        let toggleBtn = document.getElementById('themeToggle');
        if (toggleBtn) toggleBtn.innerHTML = '☀️';
    } else {
        themeCss.setAttribute('href', 'assets/css/light-theme.css');
        localStorage.setItem('ofishal_theme', 'light');
        let toggleBtn = document.getElementById('themeToggle');
        if (toggleBtn) toggleBtn.innerHTML = '🌙';
    }
}
function toggleTheme() {
    let currentTheme = localStorage.getItem('ofishal_theme') || 'dark';
    if (currentTheme === 'dark') { setTheme('light'); } else { setTheme('dark'); }
}
document.addEventListener('DOMContentLoaded', function() {
    let savedTheme = localStorage.getItem('ofishal_theme') || 'dark';
    setTheme(savedTheme);
});