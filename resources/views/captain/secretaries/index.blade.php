@extends('layouts.app')

@section('title', 'Manage Accounts')

@section('content')
<div class="py-6">
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
      <div>
        <h1 class="text-2xl font-semibold">Manage Accounts</h1>
        <p class="text-sm text-gray-500">Create and manage official accounts (Secretary, Kagawad, SK, Purok President).</p>
      </div>
      <a href="{{ route('captain.secretaries.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-medium shadow-sm">
        Add Account
      </a>
    </div>

    <div class="mb-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
          <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Search</label>
          <input id="searchInput" type="text" placeholder="Username or name" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 p-2.5 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white" oninput="filterRows()" />
          <p id="searchResults" class="mt-1 text-xs text-gray-500"></p>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Role</label>
          <select id="roleFilter" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 p-2.5 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white" onchange="filterRows()">
            <option value="">All roles</option>
            <option value="barangay_captain">Barangay Captain</option>
            <option value="secretary">Secretary</option>
            <option value="barangay_kagawad">Barangay Kagawad</option>
            <option value="sk_chairman">SK Chairman</option>
            <option value="purok_leader">Purok Leader</option>
          </select>
        </div>
        <div class="flex items-end">
          <button type="button" onclick="clearFilters()" class="w-full md:w-auto inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
            Clear
          </button>
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" id="accountsTable">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Username</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
              <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Purok</th>
              <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($secretaries as $u)
              <tr data-role="{{ $u->role }}" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $u->username }}</td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $u->name }}</td>
                <td class="px-4 py-2 text-sm text-center">
                  <span class="px-2 py-1 rounded-full text-xs {{ $u->role==='secretary' ? 'bg-blue-100 text-blue-800' : ($u->role==='barangay_kagawad' ? 'bg-purple-100 text-purple-800' : ($u->role==='sk_chairman' ? 'bg-pink-100 text-pink-800' : 'bg-green-100 text-green-800')) }}">
                    {{ $u->role === 'barangay_captain' ? 'Barangay Captain' : $u->role_display }}
                  </span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 text-center">
                  {{ $u->purok?->name ?? $u->latestResidentRecord?->purok?->name ?? '-' }}
                </td>
                <td class="px-4 py-2 text-sm text-right">
                  @if(($u->resident_records_count ?? 0) > 0)
                    <a href="{{ route('purok_leader.resident_records.create', ['user_id' => $u->id, 'redirect_to' => url()->full()]) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-md text-xs hover:bg-blue-700">Edit RBI</a>
                  @else
                    <a href="{{ route('purok_leader.resident_records.create', ['user_id' => $u->id, 'redirect_to' => url()->full()]) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-md text-xs hover:bg-blue-700">Encode RBI</a>
                  @endif
                  <a href="{{ route('captain.secretaries.edit', $u) }}" class="inline-flex items-center px-3 py-1.5 bg-amber-500 text-white rounded-md text-xs hover:bg-amber-600">Edit</a>
                  <form action="{{ route('captain.secretaries.destroy', $u) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded text-xs hover:bg-red-700" onclick="return confirm('Delete this account?')">Delete</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No accounts found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="p-3">{{ $secretaries->withQueryString()->links() }}</div>
    </div>
  </div>
</div>
<script>
function filterRows(){
  const s = (document.getElementById('searchInput').value || '').toLowerCase();
  const role = document.getElementById('roleFilter').value;
  const rows = document.querySelectorAll('#accountsTable tbody tr');
  let visible = 0;
  rows.forEach(r => {
    const text = r.textContent.toLowerCase();
    const matchesText = !s || text.includes(s);
    const matchesRole = !role || r.getAttribute('data-role') === role;
    if (matchesText && matchesRole){ r.style.display = ''; visible++; }
    else { r.style.display = 'none'; }
  });
  const res = document.getElementById('searchResults');
  if (res) res.textContent = s || role ? `Showing ${visible} of ${rows.length} accounts` : '';
}

function clearFilters(){
  const searchInput = document.getElementById('searchInput');
  const roleFilter = document.getElementById('roleFilter');
  if (searchInput) searchInput.value = '';
  if (roleFilter) roleFilter.value = '';
  filterRows();
}
</script>
@endsection
