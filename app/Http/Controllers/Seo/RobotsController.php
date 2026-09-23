<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function index(): Response
    {
        $content = implode("\n", [
            'User-agent: *',
            'Allow: /',
            '',
            'Disallow: /merchant/',
            'Disallow: /super_admin/',
            'Disallow: /dashboard',
            'Disallow: /profile',
            '',
            'Sitemap: ' . rtrim(config('app.url'), '/') . '/sitemap.xml',
        ]);

        return response($content)
            ->header('Content-Type', 'text/plain');
    }
}