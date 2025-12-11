<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        // All repository bindings have been removed!
        // Services now work directly with models using Controller → Service → Model pattern
    }
}
