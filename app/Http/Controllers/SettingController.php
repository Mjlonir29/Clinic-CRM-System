<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        $user = Auth::user();

        return view('settings.index', compact('settings', 'user'));
    }

    public function update(Request $request)
    {
        $group = $request->input('group', 'general');
        $inputs = $request->except(['_token', 'group']);

        foreach ($inputs as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            Setting::set($key, $value, $group);
        }

        return back()->with('success', ucfirst(str_replace('_', ' ', $group)) . ' updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The provided current password does not match.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Security password changed successfully.');
    }
}
