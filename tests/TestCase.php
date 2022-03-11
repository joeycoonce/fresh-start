<?php

namespace JoeyCoonce\FreshStart\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use JoeyCoonce\FreshStart\FreshStartServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'JoeyCoonce\\FreshStart\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            FreshStartServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_fresh-start_table.php.stub';
        $migration->up();
        */
    }
}
