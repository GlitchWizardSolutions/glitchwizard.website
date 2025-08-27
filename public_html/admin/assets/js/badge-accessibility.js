/**
 * Badge and Content Title Accessibility JavaScript Fix
 * 
 * This script ensures that all badge elements and content title paragraphs maintain WCAG AA compliance
 * by overriding any inline styles that may be applied by Bootstrap or other libraries.
 * 
 * CREATED: 2025-07-08
 * PURPOSE: Fix accessibility contrast issues with badge elements and content title paragraphs
 */

document.addEventListener('DOMContentLoaded', function() {
    // Function to fix badge accessibility
    function fixBadgeAccessibility() {
        // Find all badge elements
        const badges = document.querySelectorAll('.badge, [class*="badge"]');
        
        badges.forEach(badge => {
            // Remove problematic inline styles
            badge.style.removeProperty('background-color');
            badge.style.removeProperty('color');
            badge.style.removeProperty('opacity');
            
            // Apply accessible styling
            badge.style.setProperty('background-color', '#212529', 'important');
            badge.style.setProperty('color', '#ffffff', 'important');
            badge.style.setProperty('opacity', '1', 'important');
            badge.style.setProperty('border', '1px solid rgba(255, 255, 255, 0.3)', 'important');
        });
    }
    
    // Function to fix content title paragraph accessibility
    function fixContentTitleAccessibility() {
        // Find content title paragraphs with problematic styling
        const contentTitlePs = document.querySelectorAll('.content-title p, main .content-title .title p');
        
        contentTitlePs.forEach(p => {
            // Remove problematic inline styles
            p.style.removeProperty('color');
            p.style.removeProperty('background-color');
            
            // Apply accessible styling
            p.style.setProperty('color', '#4a5361', 'important');
            p.style.setProperty('background-color', 'transparent', 'important');
        });
        
        // Also target paragraphs with specific problematic inline styles
        const problematicPs = document.querySelectorAll('p[style*="color: rgb(134, 145, 168)"], p[style*="background-color: rgb(243, 244, 247)"]');
        
        problematicPs.forEach(p => {
            p.style.setProperty('color', '#4a5361', 'important');
            p.style.setProperty('background-color', 'transparent', 'important');
        });
    }
    
    // Function to run all accessibility fixes
    function runAccessibilityFixes() {
        fixBadgeAccessibility();
        fixContentTitleAccessibility();
    }
    
    // Initial fix on page load
    runAccessibilityFixes();
    
    // Create a MutationObserver to watch for dynamically added badges and content titles
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        // Check if the added node or its children contain badges
                        const newBadges = node.querySelectorAll ? 
                            node.querySelectorAll('.badge, [class*="badge"]') : [];
                        
                        newBadges.forEach(badge => {
                            badge.style.removeProperty('background-color');
                            badge.style.removeProperty('color');
                            badge.style.removeProperty('opacity');
                            
                            badge.style.setProperty('background-color', '#212529', 'important');
                            badge.style.setProperty('color', '#ffffff', 'important');
                            badge.style.setProperty('opacity', '1', 'important');
                            badge.style.setProperty('border', '1px solid rgba(255, 255, 255, 0.3)', 'important');
                        });
                        
                        // Also check if the node itself is a badge
                        if (node.classList && (node.classList.contains('badge') || 
                            Array.from(node.classList).some(cls => cls.includes('badge')))) {
                            node.style.removeProperty('background-color');
                            node.style.removeProperty('color');
                            node.style.removeProperty('opacity');
                            
                            node.style.setProperty('background-color', '#212529', 'important');
                            node.style.setProperty('color', '#ffffff', 'important');
                            node.style.setProperty('opacity', '1', 'important');
                            node.style.setProperty('border', '1px solid rgba(255, 255, 255, 0.3)', 'important');
                        }
                    }
                });
            }
            
            // Also handle attribute changes that might affect styling
            if (mutation.type === 'attributes' && 
                (mutation.attributeName === 'style' || mutation.attributeName === 'class')) {
                const target = mutation.target;
                if (target.classList && (target.classList.contains('badge') || 
                    Array.from(target.classList).some(cls => cls.includes('badge')))) {
                    
                    target.style.setProperty('background-color', '#212529', 'important');
                    target.style.setProperty('color', '#ffffff', 'important');
                    target.style.setProperty('opacity', '1', 'important');
                    target.style.setProperty('border', '1px solid rgba(255, 255, 255, 0.3)', 'important');
                }
            }
        });
    });
    
    // Start observing
    observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['style', 'class']
    });
    
    // Reapply fixes every few seconds as a fallback for any persistent issues
    setInterval(runAccessibilityFixes, 5000);
});
