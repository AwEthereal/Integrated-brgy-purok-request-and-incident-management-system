@extends('layouts.app')

@section('title', 'Incident Reports Preview')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Incident Reports Preview</h2>
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
                    <p class="text-sm">Select incident reports to print or click "Print All" to generate a report for all incidents.</p>
                </div>

                <div class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <form method="GET" action="{{ route('reports.incident-reports') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Live search: ID, name, email, location..." class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500" oninput="filterIncidentTable()">
                            <p id="searchResults" class="mt-1 text-xs text-gray-500 dark:text-gray-400"></p>
                        </div>

                        <div>
                            <label for="incident_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Incident Type</label>
                            <select id="incident_type" name="incident_type" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500" onchange="filterIncidentTable()">
                                <option value="">All Types</option>
                                @foreach(\App\Models\IncidentReport::TYPES as $key => $label)
                                    <option value="{{ $key }}" {{ request('incident_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select id="status" name="status" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500" onchange="filterIncidentTable()">
                                <option value="">All Status</option>
                                @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'closed' => 'Closed/Completed', 'invalid' => 'Invalid'] as $val => $label)
                                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-3 flex justify-end gap-2">
                            <a href="{{ route('reports.incident-reports') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-150">Clear Filter</a>
                            <!--<button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-150">Apply Filters</button>-->
                        </div>
                    </form>
                </div>

                <form id="printForm" action="{{ route('reports.download.incident-reports') }}" method="POST">
                    @csrf
                    <div>
                        <div class="w-full overflow-x-auto">
                            <table class="min-w-[1200px] w-full table-fixed divide-y divide-gray-200 dark:divide-gray-700" id="incidentReportsTable">
                            <colgroup>
                                <col class="w-10">
                                <col class="w-24">
                                <col class="w-40">
                                <col class="w-36">
                                <col class="w-40">
                                <col class="w-28">
                                <col class="w-[22rem]">
                                <col class="w-[22rem]">
                                <col class="w-28">
                            </colgroup>
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-3 py-3 text-left">
                                        <input type="checkbox" id="selectAll" onchange="toggleAll(this)" class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                    </th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Report ID</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reporter</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact Number</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Incident Type</th>
                                    <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="pl-6 pr-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Location</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($reports as $report)
                                    @php($reporterName = trim((string) ($report->reporter_name ?? (optional($report->user)->full_name ?: optional($report->user)->name))))
                                    @php($reporterName = $reporterName !== '' ? $reporterName : ($report->is_anonymous ? 'Anonymous' : 'Unknown'))
                                    @php($incidentTypeLabel = $report->incident_type === 'other' ? ($report->incident_type_other ?? 'Other') : (\App\Models\IncidentReport::TYPES[$report->incident_type] ?? format_label($report->incident_type)))
                                    @php($uiStatusKey = in_array($report->status, ['rejected', 'invalid'], true) ? 'invalid' : (in_array($report->status, ['pending','in_progress'], true) ? $report->status : 'closed'))
                                    @php($uiStatusLabel = $uiStatusKey === 'pending' ? 'Pending' : ($uiStatusKey === 'in_progress' ? 'In Progress' : ($uiStatusKey === 'invalid' ? 'Invalid' : 'Closed/Completed')))
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors"
                                        onclick="window.location='{{ route('incident_reports.show', ['id' => $report->id, 'redirect_to' => url()->full()]) }}'"
                                        data-incident-type="{{ $report->incident_type }}"
                                        data-status="{{ $uiStatusKey }}"
                                        data-purok-id="{{ $report->purok_id ?? '' }}">
                                        <td class="px-3 py-4 whitespace-nowrap align-top" onclick="event.stopPropagation()">
                                            <input type="checkbox" name="incident_ids[]" value="{{ $report->id }}" class="incident-checkbox rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap align-top text-sm font-medium text-gray-900 dark:text-white">
                                            #{{ $report->id }}
                                        </td>
                                        <td class="px-3 py-4 align-top text-sm whitespace-normal break-words">
                                            <div class="font-medium text-gray-900 dark:text-white leading-snug">
                                                {{ $reporterName }}
                                            </div>
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap align-top text-sm text-gray-500 dark:text-gray-300">
                                            {{ $report->contact_number ?? optional($report->user)->contact_number ?? '—' }}
                                        </td>
                                        <td class="px-3 py-4 align-top text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300 max-w-full truncate">
                                                {{ $incidentTypeLabel }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap align-top text-center">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ [
                                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                                'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                                'closed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                'invalid' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                            ][$uiStatusKey] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                                {{ $uiStatusLabel }}
                                            </span>
                                        </td>
                                        <td class="pl-6 pr-3 py-4 align-top text-sm text-gray-500 dark:text-gray-300 whitespace-normal break-words" title="{{ $report->description ?? '' }}">
                                            <div class="leading-snug">
                                                {{ \Illuminate\Support\Str::limit((string) ($report->description ?? ''), 60, '...') }}
                                            </div>
                                        </td>
                                        <td class="px-3 py-4 align-top text-sm whitespace-normal break-words">
                                            <div class="text-gray-900 dark:text-white font-medium leading-snug">
                                                {{ $report->location ?: 'No specific location' }}
                                            </div>
                                            @if($report->purok)
                                                <div class="text-gray-500 dark:text-gray-400 text-xs">
                                                    {{ $report->purok->name }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap align-top text-sm text-gray-500 dark:text-gray-300">
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                {{ $report->created_at->format('M d, Y') }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                {{ $report->created_at->format('h:i A') }}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No incident reports found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            </table>
                        </div>
                    </div>

                    @if(method_exists($reports, 'links'))
                        <div class="mt-4">
                            {{ $reports->withQueryString()->links() }}
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAll(source) {
    const checkboxes = document.querySelectorAll('.incident-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = source.checked;
    });
}

function filterIncidentTable() {
    const search = (document.getElementById('search')?.value || '').toLowerCase();
    const type = document.getElementById('incident_type')?.value || '';
    const status = document.getElementById('status')?.value || '';

    const table = document.getElementById('incidentReportsTable');
    const tbody = table?.querySelector('tbody');
    const rows = tbody ? Array.from(tbody.querySelectorAll('tr')) : [];

    let total = 0;
    let visible = 0;

    rows.forEach(row => {
        const isEmptyState = row.querySelector('td[colspan]');
        if (isEmptyState) return;
        total++;

        const text = (row.textContent || '').toLowerCase();
        const rowType = row.getAttribute('data-incident-type') || '';
        const rowStatus = row.getAttribute('data-status') || '';

        const matchesSearch = !search || text.includes(search);
        const matchesType = !type || rowType === type;
        const matchesStatus = !status || rowStatus === status;

        const show = matchesSearch && matchesType && matchesStatus;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    const results = document.getElementById('searchResults');
    if (results) {
        results.textContent = search || type || status ? `Showing ${visible} of ${total} reports` : '';
    }
}

function printAll() {
    // Open PDF preview (all incidents) in a new tab
    window.open("{{ route('reports.preview.incident-reports') }}", '_blank');
}

function printSelected() {
    const selected = Array.from(document.querySelectorAll('.incident-checkbox:checked')).map(cb => cb.value);
    if (selected.length === 0) {
        alert('Please select at least one incident report to preview.');
        return;
    }
    const url = "{{ route('reports.preview.incident-reports') }}" + '?ids=' + selected.join(',');
    window.open(url, '_blank');
}

document.addEventListener('DOMContentLoaded', function(){
    filterIncidentTable();
});
</script>
@endsection
