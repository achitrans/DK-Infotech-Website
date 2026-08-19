<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Services\BranchContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class BranchController extends Controller
{
    public function index()
    {
        $search = request('search');

        $query = Branch::with('user')->orderBy('display_name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('display_name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('manager_name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('state', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        $branches = $query->simplePaginate(15)->appends(request()->query());

        return view('branches.index', compact('branches', 'search'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();

        return view('branches.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $this->validateBranch($request);

        Branch::create($data);

        return Redirect::route('branches.index')->with('success', 'Branch created.');
    }

    public function show(Branch $branch)
    {
        return view('branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        $users = User::orderBy('name')->get();

        return view('branches.edit', compact('branch', 'users'));
    }

    public function update(Request $request, Branch $branch)
    {
        $data = $this->validateBranch($request);

        $branch->update($data);

        return Redirect::route('branches.index')->with('success', 'Branch updated.');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return Redirect::route('branches.index')->with('success', 'Branch deleted.');
    }

    public function setActive(Request $request, BranchContext $branchContext)
    {
        $data = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        $branchContext->setActiveBranch((int) $data['branch_id']);

        return back();
    }

    protected function validateBranch(Request $request): array
    {
        return $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'display_name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'gstin' => ['nullable', 'string', 'max:20'],
            'pan' => ['nullable', 'string', 'max:10'],
            'tan' => ['nullable', 'string', 'max:10'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'code' => ['required', 'string', 'max:32', 'unique:branches,code'.($request->route('branch') ? ',' . $request->route('branch')->id : '')],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'manager_phone' => ['nullable', 'string', 'max:20'],
            'state' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:12'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
