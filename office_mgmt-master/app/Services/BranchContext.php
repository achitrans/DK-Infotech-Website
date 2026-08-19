<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class BranchContext
{
    public const SESSION_KEY = 'active_branch_id';

    private ?int $resolvedBranchId = null;
    private ?Collection $cachedBranches = null;

    public function currentBranchId(?User $user = null): ?int
    {
        if ($this->resolvedBranchId !== null) {
            return $this->resolvedBranchId;
        }

        $user = $user ?? Auth::user();

        if (! $user) {
            return $this->resolvedBranchId = null;
        }

        if ($this->isAdminOrManager($user)) {
            return $this->resolvedBranchId = $this->resolveAdminBranchId($user);
        }

        return $this->resolvedBranchId = $user->branch_id;
    }

    public function availableBranches(?User $user = null): Collection
    {
        if ($this->cachedBranches !== null) {
            return $this->cachedBranches;
        }

        $user = $user ?? Auth::user();

        if (! $user) {
            return $this->cachedBranches = collect();
        }

        if ($user->isAdmin()) {
            $branches = Branch::where('is_active', true)->get();
        } elseif ($user->isBranchManager()) {
            $branches = Branch::where('user_id', $user->id)
                ->where('is_active', true)
                ->get();
        } elseif ($user->branch_id) {
            $branches = Branch::where('id', $user->branch_id)
                ->get();
        } else {
            $branches = Branch::where('is_active', true)
                ->limit(1)
                ->get();
        }

        return $this->cachedBranches = $branches;
    }

    public function shouldShowDropdown(?User $user = null): bool
    {
        $user = $user ?? Auth::user();

        if (! $user) {
            return false;
        }

        return $user->isAdmin() || $user->isBranchManager();
    }

    public function getActiveBranchIdForDropdown(?User $user = null): ?int
    {
        return $this->currentBranchId($user);
    }

    public function setActiveBranch(int $branchId, ?User $user = null): void
    {
        $user = $user ?? Auth::user();

        if (! $this->shouldShowDropdown($user)) {
            return;
        }

        if (! $this->availableBranches($user)->contains('id', $branchId)) {
            return;
        }

        session()->put(self::SESSION_KEY, $branchId);
        $this->resolvedBranchId = $branchId;
    }

    protected function resolveAdminBranchId(User $user): ?int
    {
        $branches = $this->availableBranches($user);

        if ($branches->isEmpty()) {
            session()->forget(self::SESSION_KEY);
            return null;
        }

        $selected = session()->get(self::SESSION_KEY);

        if ($selected && $branches->contains('id', $selected)) {
            return $selected;
        }

        $default = $branches->first()->id;
        session()->put(self::SESSION_KEY, $default);

        return $default;
    }

    protected function isAdminOrManager(User $user): bool
    {
        return $user->isAdmin() || $user->isBranchManager();
    }
}
