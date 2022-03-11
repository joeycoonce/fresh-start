<?php

namespace JoeyCoonce\FreshStart;

use JoeyCoonce\FreshStart\Commands\FreshStartCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FreshStartServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('fresh-start')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_fresh-start_table')
            ->hasCommand(FreshStartCommand::class);
    }
}
