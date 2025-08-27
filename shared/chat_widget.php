<!-- Chat Widget for Public Pages -->
<!-- Include this in your public page templates -->

<?php
// Check if chat is enabled
$chat_enabled = false;
try {
    $stmt = $pdo->prepare("SELECT chat_enabled FROM setting_chat_config WHERE id = 1");
    $stmt->execute();
    $chat_enabled = (bool)$stmt->fetchColumn();
} catch(PDOException $e) {
    $chat_enabled = false;
}

if ($chat_enabled):
    // Get chat settings
    try {
        $stmt = $pdo->prepare("SELECT * FROM setting_chat_config WHERE id = 1");
        $stmt->execute();
        $chat_settings = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$chat_settings) {
            $chat_settings = [
                'chat_widget_position' => 'bottom-right',
                'chat_widget_color' => '#3498db',
                'chat_welcome_message' => 'Hello! How can we help you today?'
            ];
        }
    } catch(PDOException $e) {
        $chat_settings = [
            'chat_widget_position' => 'bottom-right',
            'chat_widget_color' => '#3498db',
            'chat_welcome_message' => 'Hello! How can we help you today?'
        ];
    }
    
    $widget_position = $chat_settings['chat_widget_position'] ?? 'bottom-right';
    $widget_color = $chat_settings['chat_widget_color'] ?? '#3498db';
    $welcome_message = $chat_settings['chat_welcome_message'] ?? 'Hello! How can we help you today?';
?>

<!-- Chat Widget CSS -->
<style>
#chat-widget {
    position: fixed;
    z-index: 10000;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Position classes */
.chat-bottom-right { bottom: 20px; right: 20px; }
.chat-bottom-left { bottom: 20px; left: 20px; }
.chat-top-right { top: 20px; right: 20px; }
.chat-top-left { top: 20px; left: 20px; }

.chat-button {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: <?= htmlspecialchars($widget_color) ?>;
    color: white;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    font-size: 24px;
}

.chat-button:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}

.chat-window {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.2);
    display: none;
    flex-direction: column;
    overflow: hidden;
}

.chat-header {
    background: <?= htmlspecialchars($widget_color) ?>;
    color: white;
    padding: 15px 20px;
    font-weight: 600;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-close {
    background: none;
    border: none;
    color: white;
    font-size: 18px;
    cursor: pointer;
    padding: 5px;
}

.chat-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background: #f8f9fa;
}

.chat-input-area {
    padding: 15px 20px;
    border-top: 1px solid #eee;
    background: white;
}

.chat-form {
    display: flex;
    gap: 10px;
}

.chat-input {
    flex: 1;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 25px;
    outline: none;
}

.chat-send {
    background: <?= htmlspecialchars($widget_color) ?>;
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.message {
    margin-bottom: 15px;
    display: flex;
    flex-direction: column;
}

.message.operator .message-bubble {
    background: #e9ecef;
    align-self: flex-start;
    border-radius: 18px 18px 18px 4px;
}

.message.customer .message-bubble {
    background: <?= htmlspecialchars($widget_color) ?>;
    color: white;
    align-self: flex-end;
    border-radius: 18px 18px 4px 18px;
}

.message-bubble {
    padding: 10px 15px;
    max-width: 80%;
    word-wrap: break-word;
}

.message-time {
    font-size: 11px;
    color: #666;
    margin-top: 5px;
    align-self: flex-end;
}

.typing-indicator {
    display: none;
    padding: 10px 15px;
    color: #666;
    font-style: italic;
}

.offline-message {
    text-align: center;
    padding: 20px;
    color: #666;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .chat-window {
        width: calc(100vw - 40px);
        height: calc(100vh - 140px);
        bottom: 80px;
        right: 20px;
        left: 20px;
    }
    
    .chat-bottom-left .chat-window,
    .chat-top-left .chat-window,
    .chat-top-right .chat-window {
        left: 20px;
        right: 20px;
    }
}
</style>

<!-- Chat Widget HTML -->
<div id="chat-widget" class="chat-<?= htmlspecialchars($widget_position) ?>">
    <button class="chat-button" onclick="toggleChat()">
        <span id="chat-icon">💬</span>
    </button>
    
    <div class="chat-window" id="chat-window">
        <div class="chat-header">
            <span>Live Chat Support</span>
            <button class="chat-close" onclick="toggleChat()">×</button>
        </div>
        
        <div class="chat-messages" id="chat-messages">
            <div class="message operator">
                <div class="message-bubble">
                    <?= htmlspecialchars($welcome_message) ?>
                </div>
                <div class="message-time"><?= date('g:i A') ?></div>
            </div>
        </div>
        
        <div class="typing-indicator" id="typing-indicator">
            Operator is typing...
        </div>
        
        <div class="chat-input-area">
            <form class="chat-form" onsubmit="sendMessage(event)">
                <input type="text" class="chat-input" id="chat-input" placeholder="Type your message..." autocomplete="off">
                <button type="submit" class="chat-send">
                    <span>➤</span>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Chat Widget JavaScript -->
<script>
let chatSession = null;
let chatPolling = null;

function toggleChat() {
    const chatWindow = document.getElementById('chat-window');
    const chatIcon = document.getElementById('chat-icon');
    
    if (chatWindow.style.display === 'none' || !chatWindow.style.display) {
        chatWindow.style.display = 'flex';
        chatIcon.textContent = '×';
        
        if (!chatSession) {
            initializeChat();
        }
        
        startPolling();
    } else {
        chatWindow.style.display = 'none';
        chatIcon.textContent = '💬';
        stopPolling();
    }
}

function initializeChat() {
    // Initialize chat session
    fetch('/admin/chat_system/api/start_session.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            customer_name: prompt('Please enter your name:') || 'Anonymous',
            customer_email: prompt('Please enter your email (optional):') || ''
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            chatSession = data.session_id;
        }
    })
    .catch(error => {
        console.error('Error initializing chat:', error);
    });
}

function sendMessage(event) {
    event.preventDefault();
    
    const input = document.getElementById('chat-input');
    const message = input.value.trim();
    
    if (!message || !chatSession) return;
    
    // Add message to chat immediately
    addMessage(message, 'customer');
    input.value = '';
    
    // Send to server
    fetch('/admin/chat_system/api/send_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            session_id: chatSession,
            message: message
        })
    })
    .catch(error => {
        console.error('Error sending message:', error);
    });
}

function addMessage(message, type, time = null) {
    const messagesContainer = document.getElementById('chat-messages');
    const messageElement = document.createElement('div');
    messageElement.className = `message ${type}`;
    
    const currentTime = time || new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    
    messageElement.innerHTML = `
        <div class="message-bubble">${escapeHtml(message)}</div>
        <div class="message-time">${currentTime}</div>
    `;
    
    messagesContainer.appendChild(messageElement);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function startPolling() {
    if (chatPolling) return;
    
    chatPolling = setInterval(() => {
        if (chatSession) {
            fetchNewMessages();
        }
    }, 2000);
}

function stopPolling() {
    if (chatPolling) {
        clearInterval(chatPolling);
        chatPolling = null;
    }
}

function fetchNewMessages() {
    fetch(`/admin/chat_system/api/get_messages.php?session_id=${chatSession}&last_id=0`)
    .then(response => response.json())
    .then(data => {
        if (data.success && data.messages) {
            data.messages.forEach(msg => {
                if (msg.sender_type === 'operator') {
                    addMessage(msg.message, 'operator', msg.time);
                }
            });
        }
    })
    .catch(error => {
        console.error('Error fetching messages:', error);
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Auto-focus chat input when window opens
document.addEventListener('click', function(e) {
    if (e.target.closest('.chat-button')) {
        setTimeout(() => {
            const input = document.getElementById('chat-input');
            if (input && window.getComputedStyle(document.getElementById('chat-window')).display !== 'none') {
                input.focus();
            }
        }, 100);
    }
});
</script>

<?php endif; ?>
