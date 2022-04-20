<?php

namespace tanthammar\Recurring;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use tanthammar\Recurring\Commands\RecurringCommand;

class RecurringServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('recurring')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_recurring_table')
            ->hasCommand(RecurringCommand::class);
    }
}
