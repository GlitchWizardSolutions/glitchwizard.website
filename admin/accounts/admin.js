/* Accounts System - Admin JavaScript
 * Reserved for accounts-specific functionality if needed
 * Global admin functionality is handled by existing included scripts
 */

// Hamburger menu functionality for responsive sidebar
const aside = document.querySelector('aside');
if (window.innerWidth < 1000 || localStorage.getItem('admin_menu') == 'minimal') {
    aside.classList.add('minimal');
}

if (window.innerWidth < 1000) {
    document.addEventListener('click', event => {
        if (!aside.classList.contains('minimal') && !event.target.closest('aside') && !event.target.closest('.responsive-toggle') && window.innerWidth < 1000) {
            aside.classList.add('minimal');
        }
    });
}

window.addEventListener('resize', () => {
    if (window.innerWidth < 1000) {
        aside.classList.add('minimal');
    } else if (localStorage.getItem('admin_menu') == 'normal') {
        aside.classList.remove('minimal');
    }
});

// Hamburger button click handler
document.addEventListener('DOMContentLoaded', function() {
    const responsiveToggle = document.querySelector('.responsive-toggle');
    if (responsiveToggle) {
        responsiveToggle.onclick = event => {
            event.preventDefault();
            if (aside.classList.contains('minimal')) {
                aside.classList.remove('minimal');
                localStorage.setItem('admin_menu', 'normal');
            } else {
                aside.classList.add('minimal');
                localStorage.setItem('admin_menu', 'minimal');
            }
        };
    }
});
