<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-700">
        <thead class="bg-gray-800">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider" style="width: 4%;">
                    <input type="checkbox" id="selectAllHistory" onchange="toggleAllHistory(this)" class="rounded border-gray-500 text-green-600 shadow-sm focus:ring-green-500">
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider" style="width: 8%;">
                    Request ID
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider" style="width: 18%;">
                    Resident
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider" style="width: 20%;">
                    Purpose
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider" style="width: 13%;">
                    Purok
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider" style="width: 15%;">
                    Status
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider" style="width: 13%;">
                    Date
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider" style="width: 8%;">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody class="bg-gray-900 divide-y divide-gray-700">
            @forelse($requests as $request)
                @php($effectivePurokId = $request->purok_id ?? optional($request->user)->purok_id)
                @php($effectivePurokName = $request->purok->name ?? optional(optional($request->user)->purok)->name)
                @php($residentName = $request->requester_name ?? (optional($request->user)->full_name ?: optional($request->user)->name))
                <tr class="hover:bg-gray-800 approval-row" data-purok-id="{{ $effectivePurokId }}" data-status="{{ $request->status }}">
                    <td class="px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                        <input type="checkbox" class="history-checkbox rounded border-gray-500 text-green-600 shadow-sm focus:ring-green-500" value="{{ $request->id }}">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white font-mono">
                        #{{ str_pad($request->id, 2, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-white">{{ $residentName ?: 'N/A' }}</div>
                        <div class="text-xs text-gray-400">{{ $request->email ?? optional($request->user)->email ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-white">
                        {{ Str::limit($request->purpose ?? 'N/A', 50) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                        {{ $effectivePurokName ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($request->status === 'completed')
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-900 text-green-300">
                                Completed
                            </span>
                        @elseif($request->status === 'purok_approved')
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-900 text-blue-300">
                                Purok Approved
                            </span>
                        @elseif($request->status === 'rejected')
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-900 text-red-300">
                                Rejected
                            </span>
                        @else
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-800 text-gray-300">
                                {{ format_label($request->status) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                        {{ $request->updated_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('reports.preview.purok-clearance', ['ids' => $request->id]) }}" target="_blank" class="text-blue-400 hover:text-blue-300 mr-3" title="Generate PDF">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h6m-6 0H7a2 2 0 01-2-2v-4a2 2 0 012-2h10a2 2 0 012 2v4a2 2 0 01-2 2h-2" />
                            </svg>
                        </a>
                        <a href="{{ route('barangay.approvals.show', $request->id) }}" class="text-green-400 hover:text-green-300" title="View Details">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                        <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-white">No request history found</h3>
                        <p class="mt-1 text-sm text-gray-400">No completed requests match your search criteria.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
