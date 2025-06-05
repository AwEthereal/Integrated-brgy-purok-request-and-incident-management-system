<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/test/feedback', function (Request $request) {
    // Simulate having 3 resolved items
    $request->session()->flash('show_feedback_prompt', true);
    $request->session()->flash('resolved_count', 3);
    
    return view('test-feedback');
})->middleware(['auth']);
