<?php

declare(strict_types=1);

namespace SwiftPHP\Console\Commands;

class MakeModelCommand
{
    public function execute(array $args): void
    {
        if (empty($args[0])) {
            echo "\033[31mError: Model name is required\033[0m\n";
            echo "Usage: swiftphp make:model <ModelName>\n";
            return;
        }

        $modelName = ucfirst($args[0]);
        $modelPath = "app/Models/$modelName.php";

        if (file_exists($modelPath)) {
            echo "\033[31mError: Model '$modelName' already exists\033[0m\n";
            return;
        }

        $template = $this->getModelTemplate($modelName);

        if (!is_dir('app/Models')) {
            mkdir('app/Models', 0755, true);
        }

        file_put_contents($modelPath, $template);

        echo "\033[32mModel '$modelName' created successfully!\033[0m\n";
        echo "Location: $modelPath\n";
    }

    private function getModelTemplate(string $name): string
    {
        $tableName = strtolower($name) . 's';

        return "<?php

namespace App\\Models;

use SwiftPHP\\Core\\Model;

class $name extends Model
{
    protected string \$table = '$tableName';
    protected array \$fillable = [];
    
    // Add your model methods here
}
";
    }
}
