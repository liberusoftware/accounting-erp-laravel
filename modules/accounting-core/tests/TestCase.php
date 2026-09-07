<?php

declare(strict_types=1);

namespace Liberu\Accounting\Core\Tests;

use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Events\Dispatcher;
use Illuminate\Events\EventServiceProvider;
use Liberu\PackageTestbench\PackageTestCase;

abstract class TestCase extends PackageTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->instance('events', new Dispatcher($this->app));
    }

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * The package dispatches domain events, so its isolated Testbench app must
     * include Laravel's event bindings explicitly.
     *
     * @param  mixed  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            DatabaseServiceProvider::class,
            EventServiceProvider::class,
            ...parent::getPackageProviders($app),
        ];
    }
}
