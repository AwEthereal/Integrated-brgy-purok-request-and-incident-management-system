<?php

$titles = [
    'auth/confirm-password.blade.php' => 'Confirm Password',
    'auth/forgot-password.blade.php' => 'Forgot Password',
    'auth/reset-password.blade.php' => 'Reset Password',
    'auth/verify-email.blade.php' => 'Verify Email',
    'dashboard.blade.php' => 'Dashboard',
];

$viewsPath = __DIR__ . '/resources/views/';

foreach ($titles as $file => $title) {
    $fullPath = $viewsPath . $file;
    if (file_exists($fullPath)) {
        $content = file_get_contents($fullPath);
        
        // Add or update the title section
        if (strpos($content, '@extends') !== false) {
            $newContent = preg_replace(
                '/@extends\([^)]+\)(\s*\n)/',
                "@extends('layouts.guest')\n\n@yield('title', '$title')\n\n",
                $content,
                1
            );
            
            if ($newContent !== $content) {
                file_put_contents($fullPath, $newContent);
                echo "Updated title for: $file\n";
            } else {
                echo "No changes needed for: $file\n";
            }
        } else {
            echo "Skipping (no @extends found): $file\n";
        }
    } else {
        echo "File not found: $fullPath\n";
    }
}

echo "\nTitle update complete!\n";
