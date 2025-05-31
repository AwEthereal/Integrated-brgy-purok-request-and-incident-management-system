@extends('layouts.app')

@section('content')
<h1>Create Request</h1>

@if ($errors->any())
  <div>
    <ul>
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('requests.store') }}">
  @csrf

  <label for="form_type">Form Type:</label>
  <input type="text" name="form_type" id="form_type" value="{{ old('form_type') }}" required>

  <label for="purpose">Purpose:</label>
  <input type="text" name="purpose" id="purpose" value="{{ old('purpose') }}" required>

  <label for="remarks">Remarks (optional):</label>
  <textarea name="remarks" id="remarks">{{ old('remarks') }}</textarea>

  <button type="submit">Submit Request</button>
</form>

<a href="{{ route('requests.index') }}">Back to Requests</a>
@endsection
