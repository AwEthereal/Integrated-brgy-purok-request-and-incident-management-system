<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8fafc;
            color: #2d3748;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
        }
        .container {
            max-width: 600px;
            padding: 2rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 1.5rem;
            color: #e53e3e;
            margin-bottom: 1rem;
        }
        p {
            margin-bottom: 1.5rem;
            color: #4a5568;
        }
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #4299e1;
            color: white;
            text-decoration: none;
            border-radius: 0.25rem;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #3182ce;
        }
        .error-details {
            margin-top: 2rem;
            padding: 1rem;
            background-color: #fff5f5;
            border-left: 4px solid #feb2b2;
            text-align: left;
            font-size: 0.875rem;
            color: #c53030;
        }
    </style>
</head>
<body>
    <div class="container">
        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-triangle" style="color: #e53e3e; margin-bottom: 1rem;">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>
        <h1>500 - Server Error</h1>
        <p>Sorry, something went wrong on our end. We're working to fix this issue.</p>
        
        @if(isset($exception) && $exception instanceof Exception && config('app.debug'))
            <div class="error-details">
                <p><strong>Error:</strong> {{ $exception->getMessage() }}</p>
                <p><strong>File:</strong> {{ $exception->getFile() }}:{{ $exception->getLine() }}</p>
                @if(method_exists($exception, 'getTraceAsString'))
                    <div style="margin-top: 1rem; font-family: monospace; white-space: pre-wrap; font-size: 0.8rem;">
                        {{ $exception->getTraceAsString() }}
                    </div>
                @endif
            </div>
        @elseif(isset($error) && is_string($error))
            <div class="error-details">
                <p>{{ $error }}</p>
            </div>
        @elseif(isset($message))
            <div class="error-details">
                <p>{{ $message }}</p>
            </div>
        @endif
        
        <div style="margin-top: 2rem;">
            <a href="{{ url('/') }}" class="btn">Return to Homepage</a>
        </div>
    </div>
</body>
</html>
