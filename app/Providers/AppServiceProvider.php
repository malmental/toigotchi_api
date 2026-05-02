<?php

declare(strict_types=1);

namespace App\Providers;

use App\Helpers\SanitizePrompt;
use App\Models\Pet;
use App\Policies\PetPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SanitizePrompt::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Pet::class, PetPolicy::class);
    }
}
