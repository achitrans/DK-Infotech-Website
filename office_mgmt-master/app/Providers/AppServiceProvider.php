<?php

namespace App\Providers;

use App\Services\BranchContext;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(BranchContext $branchContext): void
    {
        View::composer(['layouts.*'], function ($view) use ($branchContext) {
            if (! $branchContext->shouldShowDropdown()) {
                return;
            }

            $view->with([
                'branchContextBranches' => $branchContext->availableBranches(),
                'branchContextActiveBranchId' => $branchContext->getActiveBranchIdForDropdown(),
            ]);
        });
    }
}
