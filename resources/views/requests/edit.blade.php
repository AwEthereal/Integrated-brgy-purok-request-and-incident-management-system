@extends('layouts.app')

@section('content')
<h1>Edit Request</h1>

@if ($errors->any())
  <div>
    <ul>
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('requests.update', $request->id) }}">
  @csrf
  @method('PUT')

  <label for="form_type">Form Type:</label>
  <input type="text" name="form_type" id="form_type" value="{{ old('form_type', $request->form_type) }}" required>

  <label for="purpose">Purpose:</label>
  <input type="text" name="purpose" id="purpose" value="{{ old('purpose', $request->purpose) }}" required>

  <label for="remarks">Remarks (optional):</label>
  <textarea name="remarks" id="remarks">{{ old('remarks', $request->remarks) }}</textarea>

  <button type="submit">Update Request</button>
</form>

<a href="{{ route('requests.index') }}">Back to Requests</a>
@endsection
