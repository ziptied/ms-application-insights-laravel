<?php namespace Ziptied\MSApplicationInsightsLaravel\Providers;

use ApplicationInsights\Telemetry_Client;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Ziptied\MSApplicationInsightsLaravel\Middleware\MSApplicationInsightsMiddleware;
use Ziptied\MSApplicationInsightsLaravel\MSApplicationInsightsClient;
use Ziptied\MSApplicationInsightsLaravel\MSApplicationInsightsHelpers;
use Ziptied\MSApplicationInsightsLaravel\MSApplicationInsightsServer;

class MSApplicationInsightsServiceProvider extends LaravelServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {
        $this->handleConfigs();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('MSApplicationInsightsServer', function ($app) {
            $telemetryClient = new Telemetry_Client();
            return new MSApplicationInsightsServer($telemetryClient);
        });

        $this->app->singleton('MSApplicationInsightsMiddleware', function ($app) {
            $msApplicationInsightsHelpers = new MSApplicationInsightsHelpers($app['MSApplicationInsightsServer']);

            return new MSApplicationInsightsMiddleware($msApplicationInsightsHelpers);
        });

        $this->app->singleton('MSApplicationInsightsClient', function ($app) {
            return new MSApplicationInsightsClient();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {

        return [
            'MSApplicationInsightsServer',
            'MSApplicationInsightsMiddleware',
            'MSApplicationInsightsClient'
        ];
    }

    private function handleConfigs() {

        $configPath = __DIR__ . '/../../config/MSApplicationInsightsLaravel.php';

        $this->publishes([$configPath => config_path('MSApplicationInsightsLaravel.php')]);

        $this->mergeConfigFrom($configPath, 'MSApplicationInsightsLaravel');
    }
}
