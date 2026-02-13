@extends('layouts.app')

@section('title', 'List of Residents')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">List of Residents</h2>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="printAll()" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-3 rounded-md transition-colors duration-150 text-sm">
                            Print All
                        </button>
                        <button type="button" onclick="printSelected()" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-3 rounded-md transition-colors duration-150 text-sm">
                            Print Selected
                        </button>
                        @can('create', \App\Models\ResidentRecord::class)
                            <a href="{{ route('purok_leader.resident_records.create', ['redirect_to' => url()->full()]) }}" class="inline-flex items-center bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-3 rounded-md transition-colors duration-150 text-sm">
                                Add Resident
                            </a>
                        @endcan
                    </div>
                </div>

                <!-- Filters -->
                <div class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <form method="GET" action="{{ route('reports.residents') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Purok Filter -->
                        <div>
                            <label for="purok_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Filter by Purok
                            </label>
                            <select name="purok_id" id="purok_id" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500" onchange="this.form.submit()">
                                <option value="">All Puroks</option>
                                @foreach($puroks as $purok)
                                    <option value="{{ $purok->id }}" {{ request('purok_id') == $purok->id ? 'selected' : '' }}>
                                        {{ $purok->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Search -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Search
                            </label>
                            <input type="text"
                                   name="search"
                                   id="search"
                                   value="{{ $search ?? request('search') }}"
                                   placeholder="Name, PhilSys no., address, contact..."
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>

                        <!-- Apply/Clear -->
                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-150">
                                Apply Filters
                            </button>
                            @if(request()->hasAny(['purok_id','search']))
                                <a href="{{ route('reports.residents') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-150">Clear</a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <input type="checkbox" id="selectAll" onchange="toggleAll(this)" class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Sex</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Birth Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Address</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Purok</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Purok Leader</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:bg-gray-800 dark:divide-gray-700">
                            @forelse ($records as $r)
                                @php($leader = ($leadersByPurok[$r->purok_id] ?? null))
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" onclick="window.open('{{ route('purok_leader.resident_records.show', ['record' => $r->id]) }}', '_blank')">
                                    <td class="px-4 py-3" onclick="event.stopPropagation()">
                                        <input type="checkbox" class="record-checkbox rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50" value="{{ $r->id }}">
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                        <a href="{{ route('purok_leader.resident_records.show', ['record' => $r->id]) }}" target="_blank" class="hover:underline" onclick="event.stopPropagation()">
                                            {{ $r->last_name }}, {{ $r->first_name }} {{ $r->middle_name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $r->sex }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ optional($r->birth_date)->format('Y-m-d') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $r->residence_address }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $r->contact_number }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $r->purok->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                        @if($leader && $r->user_id && (int) $leader->id === (int) $r->user_id)
                                            <div class="font-medium text-gray-900 dark:text-white">(Purok Leader)</div>
                                        @else
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                {{ $leader?->name ?? ($leader?->first_name ? ($leader->first_name.' '.$leader->last_name) : 'N/A') }}
                                            </div>
                                            <!--<div class="text-xs text-gray-500 dark:text-gray-400">
                                                @if($leader?->username)
                                                    ({{ $leader->username }})
                                                @elseif($leader?->contact_number)
                                                    {{ $leader->contact_number }}
                                                @endif
                                            </div> -->
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right" onclick="event.stopPropagation()">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('purok_leader.resident_records.show', ['record' => $r->id]) }}" target="_blank" class="text-blue-600 hover:underline text-sm">View</a>
                                            <a href="{{ route('purok_leader.resident_records.edit', ['record' => $r->id]) }}" class="text-purple-600 hover:underline text-sm">Edit</a>
                                            <form action="{{ route('purok_leader.resident_records.destroy', ['record' => $r->id]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this record?')">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="redirect" value="{{ url()->full() }}">
                                                <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $records->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const RESIDENTS_RBI_SELECTED_KEY = 'reports_residents_rbi_selected_ids';

function getSelectedResidentIds() {
    try {
        const raw = sessionStorage.getItem(RESIDENTS_RBI_SELECTED_KEY);
        const parsed = raw ? JSON.parse(raw) : [];
        return Array.isArray(parsed) ? parsed.map(String) : [];
    } catch (e) {
        return [];
    }
}

function setSelectedResidentIds(ids) {
    const unique = Array.from(new Set((ids || []).map(String)));
    sessionStorage.setItem(RESIDENTS_RBI_SELECTED_KEY, JSON.stringify(unique));
}

function syncResidentCheckboxesFromStorage() {
    const selected = new Set(getSelectedResidentIds());
    const checkboxes = document.querySelectorAll('.record-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = selected.has(String(cb.value));
    });
    syncSelectAllState();
}

function syncSelectAllState() {
    const all = Array.from(document.querySelectorAll('.record-checkbox'));
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
    const checkboxes = document.querySelectorAll('.record-checkbox');
    checkboxes.forEach(cb => { cb.checked = source.checked; });

    const selected = new Set(getSelectedResidentIds());
    checkboxes.forEach(cb => {
        if (source.checked) {
            selected.add(String(cb.value));
        } else {
            selected.delete(String(cb.value));
        }
    });
    setSelectedResidentIds(Array.from(selected));
    syncSelectAllState();
}

function printAll() {
    window.open("{{ route('reports.preview.residents-rbi') }}", '_blank');
}

function printSelected() {
    const selected = getSelectedResidentIds();
    if (!selected || selected.length === 0) {
        alert('Please select at least one resident to preview.');
        return;
    }
    const url = "{{ route('reports.preview.residents-rbi') }}" + '?ids=' + selected.join(',');
    window.open(url, '_blank');
}

document.addEventListener('change', function (e) {
    const target = e.target;
    if (!target || !target.classList || !target.classList.contains('record-checkbox')) {
        return;
    }

    const selected = new Set(getSelectedResidentIds());
    const value = String(target.value);
    if (target.checked) {
        selected.add(value);
    } else {
        selected.delete(value);
    }
    setSelectedResidentIds(Array.from(selected));
    syncSelectAllState();
});

document.addEventListener('DOMContentLoaded', function () {
    syncResidentCheckboxesFromStorage();
});
</script>
@endsection
