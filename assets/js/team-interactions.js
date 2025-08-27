/* ===========================
   TEAM SECTION JAVASCRIPT
   Sleek & Professional Interactions
   =========================== */

// Bio toggle functionality - only works for long content
function toggleBio(button) {
    const bioContainer = button.closest('.member-bio-container');
    const bio = bioContainer.querySelector('.member-bio');
    const isExpanded = bio.classList.contains('expanded');
    
    if (isExpanded) {
        // Collapse
        bio.classList.remove('expanded');
        bio.classList.add('truncated');
        button.classList.remove('active');
        
        // Smooth scroll to top of card if needed
        const card = button.closest('.team-member-card');
        const cardTop = card.offsetTop;
        const scrollTop = window.pageYOffset;
        const cardHeight = card.offsetHeight;
        
        if (scrollTop > cardTop + cardHeight - window.innerHeight) {
            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    } else {
        // Expand
        bio.classList.add('expanded');
        bio.classList.remove('truncated');
        button.classList.add('active');
    }
}

// Enhanced hover effects and bio management
document.addEventListener('DOMContentLoaded', function() {
    const teamCards = document.querySelectorAll('.team-member-card');
    
    // Initialize bio truncation based on content length
    teamCards.forEach(card => {
        const bioContainer = card.querySelector('.member-bio-container');
        const socialLinks = card.querySelector('.social-links');
        const overlay = card.querySelector('.member-overlay');
        
        // Handle social links visibility
        if (socialLinks && overlay) {
            const hasVisibleSocialLinks = socialLinks.querySelectorAll('a').length > 0;
            if (!hasVisibleSocialLinks) {
                overlay.style.display = 'none';
            }
        }
        
        if (bioContainer) {
            const bio = bioContainer.querySelector('.member-bio');
            const toggleButton = bioContainer.querySelector('.bio-toggle');
            
            if (bio && toggleButton) {
                // Measure the natural height of the content
                const originalMaxHeight = bio.style.maxHeight;
                bio.style.maxHeight = 'none';
                const fullHeight = bio.scrollHeight;
                bio.style.maxHeight = originalMaxHeight;
                
                // Only show toggle and truncate if content is taller than 120px
                if (fullHeight > 120) {
                    bio.classList.add('truncated');
                    toggleButton.classList.add('show');
                } else {
                    // For short content, ensure it's fully visible
                    bio.style.maxHeight = 'none';
                    bio.classList.remove('truncated');
                    toggleButton.style.display = 'none';
                }
            }
        }
        
        // Add subtle parallax effect on mouse move
        card.addEventListener('mousemove', function(e) {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 20;
            const rotateY = (centerX - x) / 20;
            
            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-12px) scale(1.02)`;
        });
        
        card.addEventListener('mouseleave', function() {
            card.style.transform = '';
        });
        
        // Add loading animation delay
        const index = Array.from(teamCards).indexOf(card);
        card.style.animationDelay = `${index * 0.2}s`;
    });
    
    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);
    
    teamCards.forEach(card => {
        observer.observe(card);
    });
});

// Add smooth scroll behavior for social links
document.addEventListener('click', function(e) {
    if (e.target.closest('.social-link')) {
        const link = e.target.closest('.social-link');
        const href = link.getAttribute('href');
        
        // Add ripple effect
        const ripple = document.createElement('span');
        ripple.classList.add('ripple');
        link.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
        
        // If it's a placeholder link, prevent default
        if (href === '#') {
            e.preventDefault();
        }
    }
});

// Add CSS for ripple effect
const rippleCSS = `
.social-link {
    position: relative;
    overflow: hidden;
}

.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.6);
    transform: scale(0);
    animation: ripple-effect 0.6s linear;
    pointer-events: none;
}

@keyframes ripple-effect {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

.team-member-card {
    opacity: 0;
    transform: translateY(50px);
    transition: all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.team-member-card.animate-in {
    opacity: 1;
    transform: translateY(0);
}
`;

// Inject the CSS
const style = document.createElement('style');
style.textContent = rippleCSS;
document.head.appendChild(style);
