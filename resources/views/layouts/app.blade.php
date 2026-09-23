<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (isset($pageSeo))

        <title>{{ $pageSeo['title'] }}</title>

        <meta name="description" content="{{ $pageSeo['description'] }}">

        <link rel="canonical" href="{{ $pageSeo['canonical'] }}">

        <meta name="robots" content="{{ $pageSeo['robots'] }}">

        {{-- Open Graph --}}
        <meta property="og:title" content="{{ $pageSeo['title'] }}">
        <meta property="og:description" content="{{ $pageSeo['description'] }}">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="PesanIn">
        <meta property="og:url" content="{{ $pageSeo['canonical'] }}">
    @elseif (isset($setting) && $setting instanceof \App\Models\WebsiteSetting)
        @php
            $seo = $setting->seoData();
        @endphp

        <title>{{ $seo->title }}</title>

        @if ($seo->description)
            <meta name="description" content="{{ $seo->description }}">
        @endif

        @if ($seo->canonical)
            <link rel="canonical" href="{{ $seo->canonical }}">
        @endif

        @if ($seo->robots)
            <meta name="robots" content="{{ $seo->robots }}">
        @endif

        <meta property="og:title" content="{{ $seo->ogTitle ?: $seo->title }}">

        @if ($seo->ogDescription ?: $seo->description)
            <meta property="og:description" content="{{ $seo->ogDescription ?: $seo->description }}">
        @endif

        @if ($seo->ogImage)
            <meta property="og:image" content="{{ $seo->ogImage }}">
        @endif

        @if ($seo->ogType)
            <meta property="og:type" content="{{ $seo->ogType }}">
        @endif

        @if ($seo->ogSiteName)
            <meta property="og:site_name" content="{{ $seo->ogSiteName }}">
        @endif

        @if ($seo->ogUrl ?: $seo->canonical)
            <meta property="og:url" content="{{ $seo->ogUrl ?: $seo->canonical }}">
        @endif

        @if ($seo->twitterCard)
            <meta name="twitter:card" content="{{ $seo->twitterCard }}">
        @endif

        <meta name="twitter:title" content="{{ $seo->twitterTitle ?: $seo->title }}">

        @if ($seo->twitterDescription ?: $seo->description)
            <meta name="twitter:description" content="{{ $seo->twitterDescription ?: $seo->description }}">
        @endif

        @if ($seo->twitterImage ?: $seo->ogImage)
            <meta name="twitter:image" content="{{ $seo->twitterImage ?: $seo->ogImage }}">
        @endif
    @else
        <title>{{ $title ?? 'PesanIn' }}</title>

    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @stack('styles')

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-[#F3F4F8] text-slate-800 antialiased font-sans">

    @yield('body')

    @if (!View::hasSection('disableSweetAlert'))
        @include('components.sweetalert')
    @endif

    @stack('scripts')

</body>

</html>
