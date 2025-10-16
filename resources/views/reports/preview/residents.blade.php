@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Residents Report Preview</h2>
                    <div class="flex gap-2">
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
                    <p class="text-sm">Select residents to print or click "Print All" to generate a report for all residents.</p>
                </div>

                <!-- Search Filter -->
                <div class="mb-4">
                    <div class="relative">
                        <input type="text" 
                               id="searchInput" 
                               placeholder="Search by name, purok, address, contact..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white"
                               onkeyup="filterTable()">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <button onclick="clearSearch()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <p id="searchResults" class="mt-2 text-sm text-gray-600 dark:text-gray-400"></p>
                </div>

                <form id="printForm" action="{{ route('reports.download.residents') }}" method="POST">
                    @csrf
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left">
                                        <input type="checkbox" id="selectAll" onchange="toggleAll(this)" class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Full Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Personal Info</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date of Birth</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Address</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact Info</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($residents as $resident)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" name="resident_ids[]" value="{{ $resident->id }}" class="resident-checkbox rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                {{ $resident->first_name }} {{ $resident->middle_name }} {{ $resident->last_name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <div class="text-gray-900 dark:text-white">
                                                <span class="font-medium">Gender:</span> {{ ucfirst($resident->gender) }}
                                            </div>
                                            <div class="text-gray-900 dark:text-white">
                                                <span class="font-medium">Civil Status:</span> {{ format_label($resident->civil_status) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <div class="text-gray-900 dark:text-white">
                                                {{ $resident->birth_date ? $resident->birth_date->format('M d, Y') : 'N/A' }}
                                            </div>
                                            <div class="text-gray-500 dark:text-gray-400 text-xs">
                                                @if($resident->birth_date)
                                                    Age: {{ $resident->birth_date->age }} years
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <div class="text-gray-900 dark:text-white font-medium">{{ $resident->purok->name ?? 'N/A' }}</div>
                                            <div class="text-gray-500 dark:text-gray-400 text-xs">{{ $resident->address }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <div class="text-gray-900 dark:text-white">{{ $resident->email }}</div>
                                            <div class="text-gray-500 dark:text-gray-400 text-xs">{{ $resident->contact_number }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $resident->is_approved ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' }}">
                                                {{ $resident->is_approved ? 'Approved' : 'Pending' }}
                                            </span>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                Registered: {{ $resident->created_at->format('M d, Y') }}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No residents found.
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
    const input = document.getElementById('searchInput');
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
    
    // Update search results text
    const resultsText = document.getElementById('searchResults');
    if (filter) {
        resultsText.textContent = `Showing ${visibleCount} of ${rows.length} residents`;
    } else {
        resultsText.textContent = '';
    }
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    filterTable();
}

function toggleAll(source) {
    const checkboxes = document.querySelectorAll('.resident-checkbox:not([style*="display: none"])');
    checkboxes.forEach(checkbox => {
        checkbox.checked = source.checked;
    });
}

function printAll() {
    // Uncheck all individual checkboxes to print everything
    document.querySelectorAll('.resident-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    document.getElementById('printForm').submit();
}

function printSelected() {
    const selected = document.querySelectorAll('.resident-checkbox:checked');
    if (selected.length === 0) {
        alert('Please select at least one resident to print.');
        return;
    }
    document.getElementById('printForm').submit();
}
</script>
@endsection
