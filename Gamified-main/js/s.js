// Dark Mode Toggle
const themeToggle = document.createElement('div');
themeToggle.className = 'theme-toggle';
themeToggle.innerHTML = `
  <label>
    <input type="checkbox" id="theme-checkbox">
    <span class="toggle-slider"></span>
    <span class="toggle-icon sun material-symbols-outlined">light_mode</span>
    <span class="toggle-icon moon material-symbols-outlined">dark_mode</span>
  </label>
`;

// Add the toggle to the header (you might want to adjust the placement)
document.querySelector('.user-menu').prepend(themeToggle);

// Check for saved theme preference or use preferred color scheme
const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
const currentTheme = localStorage.getItem('theme') || 
                     (prefersDarkScheme.matches ? 'dark' : 'light');

// Apply the current theme
document.documentElement.setAttribute('data-theme', currentTheme);

// Set the checkbox state
if (currentTheme === 'light') {
  document.getElementById('theme-checkbox').checked = true;
}

// Theme toggle functionality
document.getElementById('theme-checkbox').addEventListener('change', function() {
  if (this.checked) {
    document.documentElement.setAttribute('data-theme', 'light');
    localStorage.setItem('theme', 'light');
  } else {
    document.documentElement.setAttribute('data-theme', 'dark');
    localStorage.setItem('theme', 'dark');
  }
});