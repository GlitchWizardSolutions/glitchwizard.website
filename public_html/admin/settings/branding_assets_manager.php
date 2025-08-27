<?php
/**
 * Branding Assets Manager
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: branding_assets_manager.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Advanced logo and asset management with upload, optimization, and selection
 * 
 * This class provides comprehensive branding asset management including:
 * - File upload with automatic optimization
 * - SEO-friendly filename generation
 * - Smart duplicate handling
 * - Multiple image format support
 * - Automatic resizing and optimization
 * - Integration with database settings system
 * 
 * CREATED: 2025-08-17
 * VERSION: 1.0
 */

class BrandingAssetsManager {
    private $db;
    private $upload_dir;
    private $allowed_types;
    private $max_file_size;
    private $image_quality;
    
    public function __construct($database_connection) {
        $this->db = $database_connection;
        $this->upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/gws-universal-hybrid-app/public_html/assets/branding/';
        $this->allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        $this->max_file_size = 5 * 1024 * 1024; // 5MB
        $this->image_quality = 85; // JPEG quality
        
        // Ensure upload directory exists
        if (!file_exists($this->upload_dir)) {
            mkdir($this->upload_dir, 0755, true);
        }
    }
    
    /**
     * Upload and process logo file
     */
    public function uploadLogo($file, $logo_type, $custom_name = null) {
        try {
            // Validate file
            $validation = $this->validateFile($file);
            if (!$validation['valid']) {
                return ['success' => false, 'error' => $validation['error']];
            }
            
            // Generate SEO-friendly filename
            $filename = $this->generateFilename($file, $logo_type, $custom_name);
            
            // Handle duplicates
            $final_filename = $this->handleDuplicates($filename);
            
            // Get file paths
            $temp_path = $file['tmp_name'];
            $final_path = $this->upload_dir . $final_filename;
            
            // Process and optimize image
            $process_result = $this->processImage($temp_path, $final_path, $logo_type);
            if (!$process_result['success']) {
                return $process_result;
            }
            
            // Update database
            $db_result = $this->updateDatabaseAsset($logo_type, $final_filename);
            if (!$db_result['success']) {
                // Clean up uploaded file if database update fails
                if (file_exists($final_path)) {
                    unlink($final_path);
                }
                return $db_result;
            }
            
            return [
                'success' => true,
                'filename' => $final_filename,
                'url' => '/assets/branding/' . $final_filename,
                'message' => ucfirst(str_replace('_', ' ', $logo_type)) . ' uploaded and optimized successfully'
            ];
            
        } catch (Exception $e) {
            error_log("Logo upload error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Upload failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Validate uploaded file
     */
    private function validateFile($file) {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error_messages = [
                UPLOAD_ERR_INI_SIZE => 'File size exceeds server limit',
                UPLOAD_ERR_FORM_SIZE => 'File size exceeds form limit',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
            ];
            return ['valid' => false, 'error' => $error_messages[$file['error']] ?? 'Unknown upload error'];
        }
        
        // Check file size
        if ($file['size'] > $this->max_file_size) {
            return ['valid' => false, 'error' => 'File size must be less than ' . ($this->max_file_size / 1024 / 1024) . 'MB'];
        }
        
        // Check file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime_type, $this->allowed_types)) {
            return ['valid' => false, 'error' => 'File type not allowed. Please use JPEG, PNG, GIF, WebP, or SVG images.'];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Generate SEO-friendly filename
     */
    private function generateFilename($file, $logo_type, $custom_name = null) {
        $original_name = pathinfo($file['name'], PATHINFO_FILENAME);
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Use custom name if provided, otherwise use type + original name
        if ($custom_name) {
            $base_name = $this->sanitizeFilename($custom_name);
        } else {
            $base_name = $logo_type . '_' . $this->sanitizeFilename($original_name);
        }
        
        return $base_name . '.' . $extension;
    }
    
    /**
     * Sanitize filename for SEO
     */
    private function sanitizeFilename($filename) {
        // Convert to lowercase
        $filename = strtolower($filename);
        
        // Replace spaces and special characters with hyphens
        $filename = preg_replace('/[^a-z0-9]+/', '-', $filename);
        
        // Remove multiple consecutive hyphens
        $filename = preg_replace('/-+/', '-', $filename);
        
        // Remove leading/trailing hyphens
        $filename = trim($filename, '-');
        
        // Ensure we have something
        if (empty($filename)) {
            $filename = 'logo-' . date('Y-m-d');
        }
        
        return $filename;
    }
    
    /**
     * Handle duplicate filenames
     */
    private function handleDuplicates($filename) {
        $base_name = pathinfo($filename, PATHINFO_FILENAME);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $counter = 1;
        $final_filename = $filename;
        
        while (file_exists($this->upload_dir . $final_filename)) {
            $final_filename = $base_name . '-' . $counter . '.' . $extension;
            $counter++;
        }
        
        return $final_filename;
    }
    
    /**
     * Process and optimize image
     */
    private function processImage($source_path, $destination_path, $logo_type) {
        try {
            // Get image info
            $image_info = getimagesize($source_path);
            if (!$image_info) {
                return ['success' => false, 'error' => 'Invalid image file'];
            }
            
            $width = $image_info[0];
            $height = $image_info[1];
            $mime_type = $image_info['mime'];
            
            // Handle SVG files (no processing needed)
            if ($mime_type === 'image/svg+xml') {
                return move_uploaded_file($source_path, $destination_path) 
                    ? ['success' => true] 
                    : ['success' => false, 'error' => 'Failed to move SVG file'];
            }
            
            // Create source image resource
            switch ($mime_type) {
                case 'image/jpeg':
                    $source_image = imagecreatefromjpeg($source_path);
                    break;
                case 'image/png':
                    $source_image = imagecreatefrompng($source_path);
                    break;
                case 'image/gif':
                    $source_image = imagecreatefromgif($source_path);
                    break;
                case 'image/webp':
                    $source_image = imagecreatefromwebp($source_path);
                    break;
                default:
                    return ['success' => false, 'error' => 'Unsupported image format'];
            }
            
            if (!$source_image) {
                return ['success' => false, 'error' => 'Failed to create image resource'];
            }
            
            // Calculate optimal dimensions based on logo type
            $dimensions = $this->getOptimalDimensions($width, $height, $logo_type);
            
            // Create optimized image if resizing is needed
            if ($dimensions['resize']) {
                $optimized_image = imagecreatetruecolor($dimensions['width'], $dimensions['height']);
                
                // Preserve transparency for PNG/GIF
                if ($mime_type === 'image/png' || $mime_type === 'image/gif') {
                    imagealphablending($optimized_image, false);
                    imagesavealpha($optimized_image, true);
                    $transparent = imagecolorallocatealpha($optimized_image, 255, 255, 255, 127);
                    imagefill($optimized_image, 0, 0, $transparent);
                }
                
                // Resize image
                imagecopyresampled(
                    $optimized_image, $source_image,
                    0, 0, 0, 0,
                    $dimensions['width'], $dimensions['height'],
                    $width, $height
                );
                
                $final_image = $optimized_image;
            } else {
                $final_image = $source_image;
            }
            
            // Save optimized image
            $save_result = false;
            switch ($mime_type) {
                case 'image/jpeg':
                    $save_result = imagejpeg($final_image, $destination_path, $this->image_quality);
                    break;
                case 'image/png':
                    $save_result = imagepng($final_image, $destination_path, 9); // Max compression
                    break;
                case 'image/gif':
                    $save_result = imagegif($final_image, $destination_path);
                    break;
                case 'image/webp':
                    $save_result = imagewebp($final_image, $destination_path, $this->image_quality);
                    break;
            }
            
            // Clean up memory
            imagedestroy($source_image);
            if (isset($optimized_image)) {
                imagedestroy($optimized_image);
            }
            
            return $save_result 
                ? ['success' => true] 
                : ['success' => false, 'error' => 'Failed to save optimized image'];
                
        } catch (Exception $e) {
            return ['success' => false, 'error' => 'Image processing failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get optimal dimensions for logo type
     */
    private function getOptimalDimensions($width, $height, $logo_type) {
        $max_dimensions = [
            'business_logo_main' => ['width' => 400, 'height' => 200],
            'business_logo_horizontal' => ['width' => 300, 'height' => 100],
            'business_logo_vertical' => ['width' => 150, 'height' => 200],
            'business_logo_square' => ['width' => 200, 'height' => 200],
            'business_logo_white' => ['width' => 400, 'height' => 200],
            'business_logo_small' => ['width' => 100, 'height' => 50],
            'favicon_main' => ['width' => 32, 'height' => 32],
            'favicon_blog' => ['width' => 32, 'height' => 32],
            'favicon_portal' => ['width' => 32, 'height' => 32],
            'apple_touch_icon' => ['width' => 180, 'height' => 180],
            'social_share_default' => ['width' => 1200, 'height' => 630],
            'social_share_facebook' => ['width' => 1200, 'height' => 630],
            'social_share_twitter' => ['width' => 1200, 'height' => 600],
            'social_share_linkedin' => ['width' => 1200, 'height' => 627],
            'hero_background_image' => ['width' => 1920, 'height' => 1080],
        ];
        
        $max = $max_dimensions[$logo_type] ?? ['width' => 400, 'height' => 400];
        
        // Check if resizing is needed
        if ($width <= $max['width'] && $height <= $max['height']) {
            return ['resize' => false, 'width' => $width, 'height' => $height];
        }
        
        // Calculate new dimensions maintaining aspect ratio
        $aspect_ratio = $width / $height;
        
        if ($width > $height) {
            $new_width = $max['width'];
            $new_height = round($new_width / $aspect_ratio);
            
            if ($new_height > $max['height']) {
                $new_height = $max['height'];
                $new_width = round($new_height * $aspect_ratio);
            }
        } else {
            $new_height = $max['height'];
            $new_width = round($new_height * $aspect_ratio);
            
            if ($new_width > $max['width']) {
                $new_width = $max['width'];
                $new_height = round($new_width / $aspect_ratio);
            }
        }
        
        return ['resize' => true, 'width' => $new_width, 'height' => $new_height];
    }
    
    /**
     * Update database with new asset
     */
    public function updateDatabaseAsset($logo_type, $filename) {
        try {
            // Check if the field exists in the database
            $valid_fields = [
                'business_logo_main', 'business_logo_horizontal', 'business_logo_vertical',
                'business_logo_square', 'business_logo_white', 'business_logo_small',
                'favicon_main', 'favicon_blog', 'favicon_portal', 'apple_touch_icon',
                'social_share_default', 'social_share_facebook', 'social_share_twitter',
                'social_share_linkedin', 'social_share_instagram', 'social_share_blog',
                'hero_background_image', 'watermark_image', 'loading_animation'
            ];
            
            if (!in_array($logo_type, $valid_fields)) {
                return ['success' => false, 'error' => 'Invalid logo type'];
            }
            
            // Store relative path in database
            $relative_path = 'assets/branding/' . $filename;
            
            // Update the asset in the database
            $sql = "UPDATE setting_branding_assets SET {$logo_type} = ?, last_updated = CURRENT_TIMESTAMP WHERE id = 1";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$relative_path]);
            
            if (!$result) {
                return ['success' => false, 'error' => 'Failed to update database'];
            }
            
            return ['success' => true];
            
        } catch (Exception $e) {
            error_log("Database update error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Database update failed'];
        }
    }
    
    /**
     * Get all existing logos from assets folder
     */
    public function getExistingLogos() {
        $logos = [];
        
        if (!is_dir($this->upload_dir)) {
            return $logos;
        }
        
        $files = scandir($this->upload_dir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && !is_dir($this->upload_dir . $file)) {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                    $logos[] = [
                        'filename' => $file,
                        'url' => '/assets/branding/' . $file,
                        'size' => filesize($this->upload_dir . $file),
                        'modified' => filemtime($this->upload_dir . $file)
                    ];
                }
            }
        }
        
        // Sort by modification time (newest first)
        usort($logos, function($a, $b) {
            return $b['modified'] - $a['modified'];
        });
        
        return $logos;
    }
    
    /**
     * Delete logo file and update database
     */
    public function deleteLogo($filename, $logo_type = null) {
        try {
            $file_path = $this->upload_dir . $filename;
            
            // Check if file exists
            if (!file_exists($file_path)) {
                return ['success' => false, 'error' => 'File not found'];
            }
            
            // Delete physical file
            if (!unlink($file_path)) {
                return ['success' => false, 'error' => 'Failed to delete file'];
            }
            
            // If logo type is specified, clear it from database
            if ($logo_type) {
                $sql = "UPDATE setting_branding_assets SET {$logo_type} = NULL, last_updated = CURRENT_TIMESTAMP WHERE id = 1";
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
            }
            
            return ['success' => true, 'message' => 'Logo deleted successfully'];
            
        } catch (Exception $e) {
            error_log("Logo deletion error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Deletion failed'];
        }
    }
    
    /**
     * Get current branding assets from database
     */
    public function getCurrentAssets() {
        try {
            $stmt = $this->db->query("SELECT * FROM setting_branding_assets WHERE id = 1");
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log("Error getting current assets: " . $e->getMessage());
            return [];
        }
    }
}
