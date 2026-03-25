<?php

namespace Ok200\Analytics;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Ok200\Analytics\Listeners\Ok200EventListener;

class Ok200AnalyticsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ok200.php', 'ok200');

        $this->app->singleton(Ok200Client::class, function ($app) {
            return new Ok200Client(
                token: config('ok200.token'),
                endpoint: config('ok200.endpoint'),
            );
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/ok200.php' => config_path('ok200.php'),
        ], 'ok200-config');

        $this->registerEventListeners();
    }

    protected function registerEventListeners(): void
    {
        $events = config('ok200.events', []);

        foreach ($events as $analyticsEvent => $mapping) {
            if (! isset($mapping['event'])) {
                continue;
            }

            Event::listen($mapping['event'], function ($event) use ($analyticsEvent, $mapping) {
                $listener = new Ok200EventListener;
                $listener->handle($event, $analyticsEvent, $mapping);
            });
        }
    }
}
