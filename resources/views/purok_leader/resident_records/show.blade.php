@extends('layouts.app')

@section('title', 'Resident Record')

@section('content')
<div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white py-6 rounded-lg shadow-lg mb-6">
    <div class="max-w-5xl mx-auto px-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold">Resident Record</h1>
            <p class="text-purple-100 mt-1">RBI Form B details</p>
        </div>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 space-y-6">
    <div class="flex justify-end">
        <div class="flex items-center gap-2">
            <a href="{{ route('purok_leader.resident_records.pdf', $record) }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">Generate PDF</a>
            @can('update', $record)
                <a href="{{ route('purok_leader.resident_records.edit', $record) }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-purple-600 text-white hover:bg-purple-700">Edit</a>
            @endcan
        </div>
    </div>
    @if (session('success'))
        <div id="saveOverlay" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="mx-4 rounded-xl bg-white/90 shadow-lg px-6 py-4 text-center">
                <div class="text-lg font-semibold text-gray-800">Saved</div>
                <div class="text-gray-600 mt-1">
                    {{ session('success') }}
                    @if($record->status)
                        — Status: {{ ucfirst($record->status) }}
                    @endif
                </div>
                <button type="button" class="mt-3 px-4 py-2 rounded-md bg-purple-600 text-white hover:bg-purple-700" onclick="(function(){var el=document.getElementById('saveOverlay'); if(el){ el.remove(); }})()">OK</button>
            </div>
        </div>
        <script>
            (function(){
                var el = document.getElementById('saveOverlay');
                if(!el) return;
                setTimeout(function(){
                    el.classList.add('opacity-0','pointer-events-none','transition','duration-300');
                    setTimeout(function(){ if(el && el.parentNode){ el.parentNode.removeChild(el); } }, 320);
                }, 2000);
            })();
        </script>
    @endif

    <div class="bg-white rounded-xl shadow border border-gray-200 p-4 md:p-6">
        <h2 class="text-lg font-semibold mb-4">Personal Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
            <div><div class="text-gray-500">PhilSys Card No.</div><div class="font-medium">{{ $record->philsys_card_no ?: '—' }}</div></div>
            <div><div class="text-gray-500">Last Name</div><div class="font-medium">{{ $record->last_name }}</div></div>
            <div><div class="text-gray-500">First Name</div><div class="font-medium">{{ $record->first_name }}</div></div>
            <div><div class="text-gray-500">Middle Name</div><div class="font-medium">{{ $record->middle_name ?: '—' }}</div></div>
            <div><div class="text-gray-500">Suffix</div><div class="font-medium">{{ $record->suffix ?: '—' }}</div></div>
            <div><div class="text-gray-500">Sex</div><div class="font-medium">{{ $record->sex }}</div></div>
            <div><div class="text-gray-500">Birth Date</div><div class="font-medium">{{ optional($record->birth_date)->format('Y-m-d') }}</div></div>
            <div><div class="text-gray-500">Birth Place</div><div class="font-medium">{{ $record->birth_place ?: '—' }}</div></div>
            <div><div class="text-gray-500">Civil Status</div><div class="font-medium">{{ $record->civil_status ?: '—' }}</div></div>
            <div class="md:col-span-2"><div class="text-gray-500">Residence Address</div><div class="font-medium">{{ $record->residence_address }}</div></div>
            <div><div class="text-gray-500">Region</div><div class="font-medium">{{ $record->region ?: '—' }}</div></div>
            <div><div class="text-gray-500">Province</div><div class="font-medium">{{ $record->province ?: '—' }}</div></div>
            <div><div class="text-gray-500">City/Municipality</div><div class="font-medium">{{ $record->city_municipality ?: '—' }}</div></div>
            <div><div class="text-gray-500">Barangay</div><div class="font-medium">{{ $record->barangay ?: '—' }}</div></div>
            <div><div class="text-gray-500">Citizenship</div><div class="font-medium">{{ $record->citizenship ?: '—' }}</div></div>
            <div><div class="text-gray-500">Religion</div><div class="font-medium">{{ $record->religion ?: '—' }}</div></div>
            <div><div class="text-gray-500">Occupation</div><div class="font-medium">{{ $record->occupation ?: '—' }}</div></div>
            <div><div class="text-gray-500">Contact</div><div class="font-medium">{{ $record->contact_number ?: '—' }}</div></div>
            <div><div class="text-gray-500">Email</div><div class="font-medium">{{ $record->email ?: '—' }}</div></div>
            <div><div class="text-gray-500">Date Accomplished</div><div class="font-medium">{{ optional($record->date_accomplished)->format('Y-m-d') ?: '—' }}</div></div>
            <div><div class="text-gray-500">Household Number</div><div class="font-medium">{{ $record->household_number ?: '—' }}</div></div>
            <div><div class="text-gray-500">Attested By</div><div class="font-medium">{{ optional($record->attestedBy)->name ?: '—' }}</div></div>
            <div><div class="text-gray-500">Locked</div><div class="font-medium">{{ $record->is_locked ? 'Yes' : 'No' }}</div></div>
        </div>
    </div>


    <div class="bg-white rounded-xl shadow border border-gray-200 p-4 md:p-6">
        <h2 class="text-lg font-semibold mb-4">Education</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
            <div>
                <div class="text-gray-500">Highest Attainment</div>
                <div class="font-medium">{{ match($record->highest_educ_attainment){'elementary'=>'Elementary','high_school'=>'High School','college'=>'College','post_grad'=>'Post Grad','vocational'=>'Vocational', default => '—'} }}</div>
            </div>
            <div>
                <div class="text-gray-500">Graduate/Undergraduate</div>
                <div class="font-medium">
                    {{ $record->is_graduate ? 'Graduate' : ($record->is_undergraduate ? 'Undergraduate' : '—') }}
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow border border-gray-200 p-4 md:p-6">
        <h2 class="text-lg font-semibold mb-4">Thumbmarks / Signature</h2>
        @php
            $left = $record->left_thumbmark_path ? Storage::disk('public')->url($record->left_thumbmark_path) : null;
            $right = $record->right_thumbmark_path ? Storage::disk('public')->url($record->right_thumbmark_path) : null;
            $sign = $record->signature_path ? Storage::disk('public')->url($record->signature_path) : null;
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <div class="text-gray-500 text-sm mb-1">Left Thumbmark</div>
                @if($left)
                    <img src="{{ $left }}" class="h-32 object-contain border rounded" />
                @else
                    <div class="h-32 border rounded flex items-center justify-center text-gray-400 text-sm">—</div>
                @endif
            </div>
            <div>
                <div class="text-gray-500 text-sm mb-1">Right Thumbmark</div>
                @if($right)
                    <img src="{{ $right }}" class="h-32 object-contain border rounded" />
                @else
                    <div class="h-32 border rounded flex items-center justify-center text-gray-400 text-sm">—</div>
                @endif
            </div>
            <div>
                <div class="text-gray-500 text-sm mb-1">Signature</div>
                @if($sign)
                    <img src="{{ $sign }}" class="h-32 object-contain border rounded" />
                @else
                    <div class="h-32 border rounded flex items-center justify-center text-gray-400 text-sm">—</div>
                @endif
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <a href="{{ route('purok_leader.resident_records.index') }}" class="px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-50">Back</a>
        <form action="{{ route('purok_leader.resident_records.destroy', $record) }}" method="POST" onsubmit="return confirm('Delete this record?')">
            @csrf
            @method('DELETE')
            <button class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">Delete</button>
        </form>
    </div>
</div>
@endsection
