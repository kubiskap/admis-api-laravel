<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ADMIS API</title>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            text-align: center;
        }
        h1 {
            color: #333;
            margin-bottom: 2rem;
        }
        p {
            color: #666;
            line-height: 1.6;
        }
        .links {
            margin-top: 2rem;
        }
        .links a {
            color: #3869d4;
            text-decoration: none;
            margin: 0 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>ADMIS API</h1>
        <p>Welcome to the ADMIS API service.</p>
        
        <div class="links">
            <a href="/api/v1/docs">API Documentation</a>
        </div>
    </div>
</body>
</html>