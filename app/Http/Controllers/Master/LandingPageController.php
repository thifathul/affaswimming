<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class LandingPageController extends Controller
{
    public function edit()
    {
        $settings = Setting::whereIn('key', ['landing_title', 'landing_subtitle'])->pluck('value', 'key')->toArray();
        
        $landingTitle = $settings['landing_title'] ?? 'AFFA Swimming Academy';
        $landingSubtitle = $settings['landing_subtitle'] ?? 'We provide professional swimming lessons for all ages with experienced and certified instructors.';

        return view('master.settings.landing_page', compact('landingTitle', 'landingSubtitle'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'landing_title' => 'required|string|max:255',
            'landing_subtitle' => 'required|string',
        ]);

        Setting::updateOrCreate(
            ['key' => 'landing_title'],
            ['value' => $request->landing_title]
        );

        Setting::updateOrCreate(
            ['key' => 'landing_subtitle'],
            ['value' => $request->landing_subtitle]
        );

        return redirect()->route('master.settings.landing')->with('success', 'Pengaturan Halaman Utama berhasil diperbarui!');
    }
}
