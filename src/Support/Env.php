<?php

namespace SwiftPHP\Support;

class Env
{
    private static array $values = [];
    private static bool $loaded = false;

    public static function load(string $path): void
    {
        if (self::$loaded) {
            return;
        }

        $envFile = rtrim($path, '/') . '/.env';
        
        if (!file_exists($envFile)) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse KEY=VALUE
            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes
                $value = trim($value, '"\'');
                
                self::$values[$key] = $value;
                
                // Also set in $_ENV and putenv
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }

        self::$loaded = true;
    }

    public static function get(string $key, $default = null)
    {
        // Check in-memory values first
        if (isset(self::$values[$key])) {
            return self::parseValue(self::$values[$key]);
        }
        
        // Check $_ENV
        if (isset($_ENV[$key])) {
            return self::parseValue($_ENV[$key]);
        }
        
        // Check getenv
        $value = getenv($key);
        if ($value !== false) {
            return self::parseValue($value);
        }
        
        return $default;
    }

    private static function parseValue($value)
    {
        if ($value === 'true' || $value === '(true)') {
            return true;
        }
        
        if ($value === 'false' || $value === '(false)') {
            return false;
        }
        
        if ($value === 'null' || $value === '(null)') {
            return null;
        }
        
        if (is_numeric($value)) {
            return strpos($value, '.') !== false ? (float) $value : (int) $value;
        }
        
        return $value;
    }
}

// Helper function
if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        return \SwiftPHP\Support\Env::get($key, $default);
    }
}
