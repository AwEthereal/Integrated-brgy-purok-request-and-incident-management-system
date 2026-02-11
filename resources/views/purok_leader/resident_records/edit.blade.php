@extends('layouts.app')

@section('title', 'Edit Resident Record')

@section('content')
<div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white py-6 rounded-lg shadow-lg mb-6">
    <div class="max-w-5xl mx-auto px-4">
        <h1 class="text-2xl md:text-3xl font-bold">Edit Resident Record (RBI Form B)</h1>
        <p class="text-purple-100 mt-1">Update the details below.</p>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4">
    @if (session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-4 text-green-800">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 p-4 text-red-800">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('purok_leader.resident_records.update', $record) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        <input type="hidden" name="redirect_to" value="{{ old('redirect_to', request('redirect_to', url()->previous())) }}" />

        <div class="bg-white rounded-xl shadow border border-gray-200 p-4 md:p-6">
            <h2 class="text-lg font-semibold mb-4">Personal Information</h2>
            @if(isset($puroks) && $puroks)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-3">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Purok *</label>
                    <select name="purok_id" class="w-full rounded-md border border-gray-300 px-3 py-2" required>
                        <option value="">Select Purok</option>
                        @foreach($puroks as $p)
                            <option value="{{ $p->id }}" @selected(old('purok_id', $record->purok_id)==$p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif
            @if(!empty($canManageSecFields))
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Household Number</label>
                    <input type="text" name="household_number" value="{{ old('household_number', $record->household_number) }}" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Attested By (Secretary)</label>
                    <select name="attested_by_user_id" class="w-full rounded-md border border-gray-300 px-3 py-2">
                        <option value="">--</option>
                        @foreach($secretaries as $s)
                            <option value="{{ $s->id }}" @selected(old('attested_by_user_id', $record->attested_by_user_id)==$s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2 flex items-end">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="is_locked" value="1" @checked(old('is_locked', $record->is_locked))>
                        <span>Lock record from further edits by leaders</span>
                    </label>
                </div>
            </div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">PhilSys Card No.</label>
                    <input type="text" name="philsys_card_no" value="{{ old('philsys_card_no', $record->philsys_card_no) }}" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-3">
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium mb-1">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $record->last_name) }}" class="w-full rounded-md border border-gray-300 px-3 py-2" required />
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium mb-1">Suffix</label>
                    <input type="text" name="suffix" value="{{ old('suffix', $record->suffix) }}" class="w-full rounded-md border border-gray-300 px-3 py-2" placeholder="Jr., Sr., III" />
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium mb-1">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $record->first_name) }}" class="w-full rounded-md border border-gray-300 px-3 py-2" required />
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium mb-1">Middle Name</label>
                    <input type="text" name="middle_name" value="{{ old('middle_name', $record->middle_name) }}" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Birth Date *</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', optional($record->birth_date)->format('Y-m-d')) }}" class="w-full rounded-md border border-gray-300 px-3 py-2" required />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Birth Place</label>
                    <input type="text" name="birth_place" value="{{ old('birth_place', $record->birth_place) }}" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Sex *</label>
                    <select name="sex" class="w-full rounded-md border border-gray-300 px-3 py-2" required>
                        <option value="">Select</option>
                        @foreach (['Male','Female','Other'] as $sx)
                            <option value="{{ $sx }}" @selected(old('sex', $record->sex)===$sx)>{{ $sx }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Civil Status</label>
                    <select name="civil_status" class="w-full rounded-md border border-gray-300 px-3 py-2">
                        <option value="">--</option>
                        @foreach (['Single','Married','Widowed','Separated'] as $cs)
                            <option value="{{ $cs }}" @selected(old('civil_status', $record->civil_status)===$cs)>{{ $cs }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Religion</label>
                    <input list="religion_list" name="religion" value="{{ old('religion', $record->religion) }}" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                    <datalist id="religion_list">
                        @foreach ($religions as $rel)
                            <option value="{{ $rel }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Citizenship</label>
                    <input type="text" name="citizenship" value="{{ old('citizenship', $record->citizenship) }}" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Contact Number</label>
                    <input type="text" name="contact_number" value="{{ old('contact_number', $record->contact_number) }}" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Residence Address *</label>
                    <input type="text" name="residence_address" value="{{ old('residence_address', $record->residence_address) }}" class="w-full rounded-md border border-gray-300 px-3 py-2" required />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">E-mail Address</label>
                    <input type="email" name="email" value="{{ old('email', $record->email) }}" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Region</label>
                    <input list="region_list" name="region" value="{{ old('region', $record->region) }}" class="w-full rounded-md border border-gray-300 px-3 py-2" placeholder="e.g., Region XII" />
                    <datalist id="region_list">
                        <option value="Region 12"></option>
                    </datalist>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Province</label>
                    <input type="text" name="province" value="{{ old('province', $record->province) }}" class="w-full rounded-md border border-gray-300 px-3 py-2" placeholder="e.g., Sultan Kudarat" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">City/Municipality</label>
                    <input list="citymun_list" name="city_municipality" value="{{ old('city_municipality', $record->city_municipality) }}" class="w-full rounded-md border border-gray-300 px-3 py-2" placeholder="e.g., Isulan" />
                    <datalist id="citymun_list">
                        <option value="Isulan"></option>
                    </datalist>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Barangay</label>
                    <input type="text" name="barangay" value="{{ old('barangay', $record->barangay) }}" class="w-full rounded-md border border-gray-300 px-3 py-2" placeholder="e.g., Kalawag II" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Profession / Occupation</label>
                    <input type="text" name="occupation" value="{{ old('occupation', $record->occupation) }}" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Date Accomplished</label>
                    <input type="date" name="date_accomplished" value="{{ old('date_accomplished', optional($record->date_accomplished)->format('Y-m-d')) }}" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow border border-gray-200 p-4 md:p-6">
            <h2 class="text-lg font-semibold mb-4">Education</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Highest Educational Attainment</label>
                    <select name="highest_educ_attainment" class="w-full rounded-md border border-gray-300 px-3 py-2">
                        <option value="">--</option>
                        @foreach (['elementary'=>'Elementary','high_school'=>'High School','college'=>'College','post_grad'=>'Post Grad','vocational'=>'Vocational'] as $k=>$v)
                            <option value="{{ $k }}" @selected(old('highest_educ_attainment', $record->highest_educ_attainment)===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Please specify:</label>
                    <div class="flex items-center gap-6 mt-2">
                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_graduate" value="1" @checked(old('is_graduate', $record->is_graduate))> <span>Graduate</span></label>
                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_undergraduate" value="1" @checked(old('is_undergraduate', $record->is_undergraduate))> <span>Undergraduate</span></label>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var grad = document.querySelector('input[name="is_graduate"]');
                        var under = document.querySelector('input[name="is_undergraduate"]');
                        if (grad && under) {
                            function enforceExclusive(e) {
                                if (e.target.checked) {
                                    if (e.target === grad) { under.checked = false; }
                                    if (e.target === under) { grad.checked = false; }
                                }
                            }
                            grad.addEventListener('change', enforceExclusive);
                            under.addEventListener('change', enforceExclusive);
                        }
                    });
                </script>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow border border-gray-200 p-4 md:p-6">
            <h2 class="text-lg font-semibold mb-4">Thumbmarks / Signature (optional)</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Left Thumbmark</label>
                    <input type="file" name="left_thumbmark" accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Right Thumbmark</label>
                    <input type="file" name="right_thumbmark" accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Signature</label>
                    <input type="file" name="signature" accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
            </div>
            @php
                $left = $record->left_thumbmark_path ? Storage::disk('public')->url($record->left_thumbmark_path) : null;
                $right = $record->right_thumbmark_path ? Storage::disk('public')->url($record->right_thumbmark_path) : null;
                $sign = $record->signature_path ? Storage::disk('public')->url($record->signature_path) : null;
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
                <div>
                    <label class="block text-xs text-gray-500">Current Left</label>
                    @if($left)
                        <img src="{{ $left }}" class="h-24 object-contain border rounded" />
                    @else
                        <div class="h-24 border rounded flex items-center justify-center text-gray-400 text-sm">None</div>
                    @endif
                </div>
                <div>
                    <label class="block text-xs text-gray-500">Current Right</label>
                    @if($right)
                        <img src="{{ $right }}" class="h-24 object-contain border rounded" />
                    @else
                        <div class="h-24 border rounded flex items-center justify-center text-gray-400 text-sm">None</div>
                    @endif
                </div>
                <div>
                    <label class="block text-xs text-gray-500">Current Signature</label>
                    @if($sign)
                        <img src="{{ $sign }}" class="h-24 object-contain border rounded" />
                    @else
                        <div class="h-24 border rounded flex items-center justify-center text-gray-400 text-sm">None</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow border border-gray-200 p-4 md:p-6">
            <h2 class="text-lg font-semibold mb-3">Record Status</h2>
            <div class="flex items-center gap-6">
                <label class="inline-flex items-center gap-2"><input type="radio" name="status" value="active" @checked(old('status', $record->status)==='active')> <span>Active</span></label>
                <label class="inline-flex items-center gap-2"><input type="radio" name="status" value="draft" @checked(old('status', $record->status)==='draft')> <span>Draft</span></label>
                <label class="inline-flex items-center gap-2"><input type="radio" name="status" value="archived" @checked(old('status', $record->status)==='archived')> <span>Archived</span></label>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ request('redirect_to', url()->previous()) ?: route('purok_leader.resident_records.show', $record) }}" class="px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-5 py-2 rounded-md bg-purple-600 text-white hover:bg-purple-700">Save Changes</button>
        </div>
    </form>
</div>
@endsection
