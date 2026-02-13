@extends('layouts.app')

@section('title', 'Clearance Requests Report Preview')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Purok Clearance Requests Preview</h2>
                    <div class="flex gap-2">
                        <button type="button" onclick="printAll()" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Print All
                        </button>
                        <button type="button" onclick="printSelected()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Print Selected
                        </button>
                    </div>
                </div>

                <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-500 text-blue-700 dark:text-blue-200">
                    <p class="text-sm">Select requests to print or click "Print All" to generate a report for all clearance requests.</p>
                </div>

                <div class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <form method="GET" action="{{ route('reports.purok-clearance') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Live search: ID, resident, email, purpose..." class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500" oninput="filterClearanceTable()">
                            <p id="searchResults" class="mt-1 text-xs text-gray-500 dark:text-gray-400"></p>
                        </div>

                        <div>
                            <label for="purok_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Purok</label>
                            <select id="purok_id" name="purok_id" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500" onchange="filterClearanceTable()">
                                <option value="">All Puroks</option>
                                @foreach(($puroks ?? \App\Models\Purok::orderBy('name')->get()) as $purok)
                                    <option value="{{ $purok->id }}" {{ (string) request('purok_id') === (string) $purok->id ? 'selected' : '' }}>{{ $purok->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select id="status" name="status" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500" onchange="filterClearanceTable()">
                                <option value="">All Status</option>
                                @foreach(['pending','purok_approved','completed','rejected'] as $s)
                                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ format_label($s) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-4 flex items-end justify-end gap-2">
                            <a href="{{ route('reports.purok-clearance') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-150">Clear Filter</a>
                            
                        </div>
                    </form>
                </div>

                <form id="printForm" action="{{ route('reports.download.purok-clearance') }}" method="POST" target="_blank">
                    @csrf
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" id="purokClearanceTable">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left">
                                        <input type="checkbox" id="selectAll" onchange="toggleAll(this)" class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Request ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Resident</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Purpose</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Purok</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @php
                                    $viewerRole = auth()->user()->role ?? '';
                                    $isOfficialViewer = in_array($viewerRole, ['secretary', 'barangay_captain', 'barangay_kagawad', 'admin'], true);
                                @endphp
                                @forelse($requests as $request)
                                    @php($rowUrl = $isOfficialViewer ? route('official.clearance.view', $request->id) : route('requests.show', $request->id))
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" onclick="window.location='{{ $rowUrl }}'" data-purok-id="{{ $request->purok_id ?? '' }}" data-status="{{ $request->status ?? '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                                            <input type="checkbox" name="request_ids[]" value="{{ $request->id }}" class="request-checkbox rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            #{{ $request->id }}
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                @php($residentName = $request->requester_name ?? (optional($request->user)->full_name ?: optional($request->user)->name))
                                                {{ $residentName ?: 'N/A' }}
                                            </div>
                                            <div class="text-gray-500 dark:text-gray-400 text-xs">
                                                {{ $request->email ?? optional($request->user)->email ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                                            {{ $request->purpose ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                                            {{ $request->purok->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ [
                                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                                'purok_approved' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                                'barangay_approved' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
                                                'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                            ][$request->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                                {{ format_label($request->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                                            {{ $request->created_at->format('M d, Y') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No clearance requests found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($requests, 'links'))
                        <div class="mt-4">
                            {{ $requests->withQueryString()->links() }}
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const PUROK_CLEARANCE_SELECTED_KEY = 'reports_purok_clearance_selected_ids';

function getSelectedClearanceIds() {
    try {
        const raw = sessionStorage.getItem(PUROK_CLEARANCE_SELECTED_KEY);
        const parsed = raw ? JSON.parse(raw) : [];
        return Array.isArray(parsed) ? parsed.map(String) : [];
    } catch (e) {
        return [];
    }
}

function setSelectedClearanceIds(ids) {
    const unique = Array.from(new Set((ids || []).map(String)));
    sessionStorage.setItem(PUROK_CLEARANCE_SELECTED_KEY, JSON.stringify(unique));
}

function syncClearanceCheckboxesFromStorage() {
    const selected = new Set(getSelectedClearanceIds());
    const checkboxes = document.querySelectorAll('.request-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = selected.has(String(cb.value));
    });
    syncClearanceSelectAllState();
}

function syncClearanceSelectAllState() {
    const all = Array.from(document.querySelectorAll('.request-checkbox'));
    const selectAll = document.getElementById('selectAll');
    if (!selectAll) return;
    if (all.length === 0) {
        selectAll.checked = false;
        selectAll.indeterminate = false;
        return;
    }
    const checkedCount = all.filter(cb => cb.checked).length;
    selectAll.checked = checkedCount > 0 && checkedCount === all.length;
    selectAll.indeterminate = checkedCount > 0 && checkedCount < all.length;
}

function toggleAll(source) {
    const checkboxes = document.querySelectorAll('.request-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = source.checked;
    });

    const selected = new Set(getSelectedClearanceIds());
    checkboxes.forEach(cb => {
        if (source.checked) {
            selected.add(String(cb.value));
        } else {
            selected.delete(String(cb.value));
        }
    });
    setSelectedClearanceIds(Array.from(selected));
    syncClearanceSelectAllState();
}

function printAll() {
    const url = "{{ route('reports.pdf.purok-clearance') }}";
    window.open(url, '_blank');
}

function printSelected() {
    const selected = getSelectedClearanceIds();
    if (!selected || selected.length === 0) {
        alert('Please select at least one request to preview.');
        return;
    }
    const url = "{{ route('reports.pdf.purok-clearance') }}" + '?ids=' + selected.join(',');
    window.open(url, '_blank');
}

function filterClearanceTable() {
    const search = (document.getElementById('search')?.value || '').toLowerCase();
    const purokId = document.getElementById('purok_id')?.value || '';
    const status = document.getElementById('status')?.value || '';

    const table = document.getElementById('purokClearanceTable');
    const tbody = table?.querySelector('tbody');
    const rows = tbody ? Array.from(tbody.querySelectorAll('tr')) : [];

    let total = 0;
    let visible = 0;

    rows.forEach(row => {
        const isEmptyState = row.querySelector('td[colspan]');
        if (isEmptyState) return;
        total++;

        const text = (row.textContent || '').toLowerCase();
        const rowPurok = row.getAttribute('data-purok-id') || '';
        const rowStatus = row.getAttribute('data-status') || '';

        const matchesSearch = !search || text.includes(search);
        const matchesPurok = !purokId || rowPurok === purokId;
        const matchesStatus = !status || rowStatus === status;

        const show = matchesSearch && matchesPurok && matchesStatus;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    const results = document.getElementById('searchResults');
    if (results) {
        results.textContent = search || purokId || status ? `Showing ${visible} of ${total} requests (this page)` : '';
    }
}

document.addEventListener('DOMContentLoaded', function(){
    syncClearanceCheckboxesFromStorage();
    filterClearanceTable();
});

document.addEventListener('change', function (e) {
    const target = e.target;
    if (!target || !target.classList || !target.classList.contains('request-checkbox')) {
        return;
    }

    const selected = new Set(getSelectedClearanceIds());
    const value = String(target.value);
    if (target.checked) {
        selected.add(value);
    } else {
        selected.delete(value);
    }
    setSelectedClearanceIds(Array.from(selected));
    syncClearanceSelectAllState();
});
</script>
@endsection
