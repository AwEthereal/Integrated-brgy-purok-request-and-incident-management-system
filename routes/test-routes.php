<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/test-file-access', function () {
    $latestRequest = \App\Models\Request::latest()->first();
    
    if (!$latestRequest) {
        return 'No requests found';
    }

    $frontPath = $latestRequest->valid_id_front_path;
    $backPath = $latestRequest->valid_id_back_path;

    $frontExists = file_exists(public_path($frontPath));
    $backExists = file_exists(public_path($backPath));

    return [
        'request_id' => $latestRequest->id,
        'front_path' => $frontPath,
        'front_exists' => $frontExists ? 'Yes' : 'No',
        'front_public_url' => $frontPath ? asset($frontPath) : 'N/A',
        'back_path' => $backPath,
        'back_exists' => $backExists ? 'Yes' : 'No',
        'back_public_url' => $backPath ? asset($backPath) : 'N/A',
    ];
});
