@extends('layouts.app')

@section('content')
<h1>Request Details</h1>

<p><strong>Form Type:</strong> {{ $request->form_type }}</p>
<p><strong>Purpose:</strong> {{ $request->purpose }}</p>
<p><strong>Status:</strong> {{ $request->status ?? 'Pending' }}</p>
<p><strong>Remarks:</strong> {{ $request->remarks ?? 'None' }}</p>

<a href="{{ route('requests.index') }}">Back to Requests</a>
<a href="{{ route('requests.edit', $request->id) }}">Edit Request</a>
@endsection
