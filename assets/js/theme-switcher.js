document.addEventListener('DOMContentLoaded', (event) => {
    const themeSwitcher = document.getElementById('theme-switcher');
    const currentTheme = localStorage.getItem('theme') || 'light';

    document.body.classList.add(`${currentTheme}-theme`);

    if (themeSwitcher) {
        themeSwitcher.addEventListener('click', () => {
            const newTheme = document.body.classList.contains('light-theme') ? 'dark' : 'light';
            document.body.classList.remove('light-theme', 'dark-theme');
            document.body.classList.add(`${newTheme}-theme`);
            localStorage.setItem('theme', newTheme);
        });
    }
});