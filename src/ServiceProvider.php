<?php

namespace Masaichi\Weather;

class ServiceProvider
{
    protected $defer = true;

    /**
     *
     */
    public function register()
    {
        $this->app->singleton(Weather::class, function(){
            return new Weather(config('services.weather.key'));
        });
        //别名
        $this->app->alias(Weather::class, 'weather');
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [Weather::class, 'weather'];
    }
}