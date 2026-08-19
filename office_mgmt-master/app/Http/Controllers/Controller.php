<?php

namespace App\Http\Controllers;

use App\Services\BranchContext;

abstract class Controller
{
    protected BranchContext $branchContext;

    public function __construct(BranchContext $branchContext)
    {
        $this->branchContext = $branchContext;
    }
}
