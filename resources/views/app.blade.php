<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <title>Vue App</title>

    <!-- FullCalendar CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.17/main.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.17/main.min.css" rel="stylesheet" />

    @vite('resources/js/app.js')
</head>
<body>
<div id="app"></div>
</body>
</html>
