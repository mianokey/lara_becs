<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Task & Project Management') }}</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script>
        // Redirect automatically to /home
        window.location.href = "/home";
    </script>
    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f5f5f5;
            color: #111;
        }

        .container {
            text-align: center;
            padding: 4rem;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            max-width: 400px;
            transition: transform 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        p {
            font-size: 1rem;
            margin-bottom: 2rem;
            color: #555;
        }

        a.button {
            display: inline-block;
            padding: 0.75rem 2rem;
            font-weight: 500;
            text-decoration: none;
            border-radius: 8px;
            background: #111;
            color: #fff;
            transition: background 0.3s ease;
        }

        a.button:hover {
            background: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to {{ config('app.name', 'Task & Project Management') }}</h1>
        <p>You are being redirected to your dashboard...</p>
        <noscript>
            <a href="/home" class="button">Go to Dashboard</a>
        </noscript>
    </div>
</body>
</html>
