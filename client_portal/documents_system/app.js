/**
 * DOCUMENTS SYSTEM JAVASCRIPT
 * App-specific JavaScript for documents_system
 * Location: public_html/documents_system/app.js
 */

// Documents system specific functionality
const DocumentsApp = {
    /**
     * Initialize documents system specific features
     */
    init: function() {
        console.log('Documents System JavaScript loaded');
        
        // Document dashboard features
        this.setupDocumentDashboard();
        
        // Document table enhancements
        this.setupDocumentTables();
        
        // Signature pad functionality
        this.setupSignaturePad();
        
        // Document viewer features
        this.setupDocumentViewer();
    },

    /**
     * Setup document dashboard features
     */
    setupDocumentDashboard: function() {
        // Summernote initialization for document content
        if (typeof $ !== 'undefined' && $.fn.summernote) {
            $('#documentContent, #versionNotes').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        }

        // Bootstrap tab functionality
        const triggerTabList = document.querySelectorAll('#editorTabs button[data-bs-toggle="tab"]');
        triggerTabList.forEach(function(triggerEl) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                new bootstrap.Tab(triggerEl);
            }
        });

        // Draft lock checking
        this.setupDraftLocking();
    },

    /**
     * Setup document tables with enhanced functionality
     */
    setupDocumentTables: function() {
        // Make document tables sortable
        const docTables = document.querySelectorAll('.document-table, .table');
        docTables.forEach(table => {
            // Add sortable class and headers
            table.classList.add('sortable-table');
            const headers = table.querySelectorAll('th');
            headers.forEach(header => {
                if (!header.getAttribute('data-sortable')) {
                    header.setAttribute('data-sortable', 'true');
                }
            });
        });

        // Add row hover effects and click handling
        const tableRows = document.querySelectorAll('.document-table tbody tr, .table tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('click', function(e) {
                // Don't trigger if clicking on a button or link
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || e.target.closest('a, button')) {
                    return;
                }
                
                // Add selected class
                tableRows.forEach(r => r.classList.remove('selected'));
                this.classList.add('selected');
            });
        });
    },

    /**
     * Setup signature pad functionality
     */
    setupSignaturePad: function() {
        const signatureCanvas = document.querySelector('#signature-pad');
        if (!signatureCanvas) return;

        // Basic signature pad setup (could be enhanced with library)
        let isDrawing = false;
        const ctx = signatureCanvas.getContext('2d');
        
        signatureCanvas.addEventListener('mousedown', (e) => {
            isDrawing = true;
            const rect = signatureCanvas.getBoundingClientRect();
            ctx.beginPath();
            ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
        });

        signatureCanvas.addEventListener('mousemove', (e) => {
            if (!isDrawing) return;
            const rect = signatureCanvas.getBoundingClientRect();
            ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
            ctx.stroke();
        });

        signatureCanvas.addEventListener('mouseup', () => {
            isDrawing = false;
        });

        // Clear signature button
        const clearBtn = document.querySelector('#clear-signature');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                ctx.clearRect(0, 0, signatureCanvas.width, signatureCanvas.height);
            });
        }
    },

    /**
     * Setup document viewer functionality
     */
    setupDocumentViewer: function() {
        // PDF viewer controls
        const pdfViewers = document.querySelectorAll('.pdf-viewer, iframe[src*=".pdf"]');
        pdfViewers.forEach(viewer => {
            viewer.style.border = '1px solid #dee2e6';
            viewer.style.borderRadius = '8px';
        });

        // Document download tracking
        const downloadLinks = document.querySelectorAll('a[href*=".pdf"], a[download]');
        downloadLinks.forEach(link => {
            link.addEventListener('click', function() {
                console.log('Document downloaded:', this.href);
                // Could send analytics event here
            });
        });
    },

    /**
     * Setup draft locking functionality
     */
    setupDraftLocking: function() {
        const draftId = document.querySelector('input[name="draft_id"]')?.value;
        if (!draftId) return;

        // Check if draft is locked
        this.checkIfDraftLocked(draftId);
        
        // Set up periodic lock checking
        setInterval(() => {
            this.checkIfDraftLocked(draftId);
        }, 30000); // Check every 30 seconds
    },

    /**
     * Check if draft is locked by another user
     */
    checkIfDraftLocked: function(draftId) {
        if (!draftId || draftId.startsWith('new_')) return;

        fetch('draft-locking-setup.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `draft_id=${encodeURIComponent(draftId)}&action=check_lock`
        })
        .then(response => response.json())
        .then(data => {
            if (data.locked && data.locked_by_current_user === false) {
                UnifiedUI.showNotification(
                    'This draft is currently being edited by someone else.',
                    'warning'
                );
                
                // Disable form elements
                const form = document.querySelector('#draftForm');
                if (form) {
                    const inputs = form.querySelectorAll('input, textarea, button');
                    inputs.forEach(input => input.disabled = true);
                }
            }
        })
        .catch(error => {
            console.log('Error checking draft lock:', error);
        });
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    DocumentsApp.init();
});

// Add CSS for selected table rows
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        .document-table tbody tr.selected,
        .table tbody tr.selected {
            background-color: #e3f2fd !important;
        }
        
        .document-table th[data-sortable]:hover,
        .table th[data-sortable]:hover {
            background-color: #e9ecef;
        }
        
        .document-table th.sort-asc::after,
        .table th.sort-asc::after {
            content: " ↑";
        }
        
        .document-table th.sort-desc::after,
        .table th.sort-desc::after {
            content: " ↓";
        }
    `;
    document.head.appendChild(style);
});
