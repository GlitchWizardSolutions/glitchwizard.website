<?php
/**
 * File: custom-template-manager.php
 * Description: Handles saving and loading of custom Summernote templates for reuse.
 * Functions:
 *   - Save template to database
 *   - Load saved templates
 *   - Delete templates
 * Expected Outputs:
 *   - JSON confirmation or data output
 * Related Files:
 *   - dashboard.php
 *   - sample-schema.sql (templates table)
 */

require_once '../../private/gws-universal-config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $templateName = trim($_POST['template_name'] ?? '');
    $templateHTML = $_POST['template_html'] ?? '';

    if ($action === 'save' && $templateName && $templateHTML) {
        $stmt = $pdo->prepare("INSERT INTO templates (name, html) VALUES (?, ?) ON DUPLICATE KEY UPDATE html = VALUES(html)");
        $stmt->execute([$templateName, $templateHTML]);
        echo json_encode(['status' => 'success', 'message' => 'Template saved.']);
        exit;
    }

    if ($action === 'delete' && $templateName) {
        $stmt = $pdo->prepare("DELETE FROM templates WHERE name = ?");
        $stmt->execute([$templateName]);
        echo json_encode(['status' => 'success', 'message' => 'Template deleted.']);
        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['list'])) {
    $stmt = $pdo->query("SELECT name FROM templates ORDER BY name");
    $names = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode($names);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['load'])) {
    $name = $_GET['load'];
    $stmt = $pdo->prepare("SELECT html FROM templates WHERE name = ? LIMIT 1");
    $stmt->execute([$name]);
    $html = $stmt->fetchColumn();
    echo json_encode(['html' => $html]);
    exit;
}

http_response_code(400);
echo json_encode([
    'status' => 'error',
    'message' => 'Unsupported request. Supported: POST (action=save|delete, template_name, template_html), GET (list, load)'
]);