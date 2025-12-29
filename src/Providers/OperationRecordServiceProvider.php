<?php

namespace Jsadways\Operationrecord\Providers;

use Illuminate\Support\ServiceProvider;
use Jsadways\Operationrecord\Console\Commands\MakeRecordModelCommand;
use Jsadways\Operationrecord\Services\OperationRecordFactory;

class OperationRecordServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('operation.record', function () {
            return new OperationRecordFactory();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // 註冊 commands（僅在 console 環境）
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeRecordModelCommand::class,
            ]);
        }
    }
}
