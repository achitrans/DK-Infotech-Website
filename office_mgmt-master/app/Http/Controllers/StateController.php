<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StateController extends Controller
{
    public function index()
    {
        $states = State::orderBy('name')->get();

        return view('states.index', compact('states'));
    }

    public function create()
    {
        return view('states.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:5', 'regex:/^[A-Z0-9]+$/', Rule::unique('states')],
            'gst_code' => ['required', 'string', 'min:2','max:2', 'regex:/^[0-9]+$/', Rule::unique('states')],
        ]);

        State::create($data);

        return redirect()->route('states.index')->with('success', 'State created successfully.');
    }

    public function edit(State $state)
    {
        return view('states.edit', compact('state'));
    }

    public function update(Request $request, State $state)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:5', 'regex:/^[A-Z0-9]+$/', Rule::unique('states')->ignore($state->id)],
            'gst_code' => ['required', 'string', 'min:2', 'max:2', 'regex:/^[0-9]+$/', Rule::unique('states')->ignore($state->id)],
        ]);

        $state->update($data);

        return redirect()->route('states.index')->with('success', 'State updated successfully.');
    }

    public function destroy(State $state)
    {
        $state->delete();

        return redirect()->route('states.index')->with('success', 'State removed.');
    }
}
