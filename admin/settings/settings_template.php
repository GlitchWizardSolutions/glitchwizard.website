<?php
/**
 * Settings Template
 * 
 * This file provides the tabbed interface structure for settings pages.
 * It handles tab rendering and content loading.
 */

function init_settings_template($tabs, $active_tab) {
    ?>
    <div class="settings-tabs">
        <nav class="nav-tabs">
            <?php foreach ($tabs as $tab_id => $tab): ?>
                <a href="?tab=<?= htmlspecialchars($tab_id) ?>" 
                   class="nav-tab <?= $tab_id === $active_tab ? 'active' : '' ?>"
                   role="tab"
                   aria-selected="<?= $tab_id === $active_tab ? 'true' : 'false' ?>">
                    <i class="<?= htmlspecialchars($tab['icon']) ?>"></i>
                    <?= htmlspecialchars($tab['title']) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="tab-content">
            <?php
            // Load the content for the active tab
            if (isset($tabs[$active_tab]['file'])) {
                $tab_file = __DIR__ . '/' . $tabs[$active_tab]['file'];
                if (file_exists($tab_file)) {
                    include $tab_file;
                } else {
                    echo '<div class="alert alert-warning">Tab content file not found: ' . htmlspecialchars($tabs[$active_tab]['file']) . '</div>';
                }
            }
            ?>
        </div>
    </div>
    <?php
}

// Add CSS for the tabbed interface
?>
<style>
.settings-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.settings-header {
    margin-bottom: 30px;
}

.settings-header h2 {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.settings-header p {
    color: #666;
    font-size: 0.95em;
}

.settings-tabs {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.nav-tabs {
    display: flex;
    border-bottom: 1px solid #dee2e6;
    padding: 0 15px;
    background: #f8f9fa;
    border-radius: 8px 8px 0 0;
}

.nav-tab {
    padding: 15px 20px;
    color: #495057;
    text-decoration: none;
    border-bottom: 2px solid transparent;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.nav-tab:hover {
    color: #0d6efd;
    border-bottom-color: #0d6efd;
    background: rgba(13,110,253,0.1);
}

.nav-tab.active {
    color: #0d6efd;
    border-bottom-color: #0d6efd;
    background: #fff;
}

.nav-tab i {
    font-size: 0.9em;
}

.tab-content {
    padding: 20px;
}

/* Form styling */
.settings-form {
    max-width: 800px;
    margin: 0 auto;
}

.form-section {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #dee2e6;
}

.form-section:last-child {
    border-bottom: none;
}

.form-section h3 {
    margin-bottom: 20px;
    color: #212529;
    font-size: 1.25rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-group .form-text {
    font-size: 0.875em;
    color: #6c757d;
}

/* Responsive Design */
@media (max-width: 768px) {
    .nav-tabs {
        flex-direction: column;
        padding: 10px;
    }

    .nav-tab {
        border-bottom: none;
        border-left: 2px solid transparent;
    }

    .nav-tab.active {
        border-bottom: none;
        border-left: 2px solid #0d6efd;
    }
}
</style>
