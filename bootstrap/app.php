<?php

require_once __DIR__.'/../vendor/autoload.php';

Dotenv::load(__DIR__.'/../');

if (env('MAX_EXECUTION_TIME')) {
    ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

if (!class_exists('Application')) {
    class Application extends Laravel\Lumen\Application
    {
        /**
         * Get the path to the application configuration files.
         *
         * @param string $path Optionally, a path to append to the config path
         * @return string
         */
        public function configPath($path = '')
        {
            return $this->basePath.DIRECTORY_SEPARATOR.'config'.($path ? DIRECTORY_SEPARATOR.$path : $path);
        }
    }
}

if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

if (!function_exists('app_path')) {
    /**
     * Get the path to the application folder.
     *
     * @param  string $path
     * @return string
     */
    function app_path($path = '')
    {
        return app('path') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!class_exists('Config')) {
    class_alias('Illuminate\Support\Facades\Config', 'Config');
}

$app = new Application(
    realpath(__DIR__.'/../')
);

$app->withFacades();
$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/


 $app->middleware([
        App\Http\Middleware\CorsMiddleware::class,
        Illuminate\Session\Middleware\StartSession::class,
//	    Barryvdh\Cors\HandleCors::class,
        App\Http\Middleware\UserMiddleware::class
 ]);

 $app->routeMiddleware([
     'token' => 'App\Http\Middleware\TokenMiddleware',
     'role' => 'App\Http\Middleware\RoleAuthMiddleware',
     'key' => 'App\Http\Middleware\KeyMiddleware',
 ]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

 $app->register(App\Providers\AppServiceProvider::class);
// $app->register(Barryvdh\Cors\LumenServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);
$app->register(\App\Providers\LogServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->group(['namespace' => 'App\Http\Controllers'], function ($app) {
    require __DIR__.'/../app/Http/routes.admin.php';
    require __DIR__.'/../app/Http/routes.survey.php';
    require __DIR__.'/../app/Http/routes.sync.php';
});

//$app->configure('cors');


return $app;
