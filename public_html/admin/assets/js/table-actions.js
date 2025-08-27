/* CLICK-BASED DROPDOWN - ESCAPES ALL CONTAINERS */
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Click-based dropdown system loading...');
    
    // FORCE HIDE ALL ORIGINAL DROPDOWN ITEMS
    document.querySelectorAll('.table-dropdown-items').forEach(function(item) {
        item.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important; pointer-events: none !important; position: absolute !important; left: -9999px !important; top: -9999px !important;';
    });
    
    let activeMenu = null;
    
    // Close any open context menu
    function closeContextMenu() {
        if (activeMenu) {
            activeMenu.remove();
            activeMenu = null;
        }
    }
    
    // Handle clicks on dropdown triggers
    document.addEventListener('click', function(event) {
        const svg = event.target.closest('.table-dropdown svg');
        
        if (svg) {
            event.preventDefault();
            event.stopPropagation();
            
            // Close any existing menu
            closeContextMenu();
            
            const dropdown = svg.closest('.table-dropdown');
            const originalMenu = dropdown.querySelector('.table-dropdown-items');
            
            if (!originalMenu) {
                console.error('No dropdown items found');
                return;
            }
            
            // Create context menu attached to body
            const contextMenu = document.createElement('div');
            contextMenu.className = 'table-dropdown-context-menu';
            contextMenu.innerHTML = originalMenu.innerHTML;
            
            // Position relative to viewport
            const rect = svg.getBoundingClientRect();
            const menuWidth = 140;
            const menuHeight = 150; // Approximate
            
            let left = rect.right - menuWidth;
            let top = rect.bottom + 5;
            
            // Keep menu on screen
            if (left < 10) left = rect.right + 5;
            if (left + menuWidth > window.innerWidth - 10) {
                left = window.innerWidth - menuWidth - 10;
            }
            if (top + menuHeight > window.innerHeight - 10) {
                top = rect.top - menuHeight - 5;
            }
            if (top < 10) top = 10;
            
            contextMenu.style.left = left + 'px';
            contextMenu.style.top = top + 'px';
            
            // Append to body to escape all containers
            document.body.appendChild(contextMenu);
            activeMenu = contextMenu;
            
            console.log('✅ Context menu opened at', left, top);
            
            // Handle clicks on menu items
            contextMenu.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link) {
                    // Let the browser handle the link normally
                    closeContextMenu();
                }
            });
            
        } else {
            // Click outside - close menu
            closeContextMenu();
        }
    });
    
    // Close on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeContextMenu();
        }
    });
    
    // Close on scroll
    document.addEventListener('scroll', function() {
        closeContextMenu();
    });
    
    // Continuously force hide dropdown items (in case they get shown by other code)
    setInterval(function() {
        document.querySelectorAll('.table-dropdown-items').forEach(function(item) {
            if (item.style.display !== 'none') {
                item.style.cssText = 'display: none !important; visibility: hidden !important; opacity: 0 !important; pointer-events: none !important; position: absolute !important; left: -9999px !important; top: -9999px !important;';
            }
        });
    }, 100);
    
    console.log('✅ Click-based dropdown system ready!');
});