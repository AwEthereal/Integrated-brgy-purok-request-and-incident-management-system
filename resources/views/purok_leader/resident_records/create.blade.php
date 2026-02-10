@extends('layouts.app')

@section('title', 'Add Resident Record')

@section('content')
<div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white py-6 rounded-lg shadow-lg mb-6">
    <div class="max-w-5xl mx-auto px-4">
        <h1 class="text-2xl md:text-3xl font-bold">Add Resident Record (RBI Form B)</h1>
        <p class="text-purple-100 mt-1">Fill in the details below. Fields marked with * are required.</p>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4">
    @if (session('success'))
        <div id="saveOverlay" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="mx-4 rounded-xl bg-white/90 shadow-lg px-6 py-4 text-center">
                <div class="text-lg font-semibold text-gray-800">Saved</div>
                <div class="text-gray-600 mt-1">
                    {{ session('success') }}
                    @if(session('saved_status'))
                        — Status: {{ ucfirst(session('saved_status')) }}
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
    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 p-4 text-red-800">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('purok_leader.resident_records.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <input type="hidden" name="redirect_to" value="{{ old('redirect_to', request('redirect_to', url()->previous())) }}" />
        @if(!empty($prefillUser))
            <input type="hidden" name="user_id" value="{{ old('user_id', $prefillUser->id) }}" />
        @endif

        <div class="bg-white rounded-xl shadow border border-gray-200 p-4 md:p-6">
            <h2 class="text-lg font-semibold mb-4">Personal Information</h2>
            @if(isset($puroks) && $puroks)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-3">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Purok *</label>
                    <select name="purok_id" class="w-full rounded-md border border-gray-300 px-3 py-2" required>
                        <option value="">Select Purok</option>
                        @foreach($puroks as $p)
                            <option value="{{ $p->id }}" @selected(old('purok_id', $prefillUser->purok_id ?? null)==$p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif
            @if(!empty($canManageSecFields))
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Household Number</label>
                    <input type="text" name="household_number" value="{{ old('household_number') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Attested By (Secretary)</label>
                    <select name="attested_by_user_id" class="w-full rounded-md border border-gray-300 px-3 py-2">
                        <option value="">--</option>
                        @foreach($secretaries as $s)
                            <option value="{{ $s->id }}" @selected(old('attested_by_user_id')==$s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2 flex items-end">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="is_locked" value="1" @checked(old('is_locked'))>
                        <span>Lock record from further edits by leaders</span>
                    </label>
                </div>
            </div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">PhilSys Card No.</label>
                    <input type="text" name="philsys_card_no" value="{{ old('philsys_card_no') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-3">
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium mb-1">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $prefillUser->last_name ?? '') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" required />
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium mb-1">Suffix</label>
                    <input type="text" name="suffix" value="{{ old('suffix') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" placeholder="Jr., Sr., III" />
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium mb-1">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $prefillUser->first_name ?? '') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" required />
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium mb-1">Middle Name</label>
                    <input type="text" name="middle_name" value="{{ old('middle_name', $prefillUser->middle_name ?? '') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Birth Date *</label>
                    @php($prefDob = ($prefillUser->birth_date ?? ($prefillUser->date_of_birth ?? null)))
                    <input type="date" name="birth_date" value="{{ old('birth_date', $prefDob ? \Carbon\Carbon::parse($prefDob)->format('Y-m-d') : '') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" required />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Birth Place</label>
                    <input type="text" name="birth_place" value="{{ old('birth_place') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Sex *</label>
                    @php($prefSex = $prefillUser->sex ?? ($prefillUser->gender ?? null))
                    <select name="sex" class="w-full rounded-md border border-gray-300 px-3 py-2" required>
                        <option value="">Select</option>
                        <option value="Male" @selected(old('sex', $prefSex)==='Male')>Male</option>
                        <option value="Female" @selected(old('sex', $prefSex)==='Female')>Female</option>
                        <option value="Other" @selected(old('sex', $prefSex)==='Other')>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Civil Status</label>
                    <select name="civil_status" class="w-full rounded-md border border-gray-300 px-3 py-2">
                        <option value="">--</option>
                        @foreach (['Single','Married','Widowed','Separated'] as $cs)
                            <option value="{{ $cs }}" @selected(old('civil_status')===$cs)>{{ $cs }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Religion</label>
                    <input list="religion_list" name="religion" value="{{ old('religion') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                    <datalist id="religion_list">
                        @foreach ($religions as $rel)
                            <option value="{{ $rel }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Citizenship</label>
                    <input type="text" name="citizenship" value="{{ old('citizenship') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Contact Number</label>
                    <input type="text" name="contact_number" value="{{ old('contact_number', $prefillUser->contact_number ?? '') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Residence Address *</label>
                    <input type="text" name="residence_address" value="{{ old('residence_address') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" required />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">E-mail Address</label>
                    <input type="email" name="email" value="{{ old('email', $prefillUser->email ?? '') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Region</label>
                    <input list="region_list" name="region" value="{{ old('region') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" placeholder="e.g., Region XII" />
                    <datalist id="region_list">
                        <option value="Region 12"></option>
                    </datalist>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Province</label>
                    <input type="text" name="province" value="{{ old('province') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" placeholder="e.g., Sultan Kudarat" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">City/Municipality</label>
                    <input list="citymun_list" name="city_municipality" value="{{ old('city_municipality') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" placeholder="e.g., Isulan" />
                    <datalist id="citymun_list">
                        <option value="Isulan"></option>
                    </datalist>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Barangay</label>
                    <input type="text" name="barangay" value="{{ old('barangay') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" placeholder="e.g., Kalawag II" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Profession / Occupation</label>
                    <input type="text" name="occupation" value="{{ old('occupation') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Date Accomplished</label>
                    <input type="date" name="date_accomplished" value="{{ old('date_accomplished') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" />
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
                            <option value="{{ $k }}" @selected(old('highest_educ_attainment')===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Please specify:</label>
                    <div class="flex items-center gap-6 mt-2">
                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_graduate" value="1" @checked(old('is_graduate'))> <span>Graduate</span></label>
                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_undergraduate" value="1" @checked(old('is_undergraduate'))> <span>Undergraduate</span></label>
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
                    <input type="file" name="left_thumbmark" accept="image/*" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Right Thumbmark</label>
                    <input type="file" name="right_thumbmark" accept="image/*" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Signature</label>
                    <input type="file" name="signature" accept="image/*" class="w-full rounded-md border border-gray-300 px-3 py-2" />
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow border border-gray-200 p-4 md:p-6">
            <h2 class="text-lg font-semibold mb-3">Record Status</h2>
            <div class="flex items-center gap-6">
                <label class="inline-flex items-center gap-2"><input type="radio" name="status" value="active" @checked(old('status','active')==='active')> <span>Active</span></label>
                <label class="inline-flex items-center gap-2"><input type="radio" name="status" value="draft" @checked(old('status')==='draft')> <span>Draft</span></label>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ request('redirect_to', url()->previous()) ?: route('purok_leader.resident_records.index') }}" class="px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-5 py-2 rounded-md bg-purple-600 text-white hover:bg-purple-700">Save</button>
        </div>
    </form>
</div>
@endsection
