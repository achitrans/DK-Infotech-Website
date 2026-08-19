<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('date', 'asc')->get();
        return view('holidays.index', compact('holidays'));
    }

    public function create()
    {
        if (!Auth::user()->isAdmin()){
            return abort(403);
        }
        return view('holidays.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()){
            return abort(403);
        }
        $data = $request->validate([
            'date' => 'required|date',
            'type' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:200',
            'is_optional' => 'boolean',
        ]);
        Holiday::create($data);
        return redirect()->route('holidays.index')->with('success', 'Holiday created successfully.');
    }

    public function edit($id)
    {
        if (!Auth::user()->isAdmin()){
            return abort(403);
        }
        $holiday = Holiday::findOrFail($id);
        return view('holidays.edit', compact('holiday'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->isAdmin()){
            return abort(403);
        }
        $holiday = Holiday::findOrFail($id);
        $data = $request->validate([
            'date' => 'required|date',
            'type' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:200',
            'is_optional' => 'boolean',
        ]);
        $holiday->update($data);
        return redirect()->route('holidays.index')->with('success', 'Holiday updated successfully.');
    }

    public function destroy($id)
    {
        if (!Auth::user()->isAdmin()){
            return abort(403);
        }
        $holiday = Holiday::findOrFail($id);
        $holiday->delete();
        return redirect()->route('holidays.index')->with('success', 'Holiday deleted successfully.');
    }
}
