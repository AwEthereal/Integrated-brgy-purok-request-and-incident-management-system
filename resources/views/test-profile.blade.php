<!DOCTYPE html>
<html>
<head>
    <title>Test Profile</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px;
            background-color: #f3f4f6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 { color: #1f2937; }
        .user-info { 
            margin-top: 20px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Test Profile Page</h1>
        <p>This is a test page to check if views are working.</p>
        
        @if(isset($user))
            <div class="user-info">
                <h2>User Information</h2>
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
            </div>
        @else
            <div style="color: red; margin-top: 20px;">
                No user data available
            </div>
        @endif
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <h3>Debug Information</h3>
            <p><strong>Current URL:</strong> {{ url()->current() }}</p>
            <p><strong>Previous URL:</strong> {{ url()->previous() }}</p>
            <p><strong>User Authenticated:</strong> {{ auth()->check() ? 'Yes' : 'No' }}</p>
        </div>
    </div>
</body>
</html>
