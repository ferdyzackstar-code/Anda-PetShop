@php
    $favicon    = \App\Models\SettingApp::get('app_image');
    $faviconUrl = $favicon ? asset('storage/' . $favicon) : null;
@endphp

@if ($faviconUrl)
    <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
@endif