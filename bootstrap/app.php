<?php

use App\Http\Controllers\AlertController;
use App\Http\Middleware\CustomSessionTimeout;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\Role;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\Throttle;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use App\Services\ContractService;
use App\Services\PaymentService;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(['role' => Role::class, 'throttle' => Throttle::class, 'prevent-back-history' => PreventBackHistory::class, 'custom-session-timeout' => CustomSessionTimeout::class]);
        $middleware->web(SetLocale::class);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->call([app(AlertController::class), 'checkDuePayments'])
            ->dailyAt('08:00')
            ->name('check-due-payments')
            ->onFailure(function () {
                Log::error('Failed to check due payments');
            });

        $schedule->call([app(AlertController::class), 'reminderRenewal'])
            ->sundays()
            ->name('reminder-renewal')
            ->onFailure(function () {
                Log::error('Failed to check contracts needing renewal');
            });

        $schedule->call([app(AlertController::class), 'generateMonthlyReport'])
            ->monthly()
            ->name('generate-monthly-report')
            ->onFailure(function () {
                Log::error('Failed to generate monthly report');
            });

        $schedule->call([app(PaymentService::class), 'updateOverduePayments'])
            ->dailyAt('00:00')
            ->name('update-overdue-payments')
            ->onFailure(function () {
                Log::error('Failed to update overdue payments');
            });

        // Check and update completed contracts daily
        $schedule->call(function () {
            app(ContractService::class)->checkAndUpdateCompletedContracts();
        })
        ->daily()
        ->name('check-completed-contracts')
        ->onFailure(function () {
            Log::error('Failed to check and update completed contracts');
        });

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
