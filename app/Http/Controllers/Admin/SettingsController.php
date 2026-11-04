<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\WhatsApp;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function edit()
    {
        $values = collect(Setting::DEFAULTS)->map(fn ($default, $key) => Setting::get($key, ''))->all();

        return view('admin.settings', ['values' => $values, 'testUrl' => WhatsApp::url(__('Test du lien Abidjan Shop'))]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'whatsapp_number' => ['required', 'string', 'regex:/^\+?[\d\s.-]{8,20}$/'],
            'hero_title' => ['nullable', 'string', 'max:120'],
            'hero_subtitle' => ['nullable', 'string', 'max:300'],
            'delivery_fee' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'delivery_delay' => ['nullable', 'string', 'max:120'],
            'delivery_outside' => ['nullable', 'string', 'max:300'],
            'exchange_policy' => ['nullable', 'string', 'max:500'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'contact_email' => ['nullable', 'email', 'max:150'],
            'contact_address' => ['nullable', 'string', 'max:200'],
            'contact_hours' => ['nullable', 'string', 'max:150'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'tiktok_url' => ['nullable', 'url', 'max:255'],
        ], [
            'whatsapp_number.regex' => __('Numéro invalide. Format attendu : indicatif + numéro, ex. 225 07 00 00 00 00.'),
        ]);

        $digits = preg_replace('/\D+/', '', $data['whatsapp_number']);
        // Numéro ivoirien saisi sans indicatif (10 chiffres) : on ajoute 225.
        if (strlen($digits) === 10) {
            $digits = '225'.$digits;
        }
        $data['whatsapp_number'] = $digits;

        Setting::put($data);

        return back()->with('status', __('Réglages enregistrés.'));
    }

    public function editPassword()
    {
        return view('admin.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'email' => ['required', 'email', 'max:150'],
            'password' => ['nullable', 'confirmed', Password::min(10)],
        ]);

        $user = $request->user();
        $user->email = $request->input('email');
        if ($request->filled('password')) {
            $user->password = $request->input('password'); // haché par le cast du modèle
        }
        $user->save();

        return back()->with('status', __('Identifiants mis à jour.'));
    }
}
