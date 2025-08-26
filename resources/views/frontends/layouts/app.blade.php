<!DOCTYPE html>
<html data-wf-page="68369d6ecd4dbc0b4f6e2f6e" data-wf-site="683588d6afb7bd5a9fb70ef5" data-wf-status="1" lang="en">
<head>
    <meta charset="utf-8"/>
    <title>@yield('title', 'RoomGate')</title>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    
    <meta content="{{ $content['seo']->description ?? 'Default Description' }}" name="description"/>
    <meta content="{{ $content['seo']->title ?? 'RoomGate' }}" property="og:title"/>
    <meta content="{{ $content['seo']->description ?? 'Default Description' }}" property="og:description"/>
    <meta content="{{ asset($content['seo']->image_path ?? '') }}" property="og:image"/>
    
    <link href="{{ asset('asset_frontend/css/flowis.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('asset_frontend/css/style.css') }}" rel="stylesheet" type="text/css"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin="anonymous"/>
    
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}"> 
    <link href="{{ asset('asset_frontend/images/6835899dc1c24f1d94bc3605_Webclip.png') }}" rel="apple-touch-icon"/>
    
    <script src="{{ asset('asset_frontend/js/webfont.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        WebFont.load({ google: { families: ["Geist:100,200,300,regular,500,600,700,800,900", "Geist Mono:regular"] } });
    </script>
</head>
<body>
    <div class="page-wrapper">
        @include('frontends.layouts.partials.navbar')

        <main class="main-wrapper">
            @yield('content')
        </main>

        @include('frontends.layouts.partials.footer')
    </div>

    <script src="{{ asset('asset_frontend/js/jquery-3.5.1.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('asset_frontend') }}/js/webflow.schunk.bfd5c8b822744376.js" type="text/javascript"></script>
    <script src="{{ asset('asset_frontend') }}/js/webflow.schunk.7d96b44c939f450f.js" type="text/javascript"></script>
    <script src="{{ asset('asset_frontend/js/webflow.js') }}" type="text/javascript"></script>
    
</body>
</html>