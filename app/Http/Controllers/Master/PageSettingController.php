<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class PageSettingController extends Controller
{
    public function edit()
    {
        $keys = [
            'about_owner_message', 'about_owner_photo',
            'contact_address', 'contact_phone', 'contact_email', 'contact_instagram', 'contact_map_embed'
        ];
        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();

        return view('master.settings.pages', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'about_owner_message' => 'nullable|string',
            'about_owner_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'contact_address' => 'nullable|string',
            'contact_phone' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_instagram' => 'nullable|string',
            'contact_map_embed' => 'nullable|string',
        ]);

        $data = $request->except(['_token', '_method', 'about_owner_photo']);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        if ($request->hasFile('about_owner_photo')) {
            $path = $request->file('about_owner_photo')->store('settings', 'public');
            Setting::updateOrCreate(['key' => 'about_owner_photo'], ['value' => $path]);
        }

        return redirect()->back()->with('success', 'Pengaturan Halaman Statis berhasil diperbarui.');
    }
}
