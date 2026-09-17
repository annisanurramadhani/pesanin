<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Rules\SecureText;


class WebsiteSettingController extends Controller
{

    public function index()
    {

        $setting = WebsiteSetting::first();


        return view(
            'super_admin.settings.index',
            compact('setting')
        );
    }




    public function update(Request $request)
    {


        $setting = WebsiteSetting::first();



        $validated = $request->validate([


            /*
            |--------------------------------------------------------------------------
            | GENERAL
            |--------------------------------------------------------------------------
            */

            'website_name'
            => 'required|string|max:255',
            New SecureText(),


            'tagline'
            => 'nullable|string|max:255',
            New SecureText(),




            /*
            |--------------------------------------------------------------------------
            | LOGO & FAVICON
            |--------------------------------------------------------------------------
            */


            'logo'
            => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',


            'favicon'
            => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',





            /*
            |--------------------------------------------------------------------------
            | HERO
            |--------------------------------------------------------------------------
            */


            'hero_badge'
            => 'nullable|string|max:255',


            'hero_content'
            => 'required|string',
            New SecureText(),


            'hero_description'
            => 'required|string',
            New SecureText(),





            /*
            |--------------------------------------------------------------------------
            | CTA
            |--------------------------------------------------------------------------
            */


            'cta_content'
            => 'required|string',
            New SecureText(),


            'cta_description'
            => 'nullable|string',
            New SecureText(),

            'cta_button_text'
            => 'required|string|max:255',





            /*
            |--------------------------------------------------------------------------
            | FOOTER
            |--------------------------------------------------------------------------
            */


            'footer_text'
            => 'nullable|string|max:255',


            'footer_email'
            => 'nullable|email|max:255',


            'footer_whatsapp'
            => 'nullable|string|max:20',





            /*
            |--------------------------------------------------------------------------
            | SOCIAL MEDIA
            |--------------------------------------------------------------------------
            */


            'instagram_url'
            => 'nullable|url|max:255',


        ]);






        /*
        |--------------------------------------------------------------------------
        | UPLOAD LOGO
        |--------------------------------------------------------------------------
        */


        if ($request->hasFile('logo')) {


            // hapus logo lama
            if ($setting->logo) {
                Storage::disk('public')
                    ->delete($setting->logo);
            }



            $validated['logo'] =
                $request->file('logo')
                ->store('website', 'public');
        }







        /*
        |--------------------------------------------------------------------------
        | UPLOAD FAVICON
        |--------------------------------------------------------------------------
        */


        if ($request->hasFile('favicon')) {


            // hapus favicon lama

            if ($setting->favicon) {
                Storage::disk('public')
                    ->delete($setting->favicon);
            }



            $validated['favicon'] =
                $request->file('favicon')
                ->store('website', 'public');
        }







        $setting->update($validated);






        return back()->with(
            'success',
            'Pengaturan website berhasil diperbarui.'
        );
    }
}
