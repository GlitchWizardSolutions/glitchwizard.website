/**
 * Accessibility JavaScript Fix
 * 
 * This script ensures that all badge elements and content title paragraphs 
 * maintain WCAG AA compliance by removing inline styles and letting CSS 
 * custom properties (configurable through branding) take precedence.
 * 
 * CREATED: 2025-07-08
 * PURPOSE: Remove problematic inline styles to allow proper CSS styling
 */

document.addEventListener('DOMContentLoaded', function() {
    // Function to fix badge accessibility by removing inline styles
    function fixBadgeAccessibility() {
        // Find all badge elements
        const badges = document.querySelectorAll('.badge, [class*="badge"]');
        
        badges.forEach(badge => {
            // Remove problematic inline styles to let CSS custom properties take precedence
            badge.style.removeProperty('background-color');
            badge.style.removeProperty('color');
            badge.style.removeProperty('opacity');
            badge.style.removeProperty('border');
        });
    }
    
    // Function to fix content title paragraph accessibility by removing inline styles
    function fixContentTitleAccessibility() {
        // Find content title paragraphs with problematic styling
        const contentTitlePs = document.querySelectorAll('.content-title p, main .content-title .title p');
        
        contentTitlePs.forEach(p => {
            // Remove problematic inline styles to let CSS custom properties take precedence
            p.style.removeProperty('color');
            p.style.removeProperty('background-color');
        });
        
        // Also target paragraphs with specific problematic inline styles
        const problematicPs = document.querySelectorAll('p[style*="color: rgb(134, 145, 168)"], p[style*="background-color: rgb(243, 244, 247)"]');
        
        problematicPs.forEach(p => {
            p.style.removeProperty('color');
            p.style.removeProperty('background-color');
        });
    }
    
    // Function to run all accessibility fixes
    function runAccessibilityFixes() {
        fixBadgeAccessibility();
        fixContentTitleAccessibility();
    }
    
    // Initial fix on page load
    runAccessibilityFixes();
    
    // Create a MutationObserver to watch for dynamically added content
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        // Check if the added node or its children contain badges or content title paragraphs
                        const newBadges = node.querySelectorAll ? 
                            node.querySelectorAll('.badge, [class*="badge"]') : [];
                        const newContentPs = node.querySelectorAll ? 
                            node.querySelectorAll('.content-title p, main .content-title .title p') : [];
                        
                        // Fix badges - only remove inline styles
                        newBadges.forEach(badge => {
                            badge.style.removeProperty('background-color');
                            badge.style.removeProperty('color');
                            badge.style.removeProperty('opacity');
                            badge.style.removeProperty('border');
                        });
                        
                        // Fix content title paragraphs - only remove inline styles
                        newContentPs.forEach(p => {
                            p.style.removeProperty('color');
                            p.style.removeProperty('background-color');
                        });
                        
                        // Also check if the node itself is a badge
                        if (node.classList && (node.classList.contains('badge') || 
                            Array.from(node.classList).some(cls => cls.includes('badge')))) {
                            node.style.removeProperty('background-color');
                            node.style.removeProperty('color');
                            node.style.removeProperty('opacity');
                            node.style.removeProperty('border');
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
                    
                    target.style.removeProperty('background-color');
                    target.style.removeProperty('color');
                    target.style.removeProperty('opacity');
                    target.style.removeProperty('border');
                }
                
                // Handle content title paragraphs - only remove inline styles
                if (target.tagName === 'P' && 
                    (target.closest('.content-title') || target.classList.contains('content-title'))) {
                    target.style.removeProperty('color');
                    target.style.removeProperty('background-color');
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
