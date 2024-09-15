<?php

namespace PauloRLima\HubDoDesenvolvedor;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class HubDoDesenvolvedorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('hubdodesenvolvedor')
            ->hasConfigFile();
    }

    public function registeringPackage()
    {
        $this->app->singleton(HubDoDesenvolvedor::class, function ($app) {
            $config = config('hubdodesenvolvedor');

            return new HubDoDesenvolvedor($config['token'], $config['timeout']);
        });
    }
}
