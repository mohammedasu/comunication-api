<?php

namespace App\Providers;

use App\Registry\SmsRegistry;
use App\Services\SNSService;
use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SmsRegistry::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        $this->app->make(SmsRegistry::class)->register("SNS", new SNSService());
    }

}
