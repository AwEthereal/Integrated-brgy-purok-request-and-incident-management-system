@extends('layouts.app')

@section('title', 'Manage Residents')

@section('content')
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white py-8 px-4 sm:px-6 lg:px-8 rounded-lg shadow-lg mb-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-6 md:mb-0">
                    <h1 class="text-3xl md:text-4xl font-bold mb-2 flex items-center">
                        <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Residents in {{ $purokName }}
                    </h1>
                    <p class="text-purple-100 mt-2">Managing and monitoring resident information</p>
                </div>
                <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold">{{ $residents->total() }}</p>
                    <p class="text-sm">Total Residents</p>
                </div>
            </div>
        </div>
    </div>

<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div class="flex items-center gap-2">
            <a href="{{ route('purok_leader.dashboard') }}" class="inline-flex items-center text-purple-600 hover:text-purple-800 font-medium">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Dashboard
            </a>
        </div>
        @if(in_array(auth()->user()->role, ['barangay_captain', 'barangay_kagawad', 'secretary']))
        <div class="flex flex-col sm:flex-row gap-2">
            <form id="printForm" action="{{ route('reports.specific.residents') }}" method="POST" target="_blank" class="inline-flex items-center">
                @csrf
                <input type="hidden" name="resident_ids" id="selectedResidents">
                <button type="button" id="printSelectedBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-5 rounded-lg flex items-center gap-2 transition-all shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                    </svg>
                    Print Selected
                </button>
            </form>
            <a href="{{ route('reports.residents') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-5 rounded-lg flex items-center gap-2 transition-all shadow-md hover:shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                </svg>
                Print All Residents
            </a>
        </div>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-5">
            <h2 class="text-xl font-semibold text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Resident List
                <span class="ml-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white bg-opacity-20 text-white">
                    {{ $residents->total() }} {{ Str::plural('resident', $residents->total()) }}
                </span>
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        @if(in_array(auth()->user()->role, ['barangay_captain', 'barangay_kagawad', 'secretary']))
                        <th scope="col" class="w-10 px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </th>
                        @endif
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th scope="col" class="w-20 px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase">Requests</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th scope="col" class="w-24 px-2 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($residents as $resident)
                        <tr class="hover:bg-gray-50" data-resident-id="{{ $resident->id }}">
                            @if(in_array(auth()->user()->role, ['barangay_captain', 'barangay_kagawad', 'secretary']))
                            <td class="px-2 py-3 text-center">
                                <input type="checkbox" class="resident-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" value="{{ $resident->id }}">
                            </td>
                            @endif
                            <td class="px-3 py-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center shadow-sm">
                                        <span class="text-white font-semibold text-xs">{{ substr($resident->first_name, 0, 1) }}{{ substr($resident->last_name, 0, 1) }}</span>
                                    </div>
                                    <div class="ml-2 min-w-0">
                                        <a href="{{ route('purok_leader.residents.show', $resident->id) }}" class="text-sm font-semibold text-gray-900 hover:text-purple-600 transition-colors block truncate">
                                            {{ $resident->full_name }}
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-sm text-gray-700">
                                <div class="flex items-center">
                                    <svg class="w-3 h-3 mr-1 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span class="truncate">{{ $resident->contact_number ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-sm text-gray-700">
                                <div class="flex items-center min-w-0">
                                    <svg class="w-3 h-3 mr-1 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="truncate">{{ $resident->email }}</span>
                                </div>
                            </td>
                            <td class="px-2 py-3 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold rounded-full bg-blue-100 text-blue-800">
                                    {{ $resident->requests_count }}
                                </span>
                            </td>
                            <td class="px-3 py-3">
                                @if($resident->is_approved)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @elseif($resident->rejected_at)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Rejected
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-2 py-3">
                                <div class="flex items-center justify-end space-x-2">
                                    @if(!$resident->is_approved && !$resident->rejected_at)
                                        <form action="{{ route('purok_leader.residents.approve', $resident) }}" method="POST" class="inline-flex items-center" onsubmit="return confirm('Are you sure you want to approve this resident?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="p-2 text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors shadow-sm" title="Approve">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                        </form>
                                        <a href="{{ route('purok_leader.residents.reject-form', $resident) }}" class="p-2 text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors shadow-sm" title="Reject">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </a>
                                    @elseif($resident->rejected_at)
                                        <form action="{{ route('purok_leader.residents.approve', $resident) }}" method="POST" class="inline-flex items-center" onsubmit="return confirm('Are you sure you want to approve this resident?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="p-2 text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors shadow-sm" title="Approve">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-500">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Approved
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-500">No residents found</p>
                                    <p class="text-sm text-gray-400 mt-1">Residents will appear here once they register</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const residentCheckboxes = document.querySelectorAll('.resident-checkbox');
        const printSelectedBtn = document.getElementById('printSelectedBtn');
        const selectedResidentsInput = document.getElementById('selectedResidents');
        const printForm = document.getElementById('printForm');

        // Toggle select all checkboxes
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                residentCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updatePrintButton();
            });
        }

        // Update select all checkbox when individual checkboxes change
        residentCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectAllCheckbox();
                updatePrintButton();
            });
        });

        // Update the select all checkbox based on individual checkboxes
        function updateSelectAllCheckbox() {
            if (selectAllCheckbox) {
                const allChecked = Array.from(residentCheckboxes).every(checkbox => checkbox.checked);
                const someChecked = Array.from(residentCheckboxes).some(checkbox => checkbox.checked);
                
                if (allChecked) {
                    selectAllCheckbox.checked = true;
                    selectAllCheckbox.indeterminate = false;
                } else if (someChecked) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = true;
                } else {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                }
            }
        }

        // Update print button state based on selection
        function updatePrintButton() {
            if (!printSelectedBtn) return;
            
            const selectedCount = document.querySelectorAll('.resident-checkbox:checked').length;
            printSelectedBtn.disabled = selectedCount === 0;
            
            if (selectedCount > 0) {
                printSelectedBtn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                    </svg>
                    Print Selected (${selectedCount})
                `;
            } else {
                printSelectedBtn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                    </svg>
                    Print Selected
                `;
            }
        }

        // Handle print form submission
        if (printForm) {
            printForm.addEventListener('submit', function(e) {
                const selectedCheckboxes = document.querySelectorAll('.resident-checkbox:checked');
                const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
                
                if (selectedIds.length === 0) {
                    e.preventDefault();
                    return false;
                }
                
                selectedResidentsInput.value = JSON.stringify(selectedIds);
                return true;
            });
        }

        // Initialize the print button state
        updatePrintButton();
    });
</script>
@endpush

@endsection
