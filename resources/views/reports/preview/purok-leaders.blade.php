@extends('layouts.app')

@section('title', 'Purok Leaders Report Preview')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Purok Leaders Report Preview</h2>
                    <div class="flex gap-2">
                        @if(in_array(auth()->user()->role, ['secretary','admin']))
                        <a href="{{ route('secretary.purok-leaders.create', ['redirect_to' => url()->full()]) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Purok Leader
                        </a>
                        @endif
                        <button onclick="printAll()" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Print All
                        </button>
                        <button onclick="printSelected()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Print Selected
                        </button>
                    </div>
                </div>

                <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-500 text-blue-700 dark:text-blue-200">
                    <p class="text-sm">Select purok leaders to print or click "Print All" to generate a report for all leaders. Click on any row to view full profile.</p>
                </div>

                <!-- Filters -->
                <div class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <form method="GET" action="{{ route('reports.purok-leaders') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Purok Filter -->
                        <div>
                            <label for="purok_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Filter by Purok
                            </label>
                            <select name="purok_id" id="purok_id" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                                <option value="">All Puroks</option>
                                @foreach($puroks as $purok)
                                    <option value="{{ $purok->id }}" {{ request('purok_id') == $purok->id ? 'selected' : '' }}>
                                        {{ $purok->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Search Filter -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Search
                            </label>
                            <input type="text" 
                                   name="search" 
                                   id="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Name, email, contact..." 
                                   oninput="filterTable()"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                            <p id="searchResults" class="mt-1 text-xs text-gray-500"></p>
                        </div>

                        <!-- Filter Button -->
                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-150">
                                Apply Filters
                            </button>
                            @if(request()->hasAny(['purok_id', 'search']))
                                <a href="{{ route('reports.purok-leaders') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-150">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <form id="printForm" action="{{ route('reports.download.purok-leaders') }}" method="POST">
                    @csrf
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left">
                                        <input type="checkbox" id="selectAll" onchange="toggleAll(this)" class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Purok</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact Number</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">DOB</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Sex</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date assigned</th>
                                    @if(in_array(auth()->user()->role, ['secretary','admin']))
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($leaders as $leader)
                                    @php($rbi = $leader->latestResidentRecord)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors" onclick="window.location='{{ route('reports.purok-leaders.show', $leader->id) }}'">
                                        <td class="px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                                            <input type="checkbox" name="leader_ids[]" value="{{ $leader->id }}" class="leader-checkbox rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                {{ $rbi?->first_name ?? $leader->first_name }} {{ $rbi?->middle_name ?? $leader->middle_name }} {{ $rbi?->last_name ?? $leader->last_name }}
                                            </div>
                                            <div class="text-gray-500 dark:text-gray-400 text-xs">
                                                {{ ($rbi?->suffix ?? $leader->suffix) ? ($rbi?->suffix ?? $leader->suffix) : '' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $leader->purok->name ?? 'N/A' }}</div>
                                            <div class="text-gray-500 dark:text-gray-400 text-xs">Leader</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                                            {{ $rbi?->contact_number ?? $leader->contact_number ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                                            {{ $rbi?->birth_date?->format('Y-m-d') ?? ($leader->birth_date ? $leader->birth_date->format('Y-m-d') : ($leader->date_of_birth ?? 'N/A')) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                                            {{ $rbi?->sex ?? $leader->sex ?? $leader->gender ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                                            {{ $leader->created_at->format('M d, Y') }}
                                        </td>
                                        @if(in_array(auth()->user()->role, ['secretary','admin']))
                                        <td class="px-6 py-4 text-sm text-right" onclick="event.stopPropagation()">
                                            <a href="{{ route('secretary.purok-leaders.edit', ['purok_leader' => $leader->id, 'redirect_to' => url()->full()]) }}" class="inline-flex items-center px-3 py-1.5 bg-yellow-500 text-white rounded text-xs hover:bg-yellow-600">
                                                Edit
                                            </a>
                                        </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No purok leaders found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function filterTable() {
    const input = document.getElementById('search');
    const filter = input.value.toLowerCase();
    const table = document.querySelector('tbody');
    const rows = table.getElementsByTagName('tr');
    let visibleCount = 0;
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent || row.innerText;
        
        if (text.toLowerCase().indexOf(filter) > -1) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    }
    
    const resultsText = document.getElementById('searchResults');
    if (filter) {
        resultsText.textContent = `Showing ${visibleCount} of ${rows.length} leaders`;
    } else {
        resultsText.textContent = '';
    }
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    filterTable();
}

function toggleAll(source) {
    const checkboxes = document.querySelectorAll('.leader-checkbox:not([style*="display: none"])');
    checkboxes.forEach(checkbox => {
        checkbox.checked = source.checked;
    });
}

function printAll() {
    // Navigate to preview page (all leaders) in a new tab
    window.open("{{ route('reports.preview.purok-leaders') }}", '_blank');
}

function printSelected() {
    const selected = Array.from(document.querySelectorAll('.leader-checkbox:checked')).map(cb => cb.value);
    if (selected.length === 0) {
        alert('Please select at least one purok leader to preview.');
        return;
    }
    const url = "{{ route('reports.preview.purok-leaders') }}" + '?ids=' + selected.join(',');
    window.open(url, '_blank');
}
</script>
@endsection
