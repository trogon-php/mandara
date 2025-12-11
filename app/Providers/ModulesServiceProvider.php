<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use ReflectionClass;

class ModulesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $modulesPath = base_path('app/Modules');
        $filesystem  = new Filesystem;

        if (! $filesystem->exists($modulesPath)) {
            return;
        }

        foreach ($filesystem->directories($modulesPath) as $module) {
            $providerPath = $module . '/Providers';
            if (! $filesystem->exists($providerPath)) {
                continue;
            }

            foreach ($filesystem->files($providerPath) as $file) {
                $class = $this->getClassFromFile($file->getPathname(), $module);
                if ($class && class_exists($class)) {
                    // Check if the class is abstract before trying to register it
                    try {
                        $reflection = new ReflectionClass($class);
                        if (!$reflection->isAbstract() && $reflection->isSubclassOf(ServiceProvider::class)) {
                            echo $class . PHP_EOL;
                            $this->app->register($class);
                        }
                    } catch (\ReflectionException $e) {
                        // Skip classes that can't be reflected
                        continue;
                    }
                }
            }
        }
    }

    /**
     * Convert file path to FQCN
     */
    protected function getClassFromFile(string $filePath, string $modulePath): ?string
    {
        $moduleName = basename($modulePath); // e.g. "Homeworks"
        $fileName   = pathinfo($filePath, PATHINFO_FILENAME); // e.g. "HomeworkServiceProvider"

        return "App\\Modules\\{$moduleName}\\Providers\\{$fileName}";
    }
}
