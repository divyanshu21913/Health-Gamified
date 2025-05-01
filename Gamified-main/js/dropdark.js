document.addEventListener('DOMContentLoaded', function() {
    // First check if we already have a theme toggle
    let themeToggle = document.querySelector('.theme-toggle');
    
    // If we don't have one, create it
    if (!themeToggle) {
        themeToggle = document.createElement('div');
        themeToggle.className = 'theme-toggle-container';
        themeToggle.innerHTML = `
            <label class="theme-toggle">
                <input type="checkbox" id="theme-checkbox">
                <span class="toggle-slider"></span>
                <span class="toggle-icon sun material-symbols-outlined">light_mode</span>
                <span class="toggle-icon moon material-symbols-outlined">dark_mode</span>
            </label>
        `;

        // Add the toggle to the header
        const userMenu = document.querySelector('.user-menu');
        if (userMenu) {
            userMenu.insertAdjacentElement('afterbegin', themeToggle);
        }
    }

    // Get the checkbox regardless of whether we just created it or it existed
    const themeCheckbox = document.getElementById('theme-checkbox');
    
    // Check for saved theme preference or use preferred color scheme
    const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
    const currentTheme = localStorage.getItem('theme') || 
                        (prefersDarkScheme.matches ? 'dark' : 'light');

    // Apply the current theme
    document.documentElement.setAttribute('data-theme', currentTheme);

    // Set the checkbox state
    if (themeCheckbox) {
        themeCheckbox.checked = currentTheme === 'light';

        // Theme toggle functionality
        themeCheckbox.addEventListener('change', function() {
            if (this.checked) {
                document.documentElement.setAttribute('data-theme', 'light');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
            }
        });
    }

    // User dropdown functionality
    const userMenuBtn = document.getElementById('user-menu-btn');
    const userDropdown = document.getElementById('user-dropdown');

    if (userMenuBtn && userDropdown) {
        userMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Toggle dropdown visibility
            const isActive = userDropdown.classList.contains('active');
            
            // Close all dropdowns first
            document.querySelectorAll('.user-dropdown.active').forEach(dropdown => {
                if (dropdown !== userDropdown) {
                    dropdown.classList.remove('active');
                }
            });
            
            // Toggle current dropdown
            userDropdown.classList.toggle('active', !isActive);
            
            // Close when clicking outside
            if (!isActive) {
                const clickHandler = function(e) {
                    if (!userDropdown.contains(e.target) && e.target !== userMenuBtn) {
                        userDropdown.classList.remove('active');
                        document.removeEventListener('click', clickHandler);
                    }
                };
                document.addEventListener('click', clickHandler);
            }
        });
    }
});