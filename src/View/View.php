<?php

declare(strict_types=1);

namespace SwiftPHP\View;

class View
{
    private static string $viewPath = '';
    private static string $cachePath = '';

    public static function init(): void
    {
        self::$viewPath = __DIR__ . '/../../resources/views';
        self::$cachePath = __DIR__ . '/../../storage/views';
    }

    public static function render(string $view, array $data = []): string
    {
        if (empty(self::$viewPath)) {
            self::init();
        }

        $viewFile = self::$viewPath . '/' . str_replace('.', '/', $view) . '.swift.php';
        $cacheFile = self::$cachePath . '/' . md5($view) . '.php';

        if (!file_exists($viewFile)) {
            throw new \Exception("View not found: $view");
        }

        if (!file_exists($cacheFile) || filemtime($viewFile) > filemtime($cacheFile)) {
            $compiled = self::compile(file_get_contents($viewFile));
            file_put_contents($cacheFile, $compiled);
        }

        extract($data);
        ob_start();
        include $cacheFile;
        return ob_get_clean();
    }

    private static function compile(string $content): string
    {
        // Extends
        $content = preg_replace('/@extends\([\'"](.+?)[\'"]\)/', '<?php $__layout = "$1"; ?>', $content);

        // Sections
        $content = preg_replace('/@section\([\'"](.+?)[\'"]\)/', '<?php ob_start(); $__section = "$1"; ?>', $content);
        $content = preg_replace('/@endsection/', '<?php $__sections[$__section] = ob_get_clean(); ?>', $content);

        // Yields
        $content = preg_replace('/@yield\([\'"](.+?)[\'"]\)/', '<?php echo $__sections["$1"] ?? ""; ?>', $content);

        // Echo statements
        $content = preg_replace('/\{\{\s*(.+?)\s*\}\}/', '<?php echo htmlspecialchars($1 ?? "", ENT_QUOTES, "UTF-8"); ?>', $content);
        $content = preg_replace('/\{!!\s*(.+?)\s*!!\}/', '<?php echo $1 ?? ""; ?>', $content);

        // Conditionals
        $content = preg_replace('/@if\s*\((.+?)\)/', '<?php if($1): ?>', $content);
        $content = preg_replace('/@elseif\s*\((.+?)\)/', '<?php elseif($1): ?>', $content);
        $content = preg_replace('/@else/', '<?php else: ?>', $content);
        $content = preg_replace('/@endif/', '<?php endif; ?>', $content);

        // Loops
        $content = preg_replace('/@foreach\s*\((.+?)\)/', '<?php foreach($1): ?>', $content);
        $content = preg_replace('/@endforeach/', '<?php endforeach; ?>', $content);
        $content = preg_replace('/@for\s*\((.+?)\)/', '<?php for($1): ?>', $content);
        $content = preg_replace('/@endfor/', '<?php endfor; ?>', $content);

        // CSRF
        $content = preg_replace('/@csrf/', '<?php echo \'<input type="hidden" name="_token" value="\' . \SwiftPHP\Security\Security::generateCsrfToken() . \'">\'; ?>', $content);

        // Include
        $content = preg_replace('/@include\([\'"](.+?)[\'"]\)/', '<?php echo \SwiftPHP\View\View::render("$1", get_defined_vars()); ?>', $content);

        // Handle layout
        if (strpos($content, '$__layout') !== false) {
            $content .= '<?php if(isset($__layout)) echo \SwiftPHP\View\View::render($__layout, array_merge(get_defined_vars(), ["__sections" => $__sections ?? []])); ?>';
        }

        return $content;
    }
}
