<?php

namespace App\Http\Controllers\Laz;

use App\Http\Controllers\Controller;
use App\Models\LazSetting;
use Illuminate\Http\Request;

use App\Traits\LazWhatsAppSender;

class LazSettingController extends Controller
{
    use LazWhatsAppSender;

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
            'whatsapp_api_url' => 'nullable|url',
            'whatsapp_api_key' => 'nullable|string',
        ]);

        foreach ($data as $key => $value) {
            LazSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function sendTest(Request $request)
    {
        $request->validate([
            'test_email' => 'required_without:test_whatsapp|nullable|email',
            'test_whatsapp' => 'required_without:test_email|nullable|string',
        ]);

        $results = [];

        // Test Email
        if ($request->test_email) {
            try {
                \Illuminate\Support\Facades\Mail::raw('Ini adalah email tes dari sistem LAZ.', function ($message) use ($request) {
                    $message->to($request->test_email)
                        ->subject('Tes Email LAZ');
                });
                $results[] = 'Email tes berhasil dikirim ke ' . $request->test_email;
            } catch (\Exception $e) {
                $results[] = 'Gagal mengirim email tes: ' . $e->getMessage();
            }
        }

        // Test WhatsApp
        if ($request->test_whatsapp) {
            try {
                $this->sendWhatsAppMessage($request->test_whatsapp, "Ini adalah pesan tes WhatsApp dari sistem LAZ.");
                $results[] = 'WhatsApp tes berhasil dikirim ke ' . $request->test_whatsapp;
            } catch (\Exception $e) {
                $results[] = 'Gagal mengirim WhatsApp tes: ' . $e->getMessage();
            }
        }

        return back()->with('success', implode('<br>', $results));
    }
}
