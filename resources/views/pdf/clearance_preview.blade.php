@extends('layouts.app')

@section('title', 'Purok Clearance Preview')

@section('content')
<div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white py-6 rounded-lg shadow-lg mb-6">
    <div class="max-w-6xl mx-auto px-4">
        <h1 class="text-2xl md:text-3xl font-bold">Purok Clearance • Preview & Finalize</h1>
        <p class="text-purple-100 mt-1">Review and make edits before generating the final PDF</p>
    </div>
    
</div>

<div class="max-w-6xl mx-auto px-4 py-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div>
        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-50 p-4 text-green-800">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
            <h2 class="text-lg font-semibold mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                </svg>
                Edit details before finalizing
            </h2>
            <form method="POST" action="{{ !empty($official_mode) ? route('official.clearance.draft', $req) : route('purok_leader.clearance.draft', $req) }}" class="space-y-4" id="draft-form">
                @csrf
                <div>
                    <label class="block text-sm font-medium">Name</label>
                    <input type="text" name="requester_name" value="{{ old('requester_name', $req->requester_name) }}" class="mt-1 w-full rounded border-gray-300" maxlength="150" />
                    @error('requester_name')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Purpose</label>
                    <input type="text" name="purpose" value="{{ old('purpose', $req->purpose) }}" class="mt-1 w-full rounded border-gray-300" maxlength="500" />
                    @error('purpose')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Gender</label>
                    <select name="gender" class="mt-1 w-full rounded border-gray-300">
                        <option value="">--</option>
                        <option value="Male" {{ old('gender', $req->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $req->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Age</label>
                    <input type="number" name="age" value="{{ old('age', $age) }}" class="mt-1 w-full rounded border-gray-300" min="0" max="150" />
                    @error('age')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Street</label>
                    <div class="relative">
                        <input type="text" name="address" value="{{ old('address', $req->address) }}" class="mt-1 w-full rounded border-gray-300 pr-10" maxlength="255" placeholder="Street name only" />
                        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">St.</span>
                    </div>
                    @error('address')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Issue date</label>
                    <input type="date" name="issue_date" value="{{ old('issue_date', $issue_date) }}" class="mt-1 rounded border-gray-300" />
                </div>
            </form>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mt-4">
                <div class="flex items-center gap-2 order-1">
                    <a href="{{ !empty($official_mode) ? route('official.clearance.view', $req) : route('purok_leader.clearance.view', $req) }}" class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300 shadow-sm">
                        Back to View
                    </a>
                    <button type="submit" form="draft-form" class="inline-flex items-center px-4 py-2 rounded-lg bg-purple-600 text-white font-medium hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 shadow">
                        Save Draft
                    </button>
                </div>
                <form method="POST" action="{{ !empty($official_mode) ? route('official.clearance.finalize', $req) : route('purok_leader.clearance.finalize', $req) }}" id="finalize-form" target="_blank" class="order-2 self-end sm:self-auto">
                    @csrf
                    <input type="hidden" name="issue_date" value="{{ $issue_date }}">
                    <input type="hidden" name="age" value="{{ $age }}">
                    <input type="hidden" name="requester_name" value="{{ $req->requester_name }}">
                    <input type="hidden" name="purpose" value="{{ $req->purpose }}">
                    <input type="hidden" name="gender" value="{{ $req->gender }}">
                    <input type="hidden" name="address" value="{{ $req->address }}">
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-green-600 text-white font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 shadow">
                        Generate PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div>
        <!-- Applicant Details panel -->
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 mb-6" id="applicant-details-panel">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">Applicant details</h2>
                <button type="button" id="applicant-details-toggle" data-req="{{ $req->id }}" aria-expanded="false" class="inline-flex items-center gap-2 text-sm px-3 py-1.5 rounded-md border border-gray-300 hover:bg-gray-50">
                    <span>Show details</span>
                    <svg class="w-4 h-4 transition-transform" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.127l3.71-3.896a.75.75 0 111.08 1.04l-4.24 4.455a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div id="applicant-details-content" class="space-y-3 text-sm mt-3 hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-gray-500">Contact number</div>
                        <div class="font-medium">
                            @if(!empty($req->contact_number))
                                <a href="tel:{{ $req->contact_number }}" class="text-blue-600 hover:underline">{{ $req->contact_number }}</a>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </div>
                    </div>
                    @if(!empty($req->contact_number))
                        <button type="button" class="px-2 py-1 text-xs rounded border border-gray-300 hover:bg-gray-50 copy-btn" data-copy="{{ $req->contact_number }}">Copy</button>
                    @endif
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-gray-500">Email</div>
                        <div class="font-medium">
                            @if(!empty($req->email))
                                <a href="mailto:{{ $req->email }}" class="text-blue-600 hover:underline">{{ $req->email }}</a>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </div>
                    </div>
                    @if(!empty($req->email))
                        <button type="button" class="px-2 py-1 text-xs rounded border border-gray-300 hover:bg-gray-50 copy-btn" data-copy="{{ $req->email }}">Copy</button>
                    @endif
                </div>

                @php
                    $front = $req->valid_id_front_path;
                    $back = $req->valid_id_back_path;
                    $frontUrl = $front ? Storage::disk('public')->url($front) : null;
                    $backUrl = $back ? Storage::disk('public')->url($back) : null;
                    $frontExt = $front ? strtolower(pathinfo($front, PATHINFO_EXTENSION)) : null;
                    $backExt = $back ? strtolower(pathinfo($back, PATHINFO_EXTENSION)) : null;
                    $frontIsImg = in_array($frontExt, ['jpg','jpeg','png','gif','webp']);
                    $backIsImg = in_array($backExt, ['jpg','jpeg','png','gif','webp']);
                    $frontIsFace = $front && strpos($front, 'requests/face') !== false;
                @endphp

                <div class="pt-1">
                    <div class="text-gray-500 mb-1">Identity attachment(s)</div>
                    <div class="grid grid-cols-2 gap-3">
                        @if($frontUrl)
                            <div>
                                <div class="text-xs text-gray-600 mb-1">{{ $frontIsFace ? 'Face Photo' : 'Valid ID (Front)' }}</div>
                                @if($frontIsImg)
                                    <img src="{{ $frontUrl }}" alt="Front ID" class="w-full h-24 object-cover rounded cursor-zoom-in lightbox-thumb" data-full="{{ $frontUrl }}">
                                @else
                                    <a href="{{ $frontUrl }}" target="_blank" class="text-blue-600 hover:underline text-sm">Open (PDF)</a>
                                @endif
                            </div>
                        @endif
                        @if($backUrl)
                            <div>
                                <div class="text-xs text-gray-600 mb-1">Valid ID (Back)</div>
                                @if($backIsImg)
                                    <img src="{{ $backUrl }}" alt="Back ID" class="w-full h-24 object-cover rounded cursor-zoom-in lightbox-thumb" data-full="{{ $backUrl }}">
                                @else
                                    <a href="{{ $backUrl }}" target="_blank" class="text-blue-600 hover:underline text-sm">Open (PDF)</a>
                                @endif
                            </div>
                        @endif
                        @if(!$frontUrl && !$backUrl)
                            <div class="col-span-2 text-gray-400 text-sm">No attachments provided</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
            <h2 class="text-lg font-semibold mb-3">Live preview</h2>
            <div class="border p-4">
                <style>
                    @font-face {
                        font-family: 'Consolas';
                        src: url("file://{{ public_path('fonts/consola.ttf') }}") format('truetype');
                        font-weight: 400;
                        font-style: normal;
                    }
                    @font-face {
                        font-family: 'Consolas';
                        src: url("file://{{ public_path('fonts/consolab.ttf') }}") format('truetype');
                        font-weight: 700;
                        font-style: normal;
                    }
                    @font-face {
                        font-family: 'Consolas';
                        src: url("file://{{ public_path('fonts/consolai.ttf') }}") format('truetype');
                        font-weight: 400;
                        font-style: italic;
                    }
                    @font-face {
                        font-family: 'Consolas';
                        src: url("file://{{ public_path('fonts/consolaz.ttf') }}") format('truetype');
                        font-weight: 700;
                        font-style: italic;
                    }
                    .pdf-font { font-family: 'Consolas', monospace; font-size: 11pt; color: #000; }
                    .pdf-header { text-align:center; line-height:1.25; margin-bottom: 14px; }
                    .pdf-title { text-align:center; font-family: 'Consolas', monospace; font-weight:700; text-transform:uppercase;margin: 0px 0 18px;}
                    .pdf-row { margin-bottom:10px; text-align: justify; }
                    .pdf-indent { text-indent:36px; }
                    .pdf-u { text-decoration: underline;font-weight:700; }
                    .pdf-label { font-weight:700; }
                    .pdf-sign { margin-top: 40px; display:flex; justify-content:space-between; align-items:flex-start; }
                    .pdf-sigline { border-top:1px solid #000; width:260px; text-align:center; padding-top:4px; font-size:11pt; }
                    .pdf-approved { text-align:right; }
                    .pdf-small { font-size:11pt; }
                    .pdf-logo { text-align:center; margin-bottom:8px; }
                    .pdf-logo img { height: 70px; }
                    .pdf-center { text-align:left; }
                    .pdf-approved .label { display:block; }
                    .pdf-approved .name { display:inline-block; text-decoration: underline; font-weight:700; }
                    .pdf-approved .title-line { display:block; }
                    .pdf-offset { margin-left:36px; }
                    .highlight-title { background-color: yellow; padding:2px 4px; font-size: 13pt;}
                </style>
                <div class="pdf-font">
                    @php
                        $addrClean = $req->address ?? '';
                        if (is_string($addrClean)) {
                            $addrClean = preg_replace('/\\b(street|st\\.?|st)\\b\\.?/i', '', $addrClean);
                            $addrClean = trim($addrClean);
                            $addrClean = rtrim($addrClean, ' ,.-');
                        }

                        $purokNameRaw = optional($req->purok)->name;
                        $purokNameNoPrefix = is_string($purokNameRaw) ? preg_replace('/^\\s*purok\\s+/i', '', $purokNameRaw) : null;
                        $purokNameNoPrefix = is_string($purokNameNoPrefix) ? trim($purokNameNoPrefix) : null;
                        $purokNameForTitle = $purokNameNoPrefix ?: '________';
                        $purokNameWithPrefix = 'Purok ' . ($purokNameNoPrefix ?: '________');
                    @endphp
                    
                    <div class="pdf-header">
                        <div>Republic of the Philippines</div>
                        <div>Province of Sultan Kudarat</div>
                        <div>Municipality of Isulan</div>
                        <div style="font-weight:700;">BARANGAY GOVERNMENT OF KALAWAG II</div>
                        <div class="pdf-title"><span class="highlight-title">PUROK {{ strtoupper($purokNameForTitle) }} CLEARANCE</span></div>
                    </div>
                    <div class="pdf-row pdf-center"><strong>TO WHOM IT MAY CONCERN:</strong></div>
                    <div class="pdf-row pdf-indent">
                        <strong>THIS IS TO CERTIFY</strong> that <span class="pdf-u">{{ $req->requester_name }}</span>, Filipino, (Gender)
                        <span class="pdf-u">{{ $req->gender ?? '_____' }}</span>, (Age) <span class="pdf-u">{{ isset($age) ? $age : '_____' }}</span> years old and presently residing at
                        <span class="pdf-u">{{ $addrClean ?: '________________' }}</span> St., Kalawag II, Isulan, Sultan Kudarat has appeared before me personally and requested clearance from
                        <span class="pdf-u">PUROK {{ strtoupper($purokNameForTitle) }}</span> with the following findings:
                    </div>
                    <div class="pdf-row pdf-indent"><span class="pdf-u">No derogatory record on file as of this date.</span></div>
                    <div class="pdf-row pdf-indent"><span class="pdf-label">Purpose:</span> <span class="pdf-u">{{ $req->purpose }}</span></div>
                    <div class="pdf-row pdf-offset" style="margin-left:108px;">Issued this <span class="pdf-u">{{ \Carbon\Carbon::parse($issue_date)->format('jS') }}</span> day of <span class="pdf-u">{{ \Carbon\Carbon::parse($issue_date)->format('F') }}</span>, {{ \Carbon\Carbon::parse($issue_date)->format('Y') }}.</div>
                    <div class="pdf-sign">
                        <div class="pdf-sigline pdf-small" style="margin-left: 36px;">Signature of Applicant</div>
                        <div class="pdf-approved" style="text-align:center;">
                            <div class="label">APPROVED BY:</div>
                            <div class="name">{{ strtoupper(optional($req->purokLeader)->name ?? '________________') }}</div>
                            <div class="pdf-small title-line">{{ $purokNameWithPrefix }} President</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PDF Generation Confirmation Modal -->
<div id="pdf-confirm-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" data-dismiss="pdf-confirm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-lg border border-gray-200">
            <div class="px-5 py-4 border-b flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Confirm PDF Generation</h3>
                <button type="button" class="text-gray-500 hover:text-gray-700" data-dismiss="pdf-confirm">✕</button>
            </div>
            <div class="px-5 py-4">
                <p class="text-sm text-gray-700 mb-4">Generating PDF will automatically approve the purok clearance. Make sure the details are correct before generating the PDF.</p>
                <div class="flex items-center justify-end gap-2">
                    <button type="button" class="px-4 py-2 rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50" data-dismiss="pdf-confirm">Cancel</button>
                    <button type="button" id="confirm-pdf-btn" class="px-4 py-2 rounded-md bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">Generate PDF</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function(){
        const finalizeForm = document.getElementById('finalize-form');
        const pdfModal = document.getElementById('pdf-confirm-modal');
        const confirmPdfBtn = document.getElementById('confirm-pdf-btn');
        
        function showPdfModal(){ if (pdfModal) pdfModal.classList.remove('hidden'); }
        function hidePdfModal(){ if (pdfModal) pdfModal.classList.add('hidden'); }
        
        if (finalizeForm) {
            finalizeForm.addEventListener('submit', function(e) {
                e.preventDefault();
                showPdfModal();
            });
        }
        
        if (confirmPdfBtn) {
            confirmPdfBtn.addEventListener('click', function() {
                // Copy form data before submitting
                const issueInput = document.querySelector('input[name="issue_date"]:not(#finalize-form input[name="issue_date"])') || document.querySelector('input[name="issue_date"]');
                const ageInput = document.querySelector('input[name="age"]');
                const hiddenIssue = finalizeForm.querySelector('input[name="issue_date"]');
                const hiddenAge = finalizeForm.querySelector('input[name="age"]');
                if (issueInput && hiddenIssue) hiddenIssue.value = issueInput.value || hiddenIssue.value;
                if (ageInput && hiddenAge) hiddenAge.value = ageInput.value || hiddenAge.value;
                const nameInput = document.querySelector('input[name="requester_name"]');
                const purposeInput = document.querySelector('input[name="purpose"]');
                const genderSelect = document.querySelector('select[name="gender"]');
                const addressInput = document.querySelector('input[name="address"]');
                const hiddenName = finalizeForm.querySelector('input[name="requester_name"]');
                const hiddenPurpose = finalizeForm.querySelector('input[name="purpose"]');
                const hiddenGender = finalizeForm.querySelector('input[name="gender"]');
                const hiddenAddress = finalizeForm.querySelector('input[name="address"]');
                if (hiddenName && nameInput) hiddenName.value = nameInput.value;
                if (hiddenPurpose && purposeInput) hiddenPurpose.value = purposeInput.value;
                if (hiddenGender && genderSelect) hiddenGender.value = genderSelect.value;
                if (hiddenAddress && addressInput) hiddenAddress.value = addressInput.value;
                
                // Submit the form
                finalizeForm.submit();
            });
        }
        
        if (pdfModal) {
            pdfModal.addEventListener('click', function(e) {
                if (e.target.closest('[data-dismiss="pdf-confirm"]')) {
                    hidePdfModal();
                }
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    hidePdfModal();
                }
            });
        }
    })();
</script>
<script>
    (function(){
        // Collapsible Applicant Details
        const panel = document.getElementById('applicant-details-panel');
        const btn = document.getElementById('applicant-details-toggle');
        const content = document.getElementById('applicant-details-content');
        if (panel && btn && content) {
            const key = 'pl_applicant_details_open_' + (btn.getAttribute('data-req') || '0');
            function setOpen(open){
                if (open) {
                    content.classList.remove('hidden');
                    btn.setAttribute('aria-expanded', 'true');
                    const span = btn.querySelector('span'); if (span) span.textContent = 'Hide details';
                    const icon = btn.querySelector('svg'); if (icon) icon.style.transform = 'rotate(180deg)';
                    try { localStorage.setItem(key, '1'); } catch(e){}
                } else {
                    content.classList.add('hidden');
                    btn.setAttribute('aria-expanded', 'false');
                    const span = btn.querySelector('span'); if (span) span.textContent = 'Show details';
                    const icon = btn.querySelector('svg'); if (icon) icon.style.transform = 'rotate(0deg)';
                    try { localStorage.setItem(key, '0'); } catch(e){}
                }
            }
            // Default collapsed, restore preference if any
            try {
                const saved = localStorage.getItem(key);
                setOpen(saved === '1');
            } catch(e){ setOpen(false); }
            btn.addEventListener('click', function(){
                const isOpen = btn.getAttribute('aria-expanded') === 'true';
                setOpen(!isOpen);
            });
        }

        // Copy buttons
        document.querySelectorAll('.copy-btn').forEach(function(btn){
            btn.addEventListener('click', function(){
                const val = btn.getAttribute('data-copy') || '';
                if (!val) return;
                navigator.clipboard && navigator.clipboard.writeText(val).then(function(){
                    btn.textContent = 'Copied';
                    setTimeout(function(){ btn.textContent = 'Copy'; }, 1200);
                }).catch(function(){
                    // fallback
                });
            });
        });

        // Simple image lightbox
        const overlay = document.createElement('div');
        overlay.className = 'fixed inset-0 bg-black/80 z-50 hidden flex items-center justify-center p-4';
        overlay.innerHTML = '<img id="lb_img" class="max-w-full max-h-full rounded shadow" alt="Attachment">';
        document.body.appendChild(overlay);
        const lbImg = overlay.querySelector('#lb_img');
        function openLightbox(src){ if (!src) return; lbImg.src = src; overlay.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); }
        function closeLightbox(){ overlay.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); lbImg.src = ''; }
        overlay.addEventListener('click', function(e){ if (e.target === overlay) closeLightbox(); });
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeLightbox(); });
        document.querySelectorAll('.lightbox-thumb').forEach(function(img){
            img.addEventListener('click', function(){ openLightbox(img.getAttribute('data-full')); });
        });
    })();
</script>
@endpush
