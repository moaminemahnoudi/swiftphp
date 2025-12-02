<?php

namespace SwiftPHP\Error;

use Throwable;

/**
 * SwiftPHP Error Handler with AI-Powered Hints
 * 
 * Provides detailed error pages with intelligent suggestions
 * for common issues and their solutions
 */
class ErrorHandler
{
    protected static bool $registered = false;
    protected static array $errorLog = [];

    /**
     * Register error and exception handlers
     */
    public static function register(): void
    {
        if (self::$registered) {
            return;
        }

        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);

        self::$registered = true;
    }

    /**
     * Handle PHP errors
     */
    public static function handleError(int $level, string $message, string $file = '', int $line = 0): bool
    {
        if (!(error_reporting() & $level)) {
            return false;
        }

        throw new \ErrorException($message, 0, $level, $file, $line);
    }

    /**
     * Handle uncaught exceptions
     */
    public static function handleException(Throwable $exception): void
    {
        self::logError($exception);

        if (self::isAjaxRequest()) {
            self::renderJsonError($exception);
        } else {
            self::renderHtmlError($exception);
        }

        exit(1);
    }

    /**
     * Handle fatal errors
     */
    public static function handleShutdown(): void
    {
        $error = error_get_last();

        if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            $exception = new \ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            );

            self::handleException($exception);
        }
    }

    /**
     * Log error to file
     */
    protected static function logError(Throwable $exception): void
    {
        $logDir = __DIR__ . '/../../storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/error_' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        
        $logMessage = sprintf(
            "[%s] %s: %s in %s:%d\nStack trace:\n%s\n\n",
            $timestamp,
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );

        @file_put_contents($logFile, $logMessage, FILE_APPEND);

        self::$errorLog[] = [
            'time' => $timestamp,
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine()
        ];
    }

    /**
     * Check if request is AJAX
     */
    protected static function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Render JSON error for AJAX requests
     */
    protected static function renderJsonError(Throwable $exception): void
    {
        http_response_code(500);
        header('Content-Type: application/json');

        $response = [
            'error' => true,
            'message' => $exception->getMessage(),
            'type' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'hint' => self::getAIHint($exception)
        ];

        if (self::isDebugMode()) {
            $response['trace'] = $exception->getTraceAsString();
        }

        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    /**
     * Render HTML error page with AI hints
     */
    protected static function renderHtmlError(Throwable $exception): void
    {
        http_response_code(500);
        
        $errorType = get_class($exception);
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTraceAsString();
        
        $aiHint = self::getAIHint($exception);
        $codeSnippet = self::getCodeSnippet($file, $line);
        $solution = self::getSolution($exception);

        include __DIR__ . '/error_template.php';
    }

    /**
     * Get AI-powered hint based on error type and message
     */
    protected static function getAIHint(Throwable $exception): array
    {
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $errorType = get_class($exception);

        // Database errors
        if (stripos($message, 'database') !== false || stripos($message, 'SQL') !== false) {
            return [
                'category' => 'Database',
                'icon' => '🗄️',
                'title' => 'Database Connection Issue',
                'problem' => 'The application cannot connect to or query the database.',
                'likely_causes' => [
                    'Database credentials are incorrect in .env file',
                    'Database server is not running',
                    'Database name doesn\'t exist',
                    'Table or column names are misspelled',
                    'Missing database migration'
                ],
                'quick_fixes' => [
                    'Check config/database.php and .env file',
                    'Run: swiftphp migrate',
                    'Verify database server is running',
                    'Check table names match your models'
                ]
            ];
        }

        // Class not found errors
        if (stripos($message, 'Class') !== false && stripos($message, 'not found') !== false) {
            return [
                'category' => 'Autoloading',
                'icon' => '📦',
                'title' => 'Class Not Found',
                'problem' => 'PHP cannot find the class you\'re trying to use.',
                'likely_causes' => [
                    'Missing namespace declaration',
                    'Incorrect use statement',
                    'File not autoloaded (composer)',
                    'Typo in class name',
                    'File not in correct directory'
                ],
                'quick_fixes' => [
                    'Run: composer dump-autoload',
                    'Check namespace matches directory structure',
                    'Verify use statements at top of file',
                    'Check class name spelling'
                ]
            ];
        }

        // File not found errors
        if (stripos($message, 'No such file') !== false || stripos($message, 'failed to open') !== false) {
            return [
                'category' => 'File System',
                'icon' => '📁',
                'title' => 'File Not Found',
                'problem' => 'A required file or directory is missing.',
                'likely_causes' => [
                    'View file doesn\'t exist',
                    'Incorrect file path',
                    'File permissions issue',
                    'Missing storage directory',
                    'Case-sensitive file system'
                ],
                'quick_fixes' => [
                    'Check file path spelling',
                    'Verify file exists in resources/views/',
                    'Check file permissions (chmod 755)',
                    'Create missing directories'
                ]
            ];
        }

        // Undefined variable
        if (stripos($message, 'Undefined variable') !== false) {
            preg_match('/Undefined variable[:\s]+\$?(\w+)/', $message, $matches);
            $varName = $matches[1] ?? 'unknown';
            
            return [
                'category' => 'Variable',
                'icon' => '❓',
                'title' => 'Undefined Variable',
                'problem' => "Variable \${$varName} is used before being defined.",
                'likely_causes' => [
                    "Forgot to pass \${$varName} to view",
                    "Variable not initialized in controller",
                    "Typo in variable name",
                    "Conditional logic skipped initialization"
                ],
                'quick_fixes' => [
                    "Pass variable in view(): view('name', ['{$varName}' => \$value])",
                    "Initialize in controller before use",
                    "Check variable name spelling",
                    "Use isset() or null coalescing (??)"
                ]
            ];
        }

        // Undefined method
        if (stripos($message, 'Call to undefined method') !== false) {
            preg_match('/Call to undefined method (.+?)::(\w+)/', $message, $matches);
            $class = $matches[1] ?? 'Unknown';
            $method = $matches[2] ?? 'unknown';
            
            return [
                'category' => 'Method',
                'icon' => '⚙️',
                'title' => 'Method Not Found',
                'problem' => "Method {$method}() doesn't exist in class {$class}.",
                'likely_causes' => [
                    "Typo in method name",
                    "Method doesn't exist in this class",
                    "Using wrong object/class",
                    "Missing trait or inheritance"
                ],
                'quick_fixes' => [
                    "Check method spelling: {$method}()",
                    "Verify class has this method",
                    "Check class documentation",
                    "Add missing method to class"
                ]
            ];
        }

        // Syntax errors
        if (stripos($message, 'syntax error') !== false || $errorType === 'ParseError') {
            return [
                'category' => 'Syntax',
                'icon' => '⚠️',
                'title' => 'Syntax Error',
                'problem' => 'Invalid PHP syntax in your code.',
                'likely_causes' => [
                    'Missing semicolon ;',
                    'Unmatched brackets/parentheses',
                    'Missing closing quote',
                    'Invalid PHP syntax',
                    'Wrong function usage'
                ],
                'quick_fixes' => [
                    'Check for missing semicolons',
                    'Count opening/closing brackets',
                    'Check string quotes matching',
                    'Review PHP syntax documentation'
                ]
            ];
        }

        // Authentication errors
        if (stripos($file, 'Auth') !== false || stripos($message, 'Unauthenticated') !== false) {
            return [
                'category' => 'Authentication',
                'icon' => '🔐',
                'title' => 'Authentication Issue',
                'problem' => 'User authentication is required or failed.',
                'likely_causes' => [
                    'User not logged in',
                    'Session expired',
                    'Missing AuthMiddleware on route',
                    'Invalid credentials'
                ],
                'quick_fixes' => [
                    'Add AuthMiddleware to route',
                    'Check user is logged in: Auth::check()',
                    'Redirect to login page',
                    'Check session configuration'
                ]
            ];
        }

        // Memory errors
        if (stripos($message, 'memory') !== false) {
            return [
                'category' => 'Performance',
                'icon' => '💾',
                'title' => 'Memory Limit Exceeded',
                'problem' => 'Script is using too much memory.',
                'likely_causes' => [
                    'Loading too much data at once',
                    'Infinite loop',
                    'Large file processing',
                    'Memory leak in code'
                ],
                'quick_fixes' => [
                    'Use pagination for large datasets',
                    'Process data in chunks',
                    'Increase memory_limit in php.ini',
                    'Use generators for large iterations'
                ]
            ];
        }

        // Default generic error
        return [
            'category' => 'General',
            'icon' => '🔍',
            'title' => 'Application Error',
            'problem' => 'An unexpected error occurred.',
            'likely_causes' => [
                'Logic error in your code',
                'Missing dependency',
                'Configuration issue',
                'Unexpected input data'
            ],
            'quick_fixes' => [
                'Check error message and stack trace',
                'Review recent code changes',
                'Check application logs',
                'Enable debug mode for more details'
            ]
        ];
    }

    /**
     * Get suggested solution based on error context
     */
    protected static function getSolution(Throwable $exception): array
    {
        $message = strtolower($exception->getMessage());
        $solutions = [];

        // Add specific solutions based on error patterns
        if (strpos($message, 'pdo') !== false || strpos($message, 'database') !== false) {
            $solutions[] = [
                'title' => 'Fix Database Connection',
                'code' => "// Check your .env file:\nDB_HOST=localhost\nDB_DATABASE=your_database\nDB_USERNAME=your_username\nDB_PASSWORD=your_password\n\n// Run migrations:\nswiftphp migrate"
            ];
        }

        if (strpos($message, 'class') !== false && strpos($message, 'not found') !== false) {
            $solutions[] = [
                'title' => 'Fix Class Loading',
                'code' => "// Run composer autoload:\ncomposer dump-autoload\n\n// Check namespace:\nnamespace App\\Controllers;\n\nuse SwiftPHP\\Core\\Controller;"
            ];
        }

        if (strpos($message, 'undefined variable') !== false) {
            $solutions[] = [
                'title' => 'Pass Variable to View',
                'code' => "// In Controller:\npublic function index() {\n    \$users = User::all();\n    return view('users.index', compact('users'));\n}"
            ];
        }

        return $solutions;
    }

    /**
     * Get code snippet around error line
     */
    protected static function getCodeSnippet(string $file, int $errorLine, int $context = 10): array
    {
        if (!file_exists($file)) {
            return [];
        }

        $lines = file($file);
        $start = max(0, $errorLine - $context - 1);
        $end = min(count($lines), $errorLine + $context);

        $snippet = [];
        for ($i = $start; $i < $end; $i++) {
            $snippet[] = [
                'line' => $i + 1,
                'code' => rtrim($lines[$i]),
                'highlight' => ($i + 1) === $errorLine
            ];
        }

        return $snippet;
    }

    /**
     * Check if debug mode is enabled
     */
    protected static function isDebugMode(): bool
    {
        return (getenv('APP_DEBUG') === 'true') || (getenv('APP_ENV') === 'development');
    }

    /**
     * Get error log
     */
    public static function getErrorLog(): array
    {
        return self::$errorLog;
    }
}
