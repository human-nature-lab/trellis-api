<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Library\RestValidator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Way\Generators\GeneratorsServiceProvider::class);
            $this->app->register(\Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider::class);
            $this->app->register('Mojopollo\Schema\MakeMigrationJsonServiceProvider');
            $this->app->register('Laracasts\Generators\GeneratorsServiceProvider');
        }
    }

    public function boot()
    {
        \Validator::resolver(function ($translator, $data, $rules, $messages) {
            return new RestValidator($translator, $data, $rules, $messages);
        });
    }
}
