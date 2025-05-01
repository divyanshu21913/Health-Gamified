document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.change-password-form');
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');

    // Password validation
    function validatePasswords() {
        if (newPassword.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity("Passwords don't match");
        } else {
            confirmPassword.setCustomValidity('');
        }
    }

    newPassword.addEventListener('change', validatePasswords);
    confirmPassword.addEventListener('input', validatePasswords);

    // Password strength indicator
    newPassword.addEventListener('input', function() {
        const strength = {
            0: "Very Weak",
            1: "Weak",
            2: "Medium",
            3: "Strong",
            4: "Very Strong"
        };

        let score = 0;
        const password = newPassword.value;

        // Add points for password characteristics
        if (password.length >= 8) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;

        // Update strength indicator
        const strengthIndicator = document.getElementById('password-strength');
        if (strengthIndicator) {
            strengthIndicator.textContent = `Strength: ${strength[score]}`;
            strengthIndicator.className = `strength-${score}`;
        }
    });

    // Success message fade
    const successMessage = document.querySelector('.message.success');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.opacity = '0';
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 300);
        }, 3000);
    }

    // Setup user dropdown
    document.getElementById('user-menu-btn').addEventListener('click', function() {
        document.getElementById('user-dropdown').classList.toggle('active');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const userMenuBtn = document.getElementById('user-menu-btn');
        const userDropdown = document.getElementById('user-dropdown');
        
        if (!userMenuBtn.contains(event.target) && !userDropdown.contains(event.target)) {
            userDropdown.classList.remove('active');
        }
    });
});