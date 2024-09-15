<?php

namespace PauloRLima\HubDoDesenvolvedor\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use PauloRLima\HubDoDesenvolvedor\HubDoDesenvolvedorServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            HubDoDesenvolvedorServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Configure o ambiente de teste, se necessário
        $app['config']->set('hubdodesenvolvedor.token', 'sua_token');
    }
}
