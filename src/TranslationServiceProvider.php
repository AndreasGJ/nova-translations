<?php

namespace Agj\NovaTranslations;

use Illuminate\Translation\TranslationServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Broadcast;
use Agj\NovaTranslations\Services\TranslationLoader;

class TranslationServiceProvider extends ServiceProvider
{
    protected function registerLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {
            return new TranslationLoader($app['files'], $app['path.lang']);
        });
    }
}
