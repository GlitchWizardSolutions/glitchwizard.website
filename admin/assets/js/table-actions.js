/* Global Table Actions JavaScript */
/* Handles dropdown functionality for table actions across all admin sections */

// Table dropdown context menu functionality
document.querySelectorAll('.table-dropdown').forEach(dropdownElement => {
    dropdownElement.onclick = event => {
        event.preventDefault();
        let dropdownItems = dropdownElement.querySelector('.table-dropdown-items');
        let contextMenu = document.querySelector('.table-dropdown-items-context-menu');
        if (!contextMenu) {
            contextMenu = document.createElement('div');
            contextMenu.classList.add('table-dropdown-items', 'table-dropdown-items-context-menu');
            document.addEventListener('click', event => {
                if (contextMenu.classList.contains('show') && !event.target.closest('.table-dropdown-items-context-menu') && !event.target.closest('.table-dropdown')) {
                    contextMenu.classList.remove('show');
                }
            });
        }
        contextMenu.classList.add('show');
        contextMenu.innerHTML = dropdownItems.innerHTML;
        contextMenu.style.position = 'absolute';
        let width = window.getComputedStyle(dropdownItems).width ? parseInt(window.getComputedStyle(dropdownItems).width) : 0;
        contextMenu.style.left = (event.pageX-width) + 'px';
        contextMenu.style.top = event.pageY + 'px';
        document.body.appendChild(contextMenu);
    };
});