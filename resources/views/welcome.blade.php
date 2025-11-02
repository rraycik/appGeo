<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'GeoApp') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Figtree', sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .container {
                text-align: center;
                color: white;
                padding: 2rem;
            }

            h1 {
                font-size: 3rem;
                margin-bottom: 1rem;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            }

            p {
                font-size: 1.25rem;
                margin-bottom: 2rem;
                opacity: 0.9;
            }

            .status {
                display: inline-block;
                padding: 0.5rem 1.5rem;
                background: rgba(255,255,255,0.2);
                border-radius: 50px;
                backdrop-filter: blur(10px);
                font-weight: 600;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🚀 GeoApp</h1>
            <p>Bem-vindo à sua aplicação Laravel!</p>
            <div class="status">✅ Aplicação inicializada com sucesso</div>
        </div>
    </body>
</html>

