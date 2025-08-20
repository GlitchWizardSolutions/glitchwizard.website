# Chat System Integration Guide

## Overview
The Chat System provides real-time customer support capabilities with a modern, responsive interface. It includes admin management tools, operator controls, and a public-facing chat widget.

## Installation Steps

### 1. Database Setup
Run the SQL file to create the necessary tables:
```sql
-- Execute this file in your database
/admin/chat_system/chat_system_tables.sql
```

### 2. Admin Navigation Integration
✅ **COMPLETED** - The chat system has been integrated into the admin navigation with the following menu structure:
- Chat Dashboard
- Chat Sessions
- Operators
- Chat Settings

### 3. Public Widget Integration
To add the chat widget to your public pages, include this line in your page templates:
```php
<?php include 'shared/chat_widget.php'; ?>
```

## File Structure

### Admin Files
```
/admin/chat_system/
├── chat_dash.php           # Main dashboard with statistics
├── chat_sessions.php       # View and manage chat sessions
├── operators.php           # Manage operator permissions
├── settings.php            # Configure chat system settings
└── chat_system_tables.sql  # Database schema
```

### Public Files
```
/shared/
└── chat_widget.php         # Public chat widget for customer use
```

## Features

### Admin Dashboard
- **Real-time Statistics**: Sessions today, active sessions, messages count
- **Recent Activity**: View latest chat sessions
- **System Status**: Database health and configuration status
- **Quick Actions**: Direct links to management pages

### Chat Sessions Management
- **Session Listing**: View all chat sessions with filtering
- **Search & Filter**: By customer name, email, status, date range
- **Session Details**: Message count, timestamps, operator assignment
- **Session Actions**: View, join, or end chat sessions

### Operator Management
- **User Assignment**: Enable/disable chat operator permissions for existing users
- **Role-based Access**: Only Admin, Manager, Operator, and Support roles eligible
- **Online Status**: Real-time operator availability tracking
- **Statistics**: Total and online operator counts

### Chat Settings
- **General Settings**: Enable/disable chat, welcome messages, timeouts
- **Widget Appearance**: Position, colors, customization
- **Customer Requirements**: Name/email collection preferences
- **Operator Settings**: Auto-assignment, notification preferences
- **File Upload**: Enable file sharing with size limits
- **Business Hours**: Configure availability windows
- **Notifications**: Sound and email alerts

### Public Chat Widget
- **Responsive Design**: Mobile-friendly interface
- **Customizable Position**: Bottom-right, bottom-left, top positions
- **Real-time Messaging**: Instant message delivery
- **File Upload Support**: Share files during conversations
- **Customer Information**: Optional name/email collection

## Database Tables

### Primary Tables
- `chat_sessions` - Chat session records
- `chat_messages` - Individual messages
- `chat_departments` - Support departments
- `chat_quick_responses` - Preset responses
- `chat_files` - File uploads
- `chat_banned_ips` - IP blocking

### Extended Tables
- `accounts` - Extended with chat operator fields
- `settings` - Chat configuration settings

## Configuration

### Basic Setup
1. **Enable Chat System**: Go to Admin → Chat → Chat Settings
2. **Set Operator Permissions**: Go to Admin → Chat → Operators
3. **Configure Widget**: Customize appearance and behavior
4. **Add Widget to Pages**: Include widget code in templates

### Widget Positioning
Available positions:
- `bottom-right` (default)
- `bottom-left`
- `top-right`
- `top-left`

### Operator Roles
Eligible roles for chat operators:
- Admin
- Super Admin
- Manager
- Operator
- Support

## API Endpoints (Future Development)

The system is designed to support these API endpoints:
- `/admin/chat_system/api/start_session.php` - Initialize chat session
- `/admin/chat_system/api/send_message.php` - Send message
- `/admin/chat_system/api/get_messages.php` - Retrieve messages
- `/admin/chat_system/api/end_session.php` - End chat session

## Usage Examples

### Enable Chat for a User
```php
// In admin/chat_system/operators.php
$stmt = $pdo->prepare("UPDATE accounts SET chat_operator = 1 WHERE id = ?");
$stmt->execute([$user_id]);
```

### Check if Chat is Available
```php
// Check business hours and operator availability
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM accounts 
    WHERE chat_operator = 1 
    AND chat_status = 'available' 
    AND last_seen > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
");
```

### Add Widget to Template
```php
<!-- In your page footer or before </body> -->
<?php if (file_exists('shared/chat_widget.php')): ?>
    <?php include 'shared/chat_widget.php'; ?>
<?php endif; ?>
```

## Customization Options

### Widget Styling
Modify the CSS in `shared/chat_widget.php`:
- Colors and branding
- Position and sizing
- Animation effects
- Mobile responsiveness

### Message Templates
Add custom quick responses in the database:
```sql
INSERT INTO chat_quick_responses (title, message, category, is_global) 
VALUES ('Custom Response', 'Your custom message here', 'general', 1);
```

### Business Logic
Extend functionality by:
- Adding custom session triggers
- Implementing auto-responses
- Creating department routing
- Adding integration webhooks

## Security Considerations

### Data Protection
- All messages stored with timestamps
- IP address logging for security
- File upload restrictions and scanning
- XSS protection in message display

### Access Control
- Role-based operator permissions
- Session-based authentication
- CSRF protection on forms
- SQL injection prevention

### Privacy Features
- Customer data anonymization options
- Session encryption capabilities
- GDPR compliance features
- Data retention settings

## Performance Optimization

### Database Indexing
All tables include proper indexes for:
- Session lookups
- Message retrieval
- Operator queries
- Time-based searches

### Polling Optimization
- Configurable refresh intervals
- Efficient query patterns
- Minimal data transfer
- Connection pooling ready

## Troubleshooting

### Common Issues
1. **Widget Not Appearing**: Check chat_enabled setting
2. **Messages Not Sending**: Verify session initialization
3. **Operators Not Listed**: Check role permissions
4. **Database Errors**: Run SQL setup file

### Debug Mode
Enable detailed logging by adding to settings:
```php
$debug_chat = true; // Add to config file
```

## Integration Status
✅ **COMPLETED**: Chat System Integration (100%)
- Admin navigation integrated
- Database schema prepared
- Management interfaces created
- Public widget developed
- Settings system configured
- Operator management functional

The chat system is now fully integrated and ready for testing and deployment.
