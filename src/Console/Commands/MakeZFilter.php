<?php

namespace Kayckmatias\ZFilters\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeZFilter extends Command
{
    protected $signature = 'make:zfilter {className}';
    protected $description = 'Create a new filter';

    /**
     * Filesystem instance
     * @var Filesystem
     */
    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function handle()
    {
        $className = $this->argument('className');

        $path = $this->getPath($className);

        $this->makeDirectory(dirname($path));

        $contents = $this->getSourceFile($className);

        $relativePath = $this->getRelativePath($path);

        if (!$this->files->exists($path)) {
            $this->files->put($path, $contents);
            $this->info("[INFO] Filter '$className' created in: {$relativePath}");
        } else {
            $this->warn("[WARN] File: {$relativePath} already Exists, filter not created.");
        }
    }


    protected function getRelativePath($path){
        return substr($path, strpos($path, 'app'));
    }

    /**
     * Get the source file for a given class name.
     *
     * @param string $className The name of the class.
     * @return string The contents of the source file.
     */
    public function getSourceFile(string $className): string
    {
        $stubPath = $this->getStubPath();
        $stubVariables = $this->getStubVariables($className);
        return $this->getStubContents($stubPath, $stubVariables);
    }

    /**
     * Get the path of the filter class.
     *
     * @param string $className The name of the filter class.
     * @return string The path of the filter class.
     */
    protected function getPath(string $className): string
    {
        return app_path("Filters/{$className}.php");
    }

    /**
     * Get the stub variables for a given name.
     *
     * @param string $className The name of the class.
     * @return array The array of stub variables.
     */
    public function getStubVariables(string $className): array
    {
        return [
            'CLASS_NAME' => $className,
        ];
    }

    /**
     * Get the contents of a stub file.
     *
     * @param string $stub The path to the stub file.
     * @param array $stubVariables An array of key-value pairs to replace in the stub file.
     * @return string The contents of the stub file with the variables replaced.
     */
    public function getStubContents(string $stub, array $stubVariables = []): string
    {
        $contents = file_get_contents($stub);

        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('{{' . $search . '}}', $replace, $contents);
        }

        return $contents;
    }

    /**
     * Get the path of the stub file.
     *
     * @return string The path of the stub file.
     */
    protected function getStubPath(): string
    {
        return __DIR__ . '/stubs/zfilter.stub';
    }

    /**
     * Creates a directory if it does not exist.
     *
     * @param string $path The path to the directory.
     * @return bool True if the directory exists or was successfully created, false otherwise.
     */
    protected function makeDirectory(string $path): bool
    {
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return true;
    }
}
