<?php
/**
 * DOCUMENTS SYSTEM CONFIGURATION
 * Template file for documents system settings
 * 
 * This file serves as a template for the documents system configuration.
 * Do not modify this template. Instead, copy it to documents-config.php
 * and make changes there.
 */

// Prevent direct access to this file
if (!defined('PROJECT_ROOT')) {
    die('Direct access to this file is not allowed');
}

// Documents System Settings
$documents_settings = [
    // PDF Generation Settings
    'pdf_engine' => 'mPDF', // or 'TCPDF', 'FPDF'
    'default_paper_size' => 'A4',
    'default_font' => 'DejaVu Sans',
    'default_font_size' => 12,
    
    // Document Storage Settings
    'storage_method' => 'filesystem', // or 's3', 'azure'
    'max_file_size' => '25MB',
    'allowed_file_types' => ['pdf', 'doc', 'docx', 'txt', 'rtf'],
    
    // Template Settings
    'enable_templates' => true,
    'default_template' => 'business-letter',
    'header_template' => 'default-header',
    'footer_template' => 'default-footer',
    
    // Invoice Settings
    'invoice_prefix' => 'INV-',
    'invoice_start_number' => 1000,
    'invoice_template' => 'default-invoice',
    'currency_symbol' => '$',
    'tax_rate' => 0.00,
    
    // Security Settings
    'enable_watermark' => false,
    'watermark_text' => 'CONFIDENTIAL',
    'enable_encryption' => false,
    'enable_digital_signature' => false,
    
    // Feature Toggles
    'enable_ocr' => false,
    'enable_batch_processing' => true,
    'enable_document_preview' => true
];

// Font Configuration
$font_settings = [
    'default_fonts' => [
        'DejaVu Sans' => [
            'R' => 'DejaVuSans.ttf',
            'B' => 'DejaVuSans-Bold.ttf',
            'I' => 'DejaVuSans-Oblique.ttf',
            'BI' => 'DejaVuSans-BoldOblique.ttf'
        ]
    ],
    'font_directories' => [
        PROJECT_ROOT . '/public_html/documents_system/fonts',
        PROJECT_ROOT . '/public_html/documents_system/pdf-driver/fonts'
    ]
];

// Documents System Paths
$documents_paths = [
    'storage' => PROJECT_ROOT . '/public_html/documents_system/storage',
    'templates' => PROJECT_ROOT . '/public_html/documents_system/templates',
    'temp' => PROJECT_ROOT . '/public_html/documents_system/temp',
    'fonts' => PROJECT_ROOT . '/public_html/documents_system/fonts',
    'cache' => PROJECT_ROOT . '/public_html/documents_system/cache'
];

// Return the configuration
return [
    'settings' => $documents_settings,
    'fonts' => $font_settings,
    'paths' => $documents_paths
];
