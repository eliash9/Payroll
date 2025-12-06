<?php

namespace App\Http\Controllers\Laz;

use App\Http\Controllers\Controller;
use App\Models\LazSetting;
use Illuminate\Http\Request;

class LazSettingController extends Controller
{
    public function index()
    {
        $settings = LazSetting::all()->pluck('value', 'key');

        return view('laz.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'email_sender_address' => 'required|email',
            'email_sender_name' => 'required|string|max:255',
            'email_new_request_subject' => 'required|string|max:255',
            'email_new_request_body' => 'required|string',
            'email_status_update_subject' => 'required|string|max:255',
            'email_status_update_body' => 'required|string',
        ]);

        foreach ($data as $key => $value) {
            LazSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
