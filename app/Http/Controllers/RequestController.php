<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Request as RequestModel;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    // List all requests for the logged-in user
    public function index()
    {
    
        $requests = Auth::user()->requests()->get();
        return view('requests.index', compact('requests'));
    }

    // Show form to create a new request
    public function create()
    {
        return view('requests.create');
    }

    // Save a new request
    public function store(HttpRequest $request)
    {
        $data = $request->validate([
            'form_type' => 'required|string|max:255',
            'purpose' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $request = Auth::user()->requests()->create([
            ...$data,
            'status' => 'pending',
            'purok_id' => Auth::user()->purok_id
        ]);

        // Send email notification to resident
        \Mail::to(Auth::user())->send(new \App\Mail\PurokClearanceRequested($request));

        return redirect()->route('requests.index')->with('success', 'Request created. You will be notified via email when it is approved.');
    }

    // Show a single request
    public function show(RequestModel $request)
    {
        $this->authorize('view', $request);

        return view('requests.show', ['request' => $request]);
    }

    // Show form to edit a request
    public function edit(RequestModel $request)
    {
        $this->authorize('update', $request);
        return view('requests.edit', ['request' => $request]);
    }

    // Update a request
    public function approvePurok(RequestModel $request, HttpRequest $requestData)
    {
        $this->authorize('approvePurok', $request);

        $request->update([
            'status' => 'purok_approved',
            'purok_approved_at' => now(),
            'purok_approved_by' => Auth::id()
        ]);

        // Send email notification to resident
        \Mail::to($request->user)->send(new \App\Mail\PurokClearanceApproved($request));

        return redirect()->route('requests.index')->with('success', 'Request approved.');
    }

    // Delete a request
    public function destroy(RequestModel $request)
    {
        $this->authorize('delete', $request);

        $request->delete();

        return redirect()->route('requests.index')->with('success', 'Request deleted.');
    }
}
