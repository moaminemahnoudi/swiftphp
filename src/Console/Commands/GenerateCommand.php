<?php

namespace SwiftPHP\Console\Commands;

use SwiftPHP\Console\Command;

class GenerateCommand extends Command
{
    protected string $signature = 'generate';
    protected string $description = 'Generate code using AI from natural language description';

    public function handle(): int
    {
        $this->info("🤖 SwiftPHP AI Code Generator");
        $this->line("");
        
        $description = $this->ask("What would you like to generate? (describe in plain English)");
        
        if (empty($description)) {
            $this->error("❌ Description cannot be empty");
            return 1;
        }

        $this->line("");
        $this->info("🧠 Analyzing your request...");
        
        // Analyze what to generate
        $analysis = $this->analyzeRequest($description);
        
        $this->line("");
        $this->success("✨ I understood:");
        foreach ($analysis['components'] as $component) {
            $this->line("  → {$component['type']}: {$component['name']}");
        }
        
        $this->line("");
        $confirm = $this->confirm("Generate these files?");
        
        if (!$confirm) {
            $this->warning("⚠️  Generation cancelled");
            return 0;
        }
        
        $this->line("");
        $this->info("🚀 Generating code...");
        
        $generated = [];
        foreach ($analysis['components'] as $component) {
            $file = $this->generateComponent($component, $analysis);
            if ($file) {
                $generated[] = $file;
                $this->success("  ✓ Created: {$file}");
            }
        }
        
        $this->line("");
        $this->success("🎉 Generated {count} file(s) successfully!", ['count' => count($generated)]);
        
        // Show next steps
        if (!empty($analysis['next_steps'])) {
            $this->line("");
            $this->info("📝 Next steps:");
            foreach ($analysis['next_steps'] as $step) {
                $this->line("  {$step}");
            }
        }
        
        return 0;
    }

    protected function analyzeRequest(string $description): array
    {
        $description = strtolower($description);
        $components = [];
        $context = [
            'has_auth' => false,
            'has_crud' => false,
            'has_api' => false,
            'has_validation' => false,
            'relationships' => []
        ];
        
        // Detect entity/resource name
        $entityName = $this->extractEntityName($description);
        
        // Detect what to generate
        $patterns = [
            'controller' => ['/controller/', '/crud/', '/resource/', '/endpoints?/', '/routes?/'],
            'model' => ['/model/', '/entity/', '/database/', '/table/'],
            'migration' => ['/migration/', '/table/', '/database/', '/schema/'],
            'test' => ['/test/', '/testing/', '/spec/'],
            'view' => ['/view/', '/page/', '/form/', '/ui/'],
        ];
        
        // Check patterns
        $shouldGenerate = [];
        foreach ($patterns as $type => $typePatterns) {
            foreach ($typePatterns as $pattern) {
                if (preg_match($pattern, $description)) {
                    $shouldGenerate[$type] = true;
                    break;
                }
            }
        }
        
        // If CRUD mentioned, generate all
        if (preg_match('/crud|resource|scaffold/', $description)) {
            $shouldGenerate = array_fill_keys(['controller', 'model', 'migration', 'view'], true);
            $context['has_crud'] = true;
        }
        
        // Default to controller + model if nothing specific
        if (empty($shouldGenerate)) {
            $shouldGenerate = ['controller' => true, 'model' => true];
        }
        
        // Detect features
        if (preg_match('/auth|login|register|protected/', $description)) {
            $context['has_auth'] = true;
        }
        
        if (preg_match('/api|json|rest/', $description)) {
            $context['has_api'] = true;
        }
        
        if (preg_match('/validat/', $description)) {
            $context['has_validation'] = true;
        }
        
        // Detect relationships
        if (preg_match('/belongs? to|has many|has one/i', $description, $matches)) {
            $context['relationships'][] = $matches[0];
        }
        
        // Extract fields
        $fields = $this->extractFields($description);
        
        // Build components list
        if (isset($shouldGenerate['model'])) {
            $components[] = [
                'type' => 'Model',
                'name' => $entityName,
                'fields' => $fields,
                'relationships' => $context['relationships']
            ];
        }
        
        if (isset($shouldGenerate['migration'])) {
            $components[] = [
                'type' => 'Migration',
                'name' => "create_{$this->pluralize(strtolower($entityName))}_table",
                'fields' => $fields
            ];
        }
        
        if (isset($shouldGenerate['controller'])) {
            $components[] = [
                'type' => 'Controller',
                'name' => "{$entityName}Controller",
                'is_api' => $context['has_api'],
                'has_crud' => $context['has_crud'],
                'has_auth' => $context['has_auth'],
                'has_validation' => $context['has_validation'],
                'fields' => $fields
            ];
        }
        
        if (isset($shouldGenerate['view']) && !$context['has_api']) {
            $components[] = [
                'type' => 'Views',
                'name' => strtolower($entityName),
                'fields' => $fields
            ];
        }
        
        if (isset($shouldGenerate['test'])) {
            $components[] = [
                'type' => 'Test',
                'name' => "{$entityName}Test",
                'fields' => $fields
            ];
        }
        
        // Next steps
        $next_steps = [];
        if (isset($shouldGenerate['migration'])) {
            $next_steps[] = "1. Run: swiftphp migrate";
        }
        if (isset($shouldGenerate['controller'])) {
            $routeName = strtolower($this->pluralize($entityName));
            $next_steps[] = "2. Add route: Route::resource('/{$routeName}', {$entityName}Controller::class);";
        }
        if (isset($shouldGenerate['test'])) {
            $next_steps[] = "3. Run tests: vendor/bin/phpunit";
        }
        
        return [
            'entity' => $entityName,
            'components' => $components,
            'context' => $context,
            'next_steps' => $next_steps
        ];
    }

    protected function extractEntityName(string $description): string
    {
        // Try to find capitalized words (likely entity names)
        if (preg_match('/\b([A-Z][a-z]+(?:[A-Z][a-z]+)*)\b/', $description, $matches)) {
            return $matches[1];
        }
        
        // Common patterns
        $patterns = [
            '/(?:for|a|an|the) (\w+) (?:controller|model|resource)/' => 1,
            '/(\w+) (?:crud|resource|management)/' => 1,
            '/manage (\w+)s?/' => 1,
        ];
        
        foreach ($patterns as $pattern => $group) {
            if (preg_match($pattern, $description, $matches)) {
                return ucfirst($matches[$group]);
            }
        }
        
        // Ask user
        return ucfirst($this->ask("What is the main entity/resource name? (e.g., Post, User, Product)"));
    }

    protected function extractFields(string $description): array
    {
        $fields = [];
        
        // Common field patterns
        $fieldPatterns = [
            '/with (?:fields? )?([^.]+)/' => 1,
            '/has ([^.]+) fields?/' => 1,
            '/columns?: ([^.]+)/' => 1,
        ];
        
        foreach ($fieldPatterns as $pattern => $group) {
            if (preg_match($pattern, $description, $matches)) {
                $fieldStr = $matches[$group];
                // Split by comma, and, &
                $fieldNames = preg_split('/[,&]| and /', $fieldStr);
                
                foreach ($fieldNames as $fieldName) {
                    $fieldName = trim($fieldName);
                    if (empty($fieldName)) continue;
                    
                    // Detect type from name
                    $type = $this->guessFieldType($fieldName);
                    $fields[] = [
                        'name' => $this->cleanFieldName($fieldName),
                        'type' => $type
                    ];
                }
                break;
            }
        }
        
        // If no fields found, ask
        if (empty($fields)) {
            $this->line("");
            $fieldsInput = $this->ask("What fields should it have? (comma-separated, e.g., title, content, status)");
            if (!empty($fieldsInput)) {
                $fieldNames = explode(',', $fieldsInput);
                foreach ($fieldNames as $fieldName) {
                    $fieldName = trim($fieldName);
                    if (empty($fieldName)) continue;
                    
                    $type = $this->guessFieldType($fieldName);
                    $fields[] = [
                        'name' => $fieldName,
                        'type' => $type
                    ];
                }
            }
        }
        
        return $fields;
    }

    protected function guessFieldType(string $fieldName): string
    {
        $fieldName = strtolower($fieldName);
        
        $typeMap = [
            'id' => 'integer',
            'name' => 'string',
            'title' => 'string',
            'slug' => 'string',
            'email' => 'string',
            'password' => 'string',
            'phone' => 'string',
            'address' => 'string',
            'city' => 'string',
            'country' => 'string',
            'description' => 'text',
            'content' => 'text',
            'body' => 'text',
            'bio' => 'text',
            'status' => 'string',
            'type' => 'string',
            'price' => 'decimal',
            'amount' => 'decimal',
            'quantity' => 'integer',
            'count' => 'integer',
            'age' => 'integer',
            'is_' => 'boolean',
            'has_' => 'boolean',
            'active' => 'boolean',
            'published' => 'boolean',
            'verified' => 'boolean',
            '_at' => 'timestamp',
            'date' => 'date',
            'time' => 'time',
            'image' => 'string',
            'avatar' => 'string',
            'file' => 'string',
        ];
        
        foreach ($typeMap as $key => $type) {
            if (str_contains($fieldName, $key)) {
                return $type;
            }
        }
        
        return 'string'; // Default
    }

    protected function cleanFieldName(string $name): string
    {
        $name = strtolower(trim($name));
        $name = preg_replace('/[^a-z0-9_]/', '_', $name);
        return $name;
    }

    protected function generateComponent(array $component, array $analysis): ?string
    {
        return match($component['type']) {
            'Model' => $this->generateModel($component, $analysis),
            'Controller' => $this->generateController($component, $analysis),
            'Migration' => $this->generateMigration($component, $analysis),
            'Views' => $this->generateViews($component, $analysis),
            'Test' => $this->generateTest($component, $analysis),
            default => null
        };
    }

    protected function generateModel(array $component, array $analysis): string
    {
        $name = $component['name'];
        $fields = $component['fields'] ?? [];
        $relationships = $component['relationships'] ?? [];
        
        $fillable = array_column($fields, 'name');
        $fillableStr = "'" . implode("', '", $fillable) . "'";
        
        $relationMethods = '';
        if (!empty($relationships)) {
            $relationMethods = "\n\n    // Relationships\n";
            foreach ($relationships as $rel) {
                if (preg_match('/belongs? to (\w+)/i', $rel, $m)) {
                    $relName = strtolower($m[1]);
                    $relClass = ucfirst($relName);
                    $relationMethods .= "    public function {$relName}()\n";
                    $relationMethods .= "    {\n";
                    $relationMethods .= "        return \$this->belongsTo({$relClass}::class);\n";
                    $relationMethods .= "    }\n\n";
                }
            }
        }
        
        $code = "<?php

namespace App\Models;

use SwiftPHP\Core\Model;

class {$name} extends Model
{
    protected \$table = '" . strtolower($this->pluralize($name)) . "';
    
    protected \$fillable = [{$fillableStr}];
    
    protected \$casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];{$relationMethods}
}
";
        
        $path = __DIR__ . "/../../../app/Models/{$name}.php";
        file_put_contents($path, $code);
        
        return "app/Models/{$name}.php";
    }

    protected function generateController(array $component, array $analysis): string
    {
        $name = $component['name'];
        $entity = str_replace('Controller', '', $name);
        $entityLower = strtolower($entity);
        $entityPlural = $this->pluralize($entityLower);
        $fields = $component['fields'] ?? [];
        $isApi = $component['is_api'] ?? false;
        $hasCrud = $component['has_crud'] ?? false;
        $hasAuth = $component['has_auth'] ?? false;
        $hasValidation = $component['has_validation'] ?? false;
        
        $authMiddleware = $hasAuth ? "\n    protected array \$middleware = ['auth'];\n" : "";
        
        $validationRules = '';
        if ($hasValidation && !empty($fields)) {
            $rules = [];
            foreach ($fields as $field) {
                $rule = match($field['type']) {
                    'string' => 'required|string|max:255',
                    'text' => 'required|string',
                    'integer' => 'required|integer',
                    'decimal' => 'required|numeric',
                    'boolean' => 'required|boolean',
                    'email' => 'required|email',
                    default => 'required'
                };
                $rules[] = "            '{$field['name']}' => '{$rule}'";
            }
            $validationRules = "\n        \$validated = \$request->validate([\n" . implode(",\n", $rules) . "\n        ]);\n";
        }
        
        $returnType = $isApi ? 'json' : 'view';
        
        $methods = '';
        if ($hasCrud) {
            $methods = "
    public function index()
    {
        \${$entityPlural} = {$entity}::all();
        " . ($isApi 
            ? "return json(\${$entityPlural});" 
            : "return view('{$entityPlural}.index', compact('{$entityPlural}'));") . "
    }

    public function create()
    {
        " . ($isApi 
            ? "return json(['message' => 'Use POST to create']);" 
            : "return view('{$entityPlural}.create');") . "
    }

    public function store(\$request)
    {{$validationRules}
        \${$entityLower} = {$entity}::create(" . ($hasValidation ? "\$validated" : "\$request->all()") . ");
        " . ($isApi 
            ? "return json(\${$entityLower}, 201);" 
            : "return redirect('/{$entityPlural}')->with('success', '{$entity} created successfully');") . "
    }

    public function show(\$id)
    {
        \${$entityLower} = {$entity}::find(\$id);
        if (!\${$entityLower}) {
            " . ($isApi 
                ? "return json(['error' => 'Not found'], 404);" 
                : "abort(404);") . "
        }
        " . ($isApi 
            ? "return json(\${$entityLower});" 
            : "return view('{$entityPlural}.show', compact('{$entityLower}'));") . "
    }

    public function edit(\$id)
    {
        \${$entityLower} = {$entity}::find(\$id);
        if (!\${$entityLower}) {
            abort(404);
        }
        return view('{$entityPlural}.edit', compact('{$entityLower}'));
    }

    public function update(\$id, \$request)
    {{$validationRules}
        \${$entityLower} = {$entity}::find(\$id);
        if (!\${$entityLower}) {
            " . ($isApi 
                ? "return json(['error' => 'Not found'], 404);" 
                : "abort(404);") . "
        }
        \${$entityLower}->update(" . ($hasValidation ? "\$validated" : "\$request->all()") . ");
        " . ($isApi 
            ? "return json(\${$entityLower});" 
            : "return redirect('/{$entityPlural}')->with('success', '{$entity} updated successfully');") . "
    }

    public function destroy(\$id)
    {
        \${$entityLower} = {$entity}::find(\$id);
        if (!\${$entityLower}) {
            " . ($isApi 
                ? "return json(['error' => 'Not found'], 404);" 
                : "abort(404);") . "
        }
        \${$entityLower}->delete();
        " . ($isApi 
            ? "return json(['message' => 'Deleted successfully']);" 
            : "return redirect('/{$entityPlural}')->with('success', '{$entity} deleted successfully');") . "
    }";
        }
        
        $code = "<?php

namespace App\Controllers;

use SwiftPHP\Core\Controller;
use App\Models\\{$entity};

class {$name} extends Controller
{{$authMiddleware}{$methods}
}
";
        
        $path = __DIR__ . "/../../../app/Controllers/{$name}.php";
        file_put_contents($path, $code);
        
        return "app/Controllers/{$name}.php";
    }

    protected function generateMigration(array $component, array $analysis): string
    {
        $name = $component['name'];
        $fields = $component['fields'] ?? [];
        
        $timestamp = date('Y_m_d_His');
        $className = 'Create' . str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
        
        $columns = '';
        foreach ($fields as $field) {
            $type = match($field['type']) {
                'integer' => 'integer',
                'string' => 'string',
                'text' => 'text',
                'boolean' => 'boolean',
                'decimal' => 'decimal',
                'timestamp' => 'timestamp',
                'date' => 'date',
                'time' => 'time',
                default => 'string'
            };
            
            $columns .= "            \$table->{$type}('{$field['name']}');\n";
        }
        
        $code = "<?php

use SwiftPHP\Database\Database;

return new class {
    public function up()
    {
        \$db = Database::getInstance();
        \$db->execute(\"
            CREATE TABLE IF NOT EXISTS {$name} (
                id INT AUTO_INCREMENT PRIMARY KEY,
{$columns}                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        \");
    }
    
    public function down()
    {
        \$db = Database::getInstance();
        \$db->execute(\"DROP TABLE IF EXISTS {$name}\");
    }
};
";
        
        $filename = "{$timestamp}_{$name}.php";
        $path = __DIR__ . "/../../../database/migrations/{$filename}";
        file_put_contents($path, $code);
        
        return "database/migrations/{$filename}";
    }

    protected function generateViews(array $component, array $analysis): string
    {
        $name = $component['name'];
        $fields = $component['fields'] ?? [];
        
        $dir = __DIR__ . "/../../../resources/views/{$name}";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
        // Index view
        $indexView = "@extends('layouts.app')

@section('content')
<h1>" . ucfirst($this->pluralize($name)) . "</h1>

<a href=\"/{$name}/create\" class=\"btn btn-primary\">Create New</a>

<table>
    <thead>
        <tr>
";
        foreach ($fields as $field) {
            $indexView .= "            <th>" . ucfirst($field['name']) . "</th>\n";
        }
        $indexView .= "            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach(\${$this->pluralize($name)} as \${$name})
        <tr>
";
        foreach ($fields as $field) {
            $indexView .= "            <td>{{ \${$name}->{$field['name']} }}</td>\n";
        }
        $indexView .= "            <td>
                <a href=\"/{$name}/{{ \${$name}->id }}\">View</a>
                <a href=\"/{$name}/{{ \${$name}->id }}/edit\">Edit</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
";
        file_put_contents("{$dir}/index.swift.php", $indexView);
        
        // Create view
        $createView = "@extends('layouts.app')

@section('content')
<h1>Create " . ucfirst($name) . "</h1>

<form action=\"/{$name}\" method=\"POST\">
    @csrf
";
        foreach ($fields as $field) {
            $createView .= "    <div>
        <label>" . ucfirst($field['name']) . "</label>
        <input type=\"text\" name=\"{$field['name']}\" required>
    </div>
";
        }
        $createView .= "    <button type=\"submit\">Create</button>
</form>
@endsection
";
        file_put_contents("{$dir}/create.swift.php", $createView);
        
        return "resources/views/{$name}/*.swift.php";
    }

    protected function generateTest(array $component, array $analysis): string
    {
        $name = $component['name'];
        $entity = str_replace('Test', '', $name);
        $entityLower = strtolower($entity);
        
        $code = "<?php

use PHPUnit\Framework\TestCase;
use App\Models\\{$entity};

class {$name} extends TestCase
{
    public function testCanCreate{$entity}()
    {
        \${$entityLower} = {$entity}::create([
            'name' => 'Test {$entity}',
        ]);
        
        \$this->assertNotNull(\${$entityLower}->id);
        \$this->assertEquals('Test {$entity}', \${$entityLower}->name);
    }
    
    public function testCanUpdate{$entity}()
    {
        \${$entityLower} = {$entity}::create(['name' => 'Original']);
        \${$entityLower}->update(['name' => 'Updated']);
        
        \$this->assertEquals('Updated', \${$entityLower}->name);
    }
    
    public function testCanDelete{$entity}()
    {
        \${$entityLower} = {$entity}::create(['name' => 'To Delete']);
        \$id = \${$entityLower}->id;
        
        \${$entityLower}->delete();
        
        \$this->assertNull({$entity}::find(\$id));
    }
}
";
        
        $dir = __DIR__ . "/../../../tests";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
        $path = "{$dir}/{$name}.php";
        file_put_contents($path, $code);
        
        return "tests/{$name}.php";
    }

    protected function pluralize(string $word): string
    {
        $irregular = [
            'person' => 'people',
            'child' => 'children',
            'man' => 'men',
            'woman' => 'women',
        ];
        
        if (isset($irregular[$word])) {
            return $irregular[$word];
        }
        
        if (str_ends_with($word, 'y')) {
            return substr($word, 0, -1) . 'ies';
        }
        
        if (str_ends_with($word, 's') || str_ends_with($word, 'x') || str_ends_with($word, 'z')) {
            return $word . 'es';
        }
        
        return $word . 's';
    }
}
