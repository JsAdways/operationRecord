<?php
namespace Jsadways\Operationrecord\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command as CommandAlias;

class MakeRecordModelCommand extends Command
{
    protected $signature = 'make:operation-record
                              {name? : The name of the model class}
                              {--table= : The table name}
                              {--namespace= : The namespace for the model}
                              {--force : Overwrite existing file}';
    protected $description = 'Create a new operation record model class';

    public function handle(): int
    {
        // 取得參數
        $name = $this->argument('name') ?? $this->ask('What is the model name?', 'Record');
        $table = $this->option('table') ?? $this->ask('What is the table name?', 'record');
        $namespace = $this->option('namespace') ?? 'App\\Models';

        // 生成檔案路徑
        $path = $this->_getPath($name, $namespace);

        // 檢查檔案是否已存在
        if (!$this->option('force') && file_exists($path)) {
            $this->error("Model [{$name}] already exists!");

            if (!$this->confirm('Do you want to overwrite it?')) {
                return Command::FAILURE;
            }
        }

        // 生成檔案內容
        $content = $this->_buildClass($name, $namespace, $table);

        // 建立目錄（如果不存在）
        $this->_makeDirectory($path);

        // 寫入檔案
        file_put_contents($path, $content);

        $this->info("Model [{$name}] created successfully.");
        $this->info("Location: {$path}");
        $this->newLine();
        $this->comment("Next steps:");
        $this->comment("1. Update your Controller to use RecordsOperation trait");
        $this->comment("2. Implement getRecordModel() to return {$namespace}\\{$name}::class");

        return CommandAlias::SUCCESS;
    }

    protected function _getPath(string $name, string $namespace): string
    {
        $name = Str::studly($name);

        // 將 namespace 轉換為路徑
        $namespacePath = str_replace('\\', '/', $namespace);
        $namespacePath = str_replace('App/', 'app/', $namespacePath);

        return base_path("{$namespacePath}/{$name}.php");
    }

    protected function _makeDirectory(string $path): void
    {
        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    protected function _buildClass(string $name, string $namespace, string $table): string
    {
        $stub = $this->_getStub();

        return str_replace(
            ['{{namespace}}', '{{class}}', '{{table}}'],
            [$namespace, Str::studly($name), $table],
            $stub
        );
    }

    protected function _getStub(): string
    {
        $stubPath = __DIR__ . '/../../stubs/record-model.stub';
        return file_get_contents($stubPath);
    }
}
