<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        <!-- Styles -->
        <link href="/dist/css/app.css" rel="stylesheet">
    </head>
    <body>
        <div id="app">
            <!-- route outlet -->
            <router-view/>
        </div>

        <!-- App bundle -->
        <script src="/dist/js/app.js"></script>
    </body>
</html>
