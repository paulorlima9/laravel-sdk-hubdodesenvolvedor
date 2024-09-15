<?php

namespace PauloRLima\HubDoDesenvolvedor\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \PauloRLima\HubDoDesenvolvedor\HubDoDesenvolvedor
 */
class HubDoDesenvolvedor extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \PauloRLima\HubDoDesenvolvedor\HubDoDesenvolvedor::class;
    }
}
