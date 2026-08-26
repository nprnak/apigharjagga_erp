<?php

namespace App\Providers;

use App\Http\Controllers\Auth\FilamentLogoutController;
use Carbon\CarbonImmutable;
use Filament\Auth\Http\Controllers\LogoutController;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Use a guard-scoped logout for the Filament panels so signing out of
        // one panel does not tear down a session established on the other.
        $this->app->bind(LogoutController::class, FilamentLogoutController::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        \App\Models\KycVerification::observe(\App\Observers\KycVerificationObserver::class);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
