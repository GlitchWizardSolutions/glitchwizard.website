<?php
// Start the session
session_start();

// Include necessary files
require_once '../../private/gws-universal-config.php';
require_once '../../private/gws-universal-functions.php';

// Initialize admin template parameters
$selected = 'chat';
$selected_child = 'settings';
$title = 'Chat Settings';

// Check if user is logged in and has permission
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Check admin role permissions
if (!in_array($_SESSION['role'], ['Admin', 'Super Admin'])) {
    header('Location: ../index.php');
    exit;
}

$success_message = '';
$error_message = '';

// Handle form submission
if ($_POST) {
    try {
        // Prepare update data for the chat config table
        $chat_config = [
            'chat_enabled' => isset($_POST['chat_enabled']) ? 1 : 0,
            'chat_widget_position' => $_POST['chat_widget_position'] ?? 'bottom-right',
            'chat_widget_color' => $_POST['chat_widget_color'] ?? '#3498db',
            'chat_welcome_message' => $_POST['chat_welcome_message'] ?? 'Hello! How can we help you today?',
            'chat_offline_message' => $_POST['chat_offline_message'] ?? 'We are currently offline. Please leave a message.',
            'chat_auto_assign' => isset($_POST['chat_auto_assign']) ? 1 : 0,
            'chat_session_timeout' => (int)($_POST['chat_session_timeout'] ?? 30),
            'chat_require_email' => isset($_POST['chat_require_email']) ? 1 : 0,
            'chat_require_name' => isset($_POST['chat_require_name']) ? 1 : 0,
            'chat_enable_file_upload' => isset($_POST['chat_enable_file_upload']) ? 1 : 0,
            'chat_max_file_size' => (int)($_POST['chat_max_file_size'] ?? 5),
            'chat_enable_sound_notifications' => isset($_POST['chat_enable_sound_notifications']) ? 1 : 0,
            'chat_enable_email_notifications' => isset($_POST['chat_enable_email_notifications']) ? 1 : 0,
            'chat_notification_email' => $_POST['chat_notification_email'] ?? '',
            'chat_business_hours_enabled' => isset($_POST['chat_business_hours_enabled']) ? 1 : 0,
            'chat_business_hours_start' => $_POST['chat_business_hours_start'] ?? '09:00',
            'chat_business_hours_end' => $_POST['chat_business_hours_end'] ?? '17:00',
            'chat_business_days' => isset($_POST['chat_business_days']) ? implode(',', $_POST['chat_business_days']) : '1,2,3,4,5'
        ];
        
        // Build the UPDATE query
        $set_clauses = [];
        $params = [];
        foreach ($chat_config as $field => $value) {
            $set_clauses[] = "$field = ?";
            $params[] = $value;
        }
        
        $sql = "UPDATE setting_chat_config SET " . implode(', ', $set_clauses) . " WHERE id = 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $success_message = "Chat settings updated successfully!";
        
    } catch(PDOException $e) {
        $error_message = "Error updating settings: " . $e->getMessage();
    }
}

// Load current settings
$current_settings = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM setting_chat_config WHERE id = 1");
    $stmt->execute();
    $config = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($config) {
        $current_settings = $config;
    }
} catch(PDOException $e) {
    error_log("Error loading chat settings: " . $e->getMessage());
}

// Set default values if settings don't exist
$defaults = [
    'chat_enabled' => '0',
    'chat_widget_position' => 'bottom-right',
    'chat_widget_color' => '#3498db',
    'chat_welcome_message' => 'Hello! How can we help you today?',
    'chat_offline_message' => 'We are currently offline. Please leave a message.',
    'chat_auto_assign' => '1',
    'chat_session_timeout' => '30',
    'chat_require_email' => '0',
    'chat_require_name' => '1',
    'chat_enable_file_upload' => '1',
    'chat_max_file_size' => '5',
    'chat_enable_sound_notifications' => '1',
    'chat_enable_email_notifications' => '1',
    'chat_notification_email' => '',
    'chat_business_hours_enabled' => '0',
    'chat_business_hours_start' => '09:00',
    'chat_business_hours_end' => '17:00',
    'chat_business_days' => '1,2,3,4,5'
];

foreach ($defaults as $key => $default_value) {
    if (!isset($current_settings[$key])) {
        $current_settings[$key] = $default_value;
    }
}

// Include the admin template
include '../assets/includes/main.php';
?>

<div class="content">
    <h2>Chat Settings</h2>
    <p>Configure your live chat system settings and appearance.</p>
    
    <?php if ($success_message): ?>
        <div class="alert alert-success">
            <span class="icon">✅</span>
            <?= htmlspecialchars($success_message) ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div class="alert alert-danger">
            <span class="icon">⚠️</span>
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" class="settings-form">
        <!-- General Settings -->
        <div class="settings-section">
            <h3>General Settings</h3>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="chat_enabled" value="1" 
                           <?= $current_settings['chat_enabled'] ? 'checked' : '' ?>>
                    Enable Chat System
                </label>
                <small>Turn the live chat system on or off site-wide.</small>
            </div>
            
            <div class="form-group">
                <label for="chat_welcome_message">Welcome Message:</label>
                <textarea id="chat_welcome_message" name="chat_welcome_message" rows="3" required><?= htmlspecialchars($current_settings['chat_welcome_message']) ?></textarea>
                <small>Message displayed when a customer starts a new chat.</small>
            </div>
            
            <div class="form-group">
                <label for="chat_offline_message">Offline Message:</label>
                <textarea id="chat_offline_message" name="chat_offline_message" rows="3" required><?= htmlspecialchars($current_settings['chat_offline_message']) ?></textarea>
                <small>Message displayed when no operators are available.</small>
            </div>
            
            <div class="form-group">
                <label for="chat_session_timeout">Session Timeout (minutes):</label>
                <input type="number" id="chat_session_timeout" name="chat_session_timeout" 
                       value="<?= htmlspecialchars($current_settings['chat_session_timeout']) ?>" 
                       min="5" max="120" required>
                <small>How long to wait before considering a session inactive.</small>
            </div>
        </div>
        
        <!-- Widget Appearance -->
        <div class="settings-section">
            <h3>Widget Appearance</h3>
            
            <div class="form-group">
                <label for="chat_widget_position">Widget Position:</label>
                <select id="chat_widget_position" name="chat_widget_position" required>
                    <option value="bottom-right" <?= $current_settings['chat_widget_position'] === 'bottom-right' ? 'selected' : '' ?>>Bottom Right</option>
                    <option value="bottom-left" <?= $current_settings['chat_widget_position'] === 'bottom-left' ? 'selected' : '' ?>>Bottom Left</option>
                    <option value="top-right" <?= $current_settings['chat_widget_position'] === 'top-right' ? 'selected' : '' ?>>Top Right</option>
                    <option value="top-left" <?= $current_settings['chat_widget_position'] === 'top-left' ? 'selected' : '' ?>>Top Left</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="chat_widget_color">Widget Color:</label>
                <div class="color-input-group">
                    <input type="color" id="chat_widget_color" name="chat_widget_color" 
                           value="<?= htmlspecialchars($current_settings['chat_widget_color']) ?>" required>
                    <input type="text" value="<?= htmlspecialchars($current_settings['chat_widget_color']) ?>" 
                           readonly class="color-code">
                </div>
                <small>Primary color for the chat widget.</small>
            </div>
        </div>
        
        <!-- Customer Requirements -->
        <div class="settings-section">
            <h3>Customer Requirements</h3>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="chat_require_name" value="1" 
                           <?= $current_settings['chat_require_name'] ? 'checked' : '' ?>>
                    Require Customer Name
                </label>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="chat_require_email" value="1" 
                           <?= $current_settings['chat_require_email'] ? 'checked' : '' ?>>
                    Require Customer Email
                </label>
            </div>
        </div>
        
        <!-- Operator Settings -->
        <div class="settings-section">
            <h3>Operator Settings</h3>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="chat_auto_assign" value="1" 
                           <?= $current_settings['chat_auto_assign'] ? 'checked' : '' ?>>
                    Auto-assign Chats to Available Operators
                </label>
                <small>Automatically assign new chats to the next available operator.</small>
            </div>
        </div>
        
        <!-- File Upload Settings -->
        <div class="settings-section">
            <h3>File Upload Settings</h3>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="chat_enable_file_upload" value="1" 
                           <?= $current_settings['chat_enable_file_upload'] ? 'checked' : '' ?>>
                    Enable File Uploads
                </label>
                <small>Allow customers and operators to share files during chat.</small>
            </div>
            
            <div class="form-group">
                <label for="chat_max_file_size">Maximum File Size (MB):</label>
                <input type="number" id="chat_max_file_size" name="chat_max_file_size" 
                       value="<?= htmlspecialchars($current_settings['chat_max_file_size']) ?>" 
                       min="1" max="50" required>
            </div>
        </div>
        
        <!-- Notification Settings -->
        <div class="settings-section">
            <h3>Notification Settings</h3>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="chat_enable_sound_notifications" value="1" 
                           <?= $current_settings['chat_enable_sound_notifications'] ? 'checked' : '' ?>>
                    Enable Sound Notifications
                </label>
                <small>Play sound alerts for new messages.</small>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="chat_enable_email_notifications" value="1" 
                           <?= $current_settings['chat_enable_email_notifications'] ? 'checked' : '' ?>>
                    Enable Email Notifications
                </label>
                <small>Send email alerts for new chat sessions.</small>
            </div>
            
            <div class="form-group">
                <label for="chat_notification_email">Notification Email:</label>
                <input type="email" id="chat_notification_email" name="chat_notification_email" 
                       value="<?= htmlspecialchars($current_settings['chat_notification_email']) ?>">
                <small>Email address to receive chat notifications (leave blank to use admin email).</small>
            </div>
        </div>
        
        <!-- Business Hours -->
        <div class="settings-section">
            <h3>Business Hours</h3>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="chat_business_hours_enabled" value="1" 
                           <?= $current_settings['chat_business_hours_enabled'] ? 'checked' : '' ?>>
                    Enable Business Hours
                </label>
                <small>Only allow chat during specified business hours.</small>
            </div>
            
            <div class="business-hours-group">
                <div class="form-group">
                    <label for="chat_business_hours_start">Start Time:</label>
                    <input type="time" id="chat_business_hours_start" name="chat_business_hours_start" 
                           value="<?= htmlspecialchars($current_settings['chat_business_hours_start']) ?>">
                </div>
                
                <div class="form-group">
                    <label for="chat_business_hours_end">End Time:</label>
                    <input type="time" id="chat_business_hours_end" name="chat_business_hours_end" 
                           value="<?= htmlspecialchars($current_settings['chat_business_hours_end']) ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label>Business Days:</label>
                <div class="checkbox-group">
                    <?php
                    $days = [
                        '1' => 'Monday',
                        '2' => 'Tuesday', 
                        '3' => 'Wednesday',
                        '4' => 'Thursday',
                        '5' => 'Friday',
                        '6' => 'Saturday',
                        '7' => 'Sunday'
                    ];
                    $selected_days = explode(',', $current_settings['chat_business_days']);
                    
                    foreach ($days as $value => $label):
                    ?>
                        <label class="checkbox-label">
                            <input type="checkbox" name="chat_business_days[]" value="<?= $value ?>" 
                                   <?= in_array($value, $selected_days) ? 'checked' : '' ?>>
                            <?= $label ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="button button-primary">Save Settings</button>
            <a href="chat_dash.php" class="button button-secondary">Cancel</a>
        </div>
    </form>
</div>

<style>
.settings-form {
    max-width: 800px;
}

.settings-section {
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 25px;
}

.settings-section h3 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #2c3e50;
    border-bottom: 2px solid #ecf0f1;
    padding-bottom: 10px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #2c3e50;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="number"],
.form-group input[type="time"],
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
}

.form-group input[type="checkbox"] {
    margin-right: 8px;
}

.form-group small {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 13px;
}

.color-input-group {
    display: flex;
    gap: 10px;
    align-items: center;
}

.color-input-group input[type="color"] {
    width: 60px;
    height: 40px;
    border: 1px solid #ddd;
    border-radius: 6px;
    cursor: pointer;
}

.color-code {
    width: 100px !important;
    font-family: monospace;
}

.business-hours-group {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.checkbox-group {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 10px;
    margin-top: 10px;
}

.checkbox-label {
    display: flex !important;
    align-items: center;
    margin-bottom: 0 !important;
    font-weight: normal !important;
}

.form-actions {
    margin-top: 30px;
    display: flex;
    gap: 15px;
}

.alert {
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

@media (max-width: 768px) {
    .business-hours-group {
        grid-template-columns: 1fr;
    }
    
    .checkbox-group {
        grid-template-columns: 1fr 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>

<script>
// Update color code input when color picker changes
document.getElementById('chat_widget_color').addEventListener('input', function() {
    document.querySelector('.color-code').value = this.value;
});
</script>

<?php include '../assets/includes/footer.php'; ?>
