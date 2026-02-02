<?php
/**
 * Language Helper Class
 * Handles multi-language support for TechSmart
 */

class Language {
    private static $instance = null;
    private $lang = [];
    private $currentLang = 'vi';
    private $availableLangs = ['vi', 'en'];
    
    private function __construct() {
        // Get language from URL param, session, or default
        if (isset($_GET['lang']) && in_array($_GET['lang'], $this->availableLangs)) {
            $this->currentLang = $_GET['lang'];
            $_SESSION['lang'] = $this->currentLang;
        } elseif (isset($_SESSION['lang'])) {
            $this->currentLang = $_SESSION['lang'];
        }
        
        // Load language file
        $langFile = dirname(__DIR__) . '/lang/' . $this->currentLang . '.php';
        if (file_exists($langFile)) {
            $this->lang = require $langFile;
        }
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get translated string
     */
    public function get($key, $default = null) {
        return $this->lang[$key] ?? $default ?? $key;
    }
    
    /**
     * Get current language code
     */
    public function getCurrentLang() {
        return $this->currentLang;
    }
    
    /**
     * Get all available languages
     */
    public function getAvailableLangs() {
        return $this->availableLangs;
    }
    
    /**
     * Check if current language is...
     */
    public function is($lang) {
        return $this->currentLang === $lang;
    }
    
    /**
     * Get all translations
     */
    public function getAll() {
        return $this->lang;
    }
}

/**
 * Global helper function for translation
 * Usage: __('key') or __('key', 'default value')
 */
function __($key, $default = null) {
    return Language::getInstance()->get($key, $default);
}

/**
 * Echo translated string
 */
function _e($key, $default = null) {
    echo __($key, $default);
}

/**
 * Get current language
 */
function getCurrentLang() {
    return Language::getInstance()->getCurrentLang();
}
