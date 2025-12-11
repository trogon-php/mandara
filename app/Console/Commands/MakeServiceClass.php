<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeServiceClass extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service-class {name}';

    protected $description = 'create a service class for a given name';

    public function handle()
    {
        $name = Str::studly($this->argument('name')); // LiveClassAccount
        $plural = Str::pluralStudly($name);
        
        $this->createFile(app_path("Services/{$plural}/{$name}Service.php"), $this->getServiceStub($name));
        $this->info("Service created: Services/{$plural}/{$name}Service.php");
    }

    protected function createFile($path, $content)
    {
        if (!File::exists(dirname($path))) {
            File::makeDirectory(dirname($path), 0755, true);
        }
        File::put($path, $content);
    }

    protected function getServiceStub($name)
    {
        $plural = Str::pluralStudly($name);
    
        return <<<PHP
    <?php
    
    namespace App\Services\\{$plural};
    
    use App\Models\\{$name};
    use App\Services\Core\BaseService;
    
    class {$name}Service extends BaseService
    {
        protected string \$modelClass = {$name}::class;
    
        public function __construct()
        {
            parent::__construct();
        }
    
        /**
         * Get filter configuration - used for CRUD filters
         */
        public function getFilterConfig(): array
        {
            return [
                'status' => [
                    'type' => 'select',
                    'label' => 'Status',
                    'col' => 3,
                    'options' => [
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ],
                ],
            ];
        }
    
        /**
         * Get search fields configuration for UI
         */
        public function getSearchFieldsConfig(): array
        {
            return [
                'title' => 'Title',
                'provider' => 'Provider',
            ];
        }
    
        /**
         * Get default search fields
         */
        public function getDefaultSearchFields(): array
        {
            return ['title', 'provider'];
        }
    
        /**
         * Get default sorting
         */
        public function getDefaultSorting(): array
        {
            return ['field' => 'sort_order', 'direction' => 'asc'];
        }
    
        public function store(array \$data): {$name}
        {
            \$maxSortOrder = \$this->model->max('sort_order') ?? 0;
            \$data['sort_order'] = \$maxSortOrder + 1;
    
            return parent::store(\$data);
        }
    }
    PHP;
    }
}



