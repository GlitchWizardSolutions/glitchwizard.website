<?php
/* 
 * Configuration Validator
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: config-validator.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Validate configuration files against their templates
 * DETAILED DESCRIPTION:
 * This file provides a validation system for checking configuration files
 * against their templates. It ensures that all required settings are present
 * and properly formatted, helping prevent configuration-related issues and
 * maintaining system integrity.
 * REQUIRED FILES: 
 * - /public_html/assets/includes/main.php
 * - /public_html/admin/settings/admin-config.template.php
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - Configuration validation
 * - Template comparison
 * - Error reporting
 * - Missing field detection
 * - Type checking
 */

class ConfigValidator {
    private $errors = [];
    private $warnings = [];

    /**
     * Validate a config array against its template
     * @param array $config The loaded config array
     * @param string $templatePath Path to the template file
     * @return bool True if validation passes
     */
    public function validateConfig($config, $templatePath) {
        if (!file_exists($templatePath)) {
            $this->errors[] = "Template file not found: {$templatePath}";
            return false;
        }

        // Load template
        $template = include($templatePath);
        
        // Check basic structure
        if (!is_array($config)) {
            $this->errors[] = "Configuration must be an array";
            return false;
        }

        // Validate each section
        foreach (['settings', 'paths'] as $section) {
            if (!isset($template[$section])) {
                continue; // Section not required in template
            }

            if (!isset($config[$section])) {
                $this->errors[] = "Missing required section: {$section}";
                continue;
            }

            $this->validateSection($config[$section], $template[$section], $section);
        }

        return empty($this->errors);
    }

    /**
     * Validate a specific section of the config
     * @param array $section The section to validate
     * @param array $template The template section to validate against
     * @param string $sectionName Name of the section for error reporting
     */
    private function validateSection($section, $template, $sectionName) {
        foreach ($template as $key => $value) {
            // Check if required setting exists
            if (!isset($section[$key])) {
                $this->errors[] = "Missing required setting in {$sectionName}: {$key}";
                continue;
            }

            // Validate type
            if (gettype($section[$key]) !== gettype($value)) {
                $this->errors[] = "Invalid type for {$sectionName}.{$key}: Expected " . gettype($value) . ", got " . gettype($section[$key]);
            }

            // Validate specific types
            switch (gettype($value)) {
                case 'array':
                    $this->validateArray($section[$key], $value, "{$sectionName}.{$key}");
                    break;
                case 'string':
                    $this->validateString($section[$key], $key);
                    break;
                case 'integer':
                    $this->validateInteger($section[$key], $key);
                    break;
                case 'boolean':
                    // Boolean values are valid by type check
                    break;
            }
        }

        // Check for unexpected settings
        foreach ($section as $key => $value) {
            if (!isset($template[$key])) {
                $this->warnings[] = "Unexpected setting in {$sectionName}: {$key}";
            }
        }
    }

    /**
     * Validate array values
     */
    private function validateArray($array, $template, $path) {
        if (empty($template)) {
            return; // Template is empty array, any array content is valid
        }

        // If template has numeric keys, it's a list of allowed values
        if (isset($template[0])) {
            foreach ($array as $value) {
                if (!in_array($value, $template)) {
                    $this->warnings[] = "Value in {$path} not in allowed list: {$value}";
                }
            }
        }
    }

    /**
     * Validate string values
     */
    private function validateString($value, $key) {
        // Validate paths
        if (strpos($key, 'path') !== false || strpos($key, 'directory') !== false) {
            if (!file_exists($value)) {
                $this->warnings[] = "Path does not exist: {$value}";
            }
        }

        // Validate email addresses
        if (strpos($key, 'email') !== false) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $this->errors[] = "Invalid email address: {$value}";
            }
        }
    }

    /**
     * Validate integer values
     */
    private function validateInteger($value, $key) {
        // Validate positive numbers where appropriate
        if (strpos($key, 'max') !== false || strpos($key, 'length') !== false || 
            strpos($key, 'size') !== false || strpos($key, 'count') !== false) {
            if ($value <= 0) {
                $this->errors[] = "Value must be positive: {$key} = {$value}";
            }
        }
    }

    /**
     * Get validation errors
     * @return array Array of error messages
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Get validation warnings
     * @return array Array of warning messages
     */
    public function getWarnings() {
        return $this->warnings;
    }

    /**
     * Check if validation has errors
     * @return bool True if there are errors
     */
    public function hasErrors() {
        return !empty($this->errors);
    }

    /**
     * Check if validation has warnings
     * @return bool True if there are warnings
     */
    public function hasWarnings() {
        return !empty($this->warnings);
    }
}

// Example usage:
/*
$validator = new ConfigValidator();
$config = include('blog-config.php');
$templatePath = 'blog-config.template.php';

if (!$validator->validateConfig($config, $templatePath)) {
    echo "Configuration validation failed:\n";
    foreach ($validator->getErrors() as $error) {
        echo "- {$error}\n";
    }
    die("Please fix configuration errors before continuing.");
}

if ($validator->hasWarnings()) {
    echo "Configuration warnings:\n";
    foreach ($validator->getWarnings() as $warning) {
        echo "- {$warning}\n";
    }
}
*/
