<!DOCTYPE html>
<!-- saved from url=(0014)about:internet -->
<html lang="en" data-sidenav-size="default" data-bs-theme="light" data-menu-color="dark" data-topbar-color="light"
    data-layout-mode="fluid">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title>@yield('title', 'RoomGate')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description">
    <meta content="Coderthemes" name="author">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets') }}/images/favicon.ico">
    
    <!-- Theme Config Js -->
    <script src="{{ asset('assets') }}/js/config.js"></script>

    <!-- Vendor css -->
    <link href="{{ asset('assets') }}/css/vendor.min.css" rel="stylesheet" type="text/css">

    <!-- App css -->
    <link href="{{ asset('assets') }}/css/app.min.css" rel="stylesheet" type="text/css" id="app-style">

    <!-- Icons css -->
    <link href="{{ asset('assets') }}/css/icons.min.css" rel="stylesheet" type="text/css">
</head>

<body class="h-100">

    @yield(section: 'content')

    <!-- Vendor js -->
    <script src="{{ asset('assets') }}/js/vendor.min.js"></script>

    <!-- App js -->
    <script src="{{ asset('assets') }}/js/app.js"></script>



</body>

</html>
