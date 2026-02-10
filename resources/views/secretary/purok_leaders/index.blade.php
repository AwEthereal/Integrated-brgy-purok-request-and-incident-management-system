@extends('layouts.app')

@section('title', 'Purok Leaders')

@section('content')
<div class="py-6">
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-semibold">Purok Leaders</h1>
      <a href="{{ route('secretary.purok-leaders.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Add Purok Leader</a>
    </div>

    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4" id="filtersForm">
      <div>
        <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search username/name" class="w-full border rounded p-2 text-sm" oninput="filterRows()" />
        <p id="searchResults" class="mt-1 text-xs text-gray-500"></p>
      </div>
      <div>
        <select name="purok_id" class="w-full border rounded p-2 text-sm" onchange="document.getElementById('filtersForm').submit()">
          <option value="">All Puroks</option>
          @foreach($puroks as $p)
            <option value="{{ $p->id }}" @selected(request('purok_id') == $p->id)>{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <button class="px-3 py-2 bg-gray-700 text-white rounded text-sm w-full">Filter</button>
      </div>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded shadow-sm">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="leadersTable">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purok</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
              <th class="px-4 py-2"></th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse($leaders as $u)
              <tr>
                <td class="px-4 py-2 text-sm">{{ $u->username }}</td>
                <td class="px-4 py-2 text-sm">{{ $u->name }}</td>
                <td class="px-4 py-2 text-sm">{{ $u->purok?->name ?? '—' }}</td>
                <td class="px-4 py-2 text-sm">{{ $u->role_display }}</td>
                <td class="px-4 py-2 text-sm text-right">
                  <a href="{{ route('secretary.purok-leaders.edit', ['purok_leader' => $u->id]) }}" class="px-3 py-1 bg-yellow-500 text-white rounded text-xs">Edit</a>
                  <form action="{{ route('secretary.purok-leaders.destroy', $u) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button class="px-3 py-1 bg-red-600 text-white rounded text-xs" onclick="return confirm('Delete this account?')">Delete</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No leaders found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="p-3">{{ $leaders->withQueryString()->links() }}</div>
    </div>
  </div>
</div>
<script>
function filterRows() {
  const input = document.getElementById('search');
  const filter = input.value.toLowerCase();
  const tbody = document.querySelector('#leadersTable tbody');
  const rows = tbody.getElementsByTagName('tr');
  let visible = 0;
  for (let i = 0; i < rows.length; i++) {
    const row = rows[i];
    const text = row.textContent || row.innerText;
    if (!filter || text.toLowerCase().indexOf(filter) > -1) {
      row.style.display = '';
      visible++;
    } else {
      row.style.display = 'none';
    }
  }
  const results = document.getElementById('searchResults');
  results.textContent = filter ? `Showing ${visible} of ${rows.length} records` : '';
}
</script>
@endsection
