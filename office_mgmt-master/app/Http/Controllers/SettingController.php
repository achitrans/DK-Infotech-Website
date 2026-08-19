<?php
namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    // List all settings
    public function index()
    {
        $settings = Setting::where('is_internal',0)->get();
        return view('settings.index', compact('settings'));
    }

    // Show edit form for a setting
    public function edit($id)
    {
        $setting = Setting::where('is_internal',0)->where('id',$id)->firstOrFail();
        return view('settings.edit', compact('setting'));
    }

    // Update a setting
    public function update(Request $request, $id)
    {
        $setting = Setting::where('is_internal',0)->where('id',$id)->firstOrFail();
        $request->validate([
            'value' => 'required',
        ]);
        $setting->value = $request->input('value');
        $setting->save();
        return redirect()->route('settings.index')->with('success', 'Setting updated successfully.');
    }
}
