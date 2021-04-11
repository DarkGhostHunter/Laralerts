<?php

namespace Tests;

trait RegistersPackage
{
    protected function getPackageAliases($app)
    {
        return [
            'ReCaptcha' => 'DarkGhostHunter\Laralerts\Facades\Alert'
        ];
    }

    protected function getPackageProviders($app)
    {
        return ['DarkGhostHunter\Laralerts\LaralertsServiceProvider'];
    }
}
