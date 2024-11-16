<?php

namespace Codatsoft\Codatbase;

use Illuminate\Support\ServiceProvider;

class CodatbaseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish configuration file to allow customization
        $this->publishes([
            __DIR__.'/config/codatbase.php' => config_path('codatbase.php'),
        ], 'config');

        // Optionally, load other resources if needed (e.g., views, migrations)
        // $this->loadViewsFrom(__DIR__.'/resources/views', 'codatbase');
        // $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Merge configuration with app's config
        $this->mergeConfigFrom(__DIR__.'/config/codatbase.php', 'codatbase');

        // Register services example of singleton use of service to be injected in classes
        //$this->app->singleton(ReportService::class, function () {
        //    return new ReportService();
        //});

        //when report service uses database service to perform actions in signleton way:
       // $this->app->singleton(DatabaseService::class, function ($app) {
       //     return new DatabaseService();
       // });

       // $this->app->singleton(ReportService::class, function ($app) {
       //     return new ReportService($app->make(DatabaseService::class));
       // });

        // this is the same but without using singleton, create new instance each time
       // $this->app->bind(DatabaseService::class, function ($app) {
       //     return new DatabaseService();
      //  });

      //  $this->app->bind(ReportService::class, function ($app) {
      //      return new ReportService($app->make(DatabaseService::class));
      //  });




    }

}