<?php
/**
 * CSS Consolidation & Optimization Script
 * 
 * PURPOSE: Remove duplicate CSS files and create unified asset structure
 * FEATURES: 
 * - Bootstrap deduplication
 * - CSS file analysis
 * - Space usage reporting
 * - Safe file cleanup
 * 
 * VERSION: 1.0
 * CREATED: 2025-08-17
 */

class CSSOptimizer {
    private $workspace_root;
    private $duplicates = [];
    private $space_saved = 0;
    private $files_removed = 0;
    
    public function __construct($workspace_root) {
        $this->workspace_root = rtrim($workspace_root, '/\\');
    }
    
    /**
     * Analyze CSS files and find duplicates
     */
    public function analyze() {
        echo "<h2>🔍 CSS Analysis Report</h2>";
        
        // Find all CSS files
        $css_files = $this->findAllCSSFiles();
        echo "<p><strong>Total CSS files found:</strong> " . count($css_files) . "</p>";
        
        // Analyze Bootstrap files
        $bootstrap_files = $this->findBootstrapFiles($css_files);
        echo "<h3>📦 Bootstrap Files Analysis</h3>";
        echo "<p><strong>Bootstrap files found:</strong> " . count($bootstrap_files) . "</p>";
        
        $total_bootstrap_size = 0;
        echo "<table class='table table-striped'>";
        echo "<thead><tr><th>File</th><th>Size</th><th>Version</th><th>Status</th></tr></thead><tbody>";
        
        foreach ($bootstrap_files as $file) {
            $size = file_exists($file) ? filesize($file) : 0;
            $total_bootstrap_size += $size;
            $version = $this->detectBootstrapVersion($file);
            $status = $this->shouldKeepBootstrapFile($file) ? 'Keep' : 'Remove';
            
            echo "<tr>";
            echo "<td>" . $this->relativePath($file) . "</td>";
            echo "<td>" . $this->formatBytes($size) . "</td>";
            echo "<td>{$version}</td>";
            echo "<td><span class='badge bg-" . ($status === 'Keep' ? 'success' : 'warning') . "'>{$status}</span></td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        echo "<p><strong>Total Bootstrap size:</strong> " . $this->formatBytes($total_bootstrap_size) . "</p>";
        
        // Analyze other duplicate CSS
        $this->findDuplicateCSS($css_files);
        
        return $this->getDuplicatesForRemoval();
    }
    
    /**
     * Execute the optimization
     */
    public function optimize($dry_run = true) {
        echo "<h2>🛠️ " . ($dry_run ? "Optimization Preview" : "Executing Optimization") . "</h2>";
        
        $duplicates = $this->getDuplicatesForRemoval();
        
        if (empty($duplicates)) {
            echo "<div class='alert alert-info'>No duplicate files found to remove.</div>";
            return;
        }
        
        echo "<h3>Files to be " . ($dry_run ? "removed" : "removed") . ":</h3>";
        echo "<ul>";
        
        foreach ($duplicates as $file) {
            if (file_exists($file)) {
                $size = filesize($file);
                echo "<li>";
                echo $this->relativePath($file) . " (" . $this->formatBytes($size) . ")";
                
                if (!$dry_run) {
                    if (unlink($file)) {
                        echo " <span class='badge bg-success'>Removed</span>";
                        $this->space_saved += $size;
                        $this->files_removed++;
                    } else {
                        echo " <span class='badge bg-danger'>Failed</span>";
                    }
                } else {
                    $this->space_saved += $size;
                    $this->files_removed++;
                }
                echo "</li>";
            }
        }
        echo "</ul>";
        
        echo "<div class='alert alert-success'>";
        echo "<h4>Optimization Results:</h4>";
        echo "<ul>";
        echo "<li><strong>Files " . ($dry_run ? "to be removed" : "removed") . ":</strong> {$this->files_removed}</li>";
        echo "<li><strong>Space " . ($dry_run ? "to be saved" : "saved") . ":</strong> " . $this->formatBytes($this->space_saved) . "</li>";
        echo "<li><strong>Reduction:</strong> " . round(($this->space_saved / (1024 * 1024)), 2) . " MB</li>";
        echo "</ul>";
        echo "</div>";
    }
    
    /**
     * Create unified asset structure
     */
    public function createUnifiedStructure() {
        echo "<h2>🏗️ Creating Unified Asset Structure</h2>";
        
        $assets_dir = $this->workspace_root . '/public_html/assets';
        $css_dir = $assets_dir . '/css';
        $js_dir = $assets_dir . '/js';
        $vendor_dir = $assets_dir . '/vendor';
        
        // Create directories if they don't exist
        $dirs = [$assets_dir, $css_dir, $js_dir, $vendor_dir];
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
                echo "<p>✓ Created directory: " . $this->relativePath($dir) . "</p>";
            }
        }
        
        // Copy the best Bootstrap version to vendor directory
        $this->copyBestBootstrapVersion($vendor_dir);
        
        // Create CSS load order file
        $this->createCSSLoadOrder($css_dir);
        
        echo "<div class='alert alert-success'>Unified asset structure created successfully!</div>";
    }
    
    /**
     * Find all CSS files in workspace
     */
    private function findAllCSSFiles() {
        $css_files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->workspace_root)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'css') {
                $css_files[] = $file->getPathname();
            }
        }
        
        return $css_files;
    }
    
    /**
     * Find Bootstrap CSS files
     */
    private function findBootstrapFiles($css_files) {
        return array_filter($css_files, function($file) {
            return stripos(basename($file), 'bootstrap') !== false;
        });
    }
    
    /**
     * Detect Bootstrap version from file content or path
     */
    private function detectBootstrapVersion($file) {
        // Check path for version indicators
        if (strpos($file, '4.6.2') !== false) return '4.6.2';
        if (strpos($file, 'bootstrap-4') !== false) return '4.x';
        
        // Check file content for version
        if (file_exists($file) && is_readable($file)) {
            $content = file_get_contents($file, false, null, 0, 1000);
            if (preg_match('/Bootstrap v?(\d+\.\d+\.?\d*)/i', $content, $matches)) {
                return $matches[1];
            }
            if (strpos($content, 'Bootstrap v5') !== false) return '5.x';
            if (strpos($content, 'Bootstrap v4') !== false) return '4.x';
        }
        
        return 'Unknown';
    }
    
    /**
     * Determine if Bootstrap file should be kept
     */
    private function shouldKeepBootstrapFile($file) {
        // Keep only one main Bootstrap file - prefer version 5.x and minified
        $basename = basename($file);
        
        // Keep if it's in the main assets directory
        if (strpos($file, '/assets/vendor/') !== false) return true;
        
        // Keep the first Bootstrap 5.x minified file we find
        static $kept_main = false;
        if (!$kept_main && strpos($file, 'bootstrap.min.css') !== false) {
            $version = $this->detectBootstrapVersion($file);
            if (strpos($version, '5') === 0 || $version === 'Unknown') {
                $kept_main = true;
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Find duplicate CSS files by content hash
     */
    private function findDuplicateCSS($css_files) {
        echo "<h3>🔍 Duplicate CSS Analysis</h3>";
        
        $hashes = [];
        $duplicates = [];
        
        foreach ($css_files as $file) {
            if (!file_exists($file) || !is_readable($file)) continue;
            
            $hash = md5_file($file);
            if (isset($hashes[$hash])) {
                $duplicates[] = $file;
                echo "<p>📄 Duplicate found: " . $this->relativePath($file) . 
                     " (same as " . $this->relativePath($hashes[$hash]) . ")</p>";
            } else {
                $hashes[$hash] = $file;
            }
        }
        
        if (empty($duplicates)) {
            echo "<p><em>No exact duplicate CSS files found.</em></p>";
        } else {
            echo "<p><strong>Total duplicates:</strong> " . count($duplicates) . "</p>";
        }
    }
    
    /**
     * Get list of files to remove
     */
    private function getDuplicatesForRemoval() {
        $remove_list = [];
        
        // Find all Bootstrap files
        $css_files = $this->findAllCSSFiles();
        $bootstrap_files = $this->findBootstrapFiles($css_files);
        
        foreach ($bootstrap_files as $file) {
            if (!$this->shouldKeepBootstrapFile($file)) {
                $remove_list[] = $file;
            }
        }
        
        return $remove_list;
    }
    
    /**
     * Copy the best Bootstrap version to vendor directory
     */
    private function copyBestBootstrapVersion($vendor_dir) {
        $css_files = $this->findAllCSSFiles();
        $bootstrap_files = $this->findBootstrapFiles($css_files);
        
        // Find the best Bootstrap file (prefer 5.x minified)
        $best_file = null;
        foreach ($bootstrap_files as $file) {
            if (strpos(basename($file), 'bootstrap.min.css') !== false) {
                $version = $this->detectBootstrapVersion($file);
                if (strpos($version, '5') === 0 || $version === 'Unknown') {
                    $best_file = $file;
                    break;
                }
            }
        }
        
        if ($best_file && file_exists($best_file)) {
            $dest = $vendor_dir . '/bootstrap.min.css';
            if (copy($best_file, $dest)) {
                echo "<p>✓ Copied best Bootstrap file to: " . $this->relativePath($dest) . "</p>";
            }
        }
    }
    
    /**
     * Create CSS load order file
     */
    private function createCSSLoadOrder($css_dir) {
        $load_order = [
            'vendor/bootstrap.min.css',
            'gws-universal-base.css',
            'blog-system-fixes.css',
            'brand-colors.css'
        ];
        
        $php_content = '<?php
/**
 * CSS Load Order Configuration
 * AUTO-GENERATED - DO NOT EDIT MANUALLY
 */

return [
    "css_files" => [';
        
        foreach ($load_order as $file) {
            $php_content .= "\n        'assets/css/{$file}',";
        }
        
        $php_content .= '
    ],
    "load_order" => "critical_first",
    "minify" => true,
    "combine" => false
];';
        
        file_put_contents($css_dir . '/load-order.php', $php_content);
        echo "<p>✓ Created CSS load order configuration</p>";
    }
    
    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Get relative path from workspace root
     */
    private function relativePath($path) {
        return str_replace($this->workspace_root . DIRECTORY_SEPARATOR, '', $path);
    }
}

// Bootstrap for web interface
if (isset($_SERVER['REQUEST_METHOD'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>CSS Optimization Tool</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            .container { margin-top: 2rem; }
            .table { font-size: 0.9rem; }
            pre { background: #f8f9fa; padding: 1rem; border-radius: 0.375rem; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🎨 CSS Optimization Tool</h1>
            <p class="lead">Analyze and optimize CSS files for GWS Universal Hybrid App</p>
            
            <?php
            $workspace = dirname(dirname(__DIR__));
            $optimizer = new CSSOptimizer($workspace);
            
            $action = $_GET['action'] ?? 'analyze';
            
            echo '<nav class="mb-4">';
            echo '<a href="?action=analyze" class="btn btn-primary me-2">Analyze</a>';
            echo '<a href="?action=preview" class="btn btn-warning me-2">Preview Optimization</a>';
            echo '<a href="?action=optimize" class="btn btn-danger me-2">Execute Optimization</a>';
            echo '<a href="?action=structure" class="btn btn-primary">Create Structure</a>';
            echo '</nav>';
            
            switch ($action) {
                case 'analyze':
                    $optimizer->analyze();
                    break;
                case 'preview':
                    $optimizer->optimize(true);
                    break;
                case 'optimize':
                    $optimizer->optimize(false);
                    break;
                case 'structure':
                    $optimizer->createUnifiedStructure();
                    break;
            }
            ?>
            
            <hr>
            <div class="alert alert-info">
                <h5>📋 Next Steps:</h5>
                <ol>
                    <li><strong>Analyze</strong> - Review current CSS file status</li>
                    <li><strong>Preview</strong> - See what will be optimized (safe)</li>
                    <li><strong>Execute</strong> - Remove duplicate files (backup first!)</li>
                    <li><strong>Create Structure</strong> - Set up unified asset system</li>
                </ol>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>
