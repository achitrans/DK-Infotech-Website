<?php

namespace App\Http\Controllers;

use App\Models\PasswordVault;
use App\Models\PasswordVaultAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasswordVaultController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));

        $vaults = PasswordVault::where(function ($query) {
            $query->where('user_id', Auth::id())
                ->orWhere('is_shared', true);
        })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('url', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->latest('updated_at')
            ->simplePaginate(15)
            ->withQueryString();

        return view('password_vaults.index', compact('vaults', 'search'));
    }


    public function show(Request $request, PasswordVault $passwordVault)
    {
        if ($passwordVault->user_id !== Auth::id() && !$passwordVault->is_shared) {
            abort(403);
        }

        $passwordVault->updateQuietly(['last_used_at' => now(),]);

        $this->logAudit($passwordVault, 'viewed', 'Credentials accessed.', $request);

        return view('password_vaults.show', compact('passwordVault'));
    }

    public function create()
    {
        return view('password_vaults.create', ['passwordVault' => new PasswordVault()]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);
        $data['user_id'] = Auth::id();

        $vault = PasswordVault::create($data);

        $this->logAudit($vault, 'created', 'New credentials added.', $request);

        return redirect()->route('password-vaults.show', $vault)->with('success', 'New Credentials saved.');
    }

    public function edit(PasswordVault $passwordVault)
    {
        $this->ensureOwnership($passwordVault);

        return view('password_vaults.edit', compact('passwordVault'));
    }

    public function update(Request $request, PasswordVault $passwordVault)
    {
        $this->ensureOwnership($passwordVault);

        $data = $this->validatePayload($request);
        $passwordVault->update($data);

        $this->logAudit($passwordVault, 'updated', 'Credentials updated.', $request);

        return redirect()->route('password-vaults.show', $passwordVault)->with('success', 'Credentials updated.');
    }

    private function ensureOwnership(PasswordVault $passwordVault): void
    {
        if ($passwordVault->user_id !== Auth::id()) {
            abort(403);
        }
    }

    private function validatePayload(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:1024',
            'url' => 'nullable|url|max:1024',
            'notes' => 'nullable|string|max:2000',
            'category' => 'nullable|string|max:255',
            'last_used_at' => 'nullable|date',
            'is_shared' => 'sometimes|boolean',
        ]);

        $data['is_shared'] = $request->boolean('is_shared');

        return $data;
    }

    private function logAudit(PasswordVault $passwordVault, string $action, string $description, Request $request): void
    {
        PasswordVaultAudit::create([
            'vault_id' => $passwordVault->id,
            'performed_by' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'ip_address' => $request->ip(),
        ]);
    }
}
