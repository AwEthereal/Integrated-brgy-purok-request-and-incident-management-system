<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-700">
        <thead class="bg-gray-800">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider w-20">
                    Request ID
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider w-48">
                    Resident
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider w-32">
                    Purok
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider w-36">
                    Form Type
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                    Purpose
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider w-28">
                    Requested
                </th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider w-24">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody class="bg-gray-900 divide-y divide-gray-700">
            @forelse($requests as $request)
                <tr class="hover:bg-gray-800">
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-white font-mono">
                        #{{ str_pad($request->id, 2, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-green-900 rounded-full">
                                <span class="text-green-300 font-medium text-sm">{{ substr($request->user->first_name ?? '', 0, 1) }}{{ substr($request->user->last_name ?? '', 0, 1) }}</span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-white">{{ $request->user->full_name ?? $request->user->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-400">{{ $request->user->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-white">
                        {{ $request->purok->name ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-900 text-blue-300">
                            {{ format_label($request->form_type ?? 'N/A') }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-sm text-white">
                        {{ Str::limit($request->purpose ?? 'N/A', 40) }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-400">
                        {{ $request->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
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
                    <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                        <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-white">No pending requests</h3>
                        <p class="mt-1 text-sm text-gray-400">There are currently no requests awaiting your approval.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
