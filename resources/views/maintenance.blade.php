<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'System Maintenance' }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 60%, #3b82f6 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        .card {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(12px);
            border-radius: 1.5rem;
            padding: 3rem;
            max-width: 520px;
            width: 90%;
            text-align: center;
        }
        .icon { font-size: 4rem; margin-bottom: 1.5rem; }
        h1 { font-size: 2rem; font-weight: 700; margin-bottom: 1rem; }
        p  { font-size: 1.05rem; line-height: 1.7; opacity: 0.9; }
        .eta {
            margin-top: 2rem;
            background: rgba(255,255,255,0.15);
            border-radius: 0.75rem;
            padding: 1rem 1.5rem;
            font-size: 0.9rem;
        }
        .eta strong { display: block; font-size: 1.1rem; margin-top: 0.25rem; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">🔧</div>
        <h1>{{ $title }}</h1>
        <p>{{ $message }}</p>
        @if(!empty($endAt))
        <div class="eta">
            Estimated back online:
            <strong>{{ \Carbon\Carbon::parse($endAt)->format('F d, Y \a\t h:i A') }}</strong>
        </div>
        @endif
    </div>
</body>
</html>
