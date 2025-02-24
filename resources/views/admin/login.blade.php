<!DOCTYPE html>
<html>
<head>
<link rel="icon" type="image/x-icon" href="{{ asset('images/logo1.png') }}">
    <title>Admin Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <admin-login></admin-login>
    </div>
</body>
</html> 