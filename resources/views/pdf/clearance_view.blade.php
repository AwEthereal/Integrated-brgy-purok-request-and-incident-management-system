@extends('layouts.app')

@section('title', 'Purok Clearance – View')

@section('content')
<div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white py-6 mb-6">
    <div class="max-w-6xl mx-auto px-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            @php
                $role = auth()->user()->role ?? '';
                $isPurokLeader = $role === 'purok_leader';
                $roleLabelMap = [
                    'barangay_captain' => 'Brgy. Capt.',
                    'secretary' => 'Secretary',
                    'barangay_kagawad' => 'Kagawad',
                    'barangay_clerk' => 'Barangay Clerk',
                    'sk_chairman' => 'SK Chairman',
                    'admin' => 'Admin',
                ];
                $roleLabel = $roleLabelMap[$role] ?? ucfirst(str_replace('_', ' ', $role));

                $canApprovePurok = auth()->user() ? auth()->user()->can('approvePurok', $req) : false;
                $canApproveBarangay = auth()->user() ? auth()->user()->can('approveBarangay', $req) : false;
                $canReject = auth()->user() ? auth()->user()->can('reject', $req) : false;
                $approveRoute = $canApproveBarangay ? 'requests.approve-barangay' : 'requests.approve-purok';
                $showDecisionButtons = ($canApprovePurok || $canApproveBarangay || $canReject);
            @endphp
            <h1 class="text-2xl md:text-3xl font-bold">{{ $isPurokLeader ? 'Purok Clearance • View' : ('Purok Clearance • View (' . $roleLabel . ')') }}</h1>
            <p class="text-purple-100">{{ $showDecisionButtons ? 'Review the full template. Approve or Reject if needed.' : 'Review the full template.' }}</p>
            @php
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'purok_approved' => 'bg-blue-100 text-blue-800',
                    'barangay_approved' => 'bg-green-100 text-green-800',
                    'completed' => 'bg-purple-100 text-purple-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    'cancelled' => 'bg-gray-100 text-gray-800'
                ];
                $statusLabelMap = [
                    'pending' => 'Pending',
                    'purok_approved' => 'Purok Approved',
                    'barangay_approved' => 'Barangay Approved',
                    'completed' => 'Completed',
                    'rejected' => 'Rejected',
                    'cancelled' => 'Cancelled'
                ];
                $badgeClass = $statusColors[$req->status] ?? 'bg-gray-100 text-gray-800';
                $badgeLabel = $statusLabelMap[$req->status] ?? ucfirst(str_replace('_',' ',$req->status));
            @endphp
            <div class="mt-2">
                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
                    {{ $badgeLabel }}
                </span>
            </div>
        </div>

<!-- Accept Request Modal -->
<div id="accept-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" data-dismiss="accept"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-lg border border-gray-200">
            <div class="px-5 py-4 border-b flex items-center justify-between">
                <h3 class="text-base font-semibold" style="color: black">Confirm approval</h3>
                <button type="button" class="text-gray-500 hover:text-gray-700" data-dismiss="accept">✕</button>
            </div>
            <form method="POST" action="{{ route($approveRoute, $req) }}" class="px-5 py-4 space-y-3">
                @csrf
                <p class="text-sm text-gray-700">Are you sure you want to approve this clearance request?</p>
                <div class="mt-2 flex items-center justify-end gap-2">
                    <button type="button" class="px-4 py-2 rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50" data-dismiss="accept">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-md bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>
    </div>
</div>

<div class="max-w-6xl mx-auto px-4 grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div id="left-col" class="lg:col-span-2">
        <!-- Small toolbar above the template -->
        <div class="flex items-center justify-end gap-2 mb-2">
            <button type="button" id="show-details-btn" class="hidden text-sm px-2 py-1 rounded border border-gray-300 hover:bg-gray-50">Show applicant details</button>
            <a href="{{ $isPurokLeader ? route('purok_leader.clearance.preview', $req) : route('official.clearance.preview', $req) }}" class="text-sm inline-flex items-center px-2 py-1 rounded border border-gray-300 bg-white hover:bg-gray-50">Edit PDF</a>
            <form method="POST" action="{{ $isPurokLeader ? route('purok_leader.clearance.finalize', $req) : route('official.clearance.finalize', $req) }}" target="_blank" class="inline-flex" id="pdf-form">
                @csrf
                <input type="hidden" name="issue_date" value="{{ $issue_date }}">
                <input type="hidden" name="age" value="{{ $age }}">
                <input type="hidden" name="requester_name" value="{{ $req->requester_name }}">
                <input type="hidden" name="purpose" value="{{ $req->purpose }}">
                <input type="hidden" name="gender" value="{{ $req->gender }}">
                <input type="hidden" name="address" value="{{ $req->address }}">
                <button type="submit" class="text-sm inline-flex items-center px-2 py-1 rounded bg-green-600 text-white hover:bg-green-700">Generate PDF</button>
            </form>
        </div>
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
            <h2 class="text-lg font-semibold mb-3">Clearance Template</h2>
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
                    .pdf-title { text-align:center; font-family: 'Consolas', monospace; font-weight:700; text-transform:uppercase; margin: 0px 0 18px; }
                    .pdf-row { margin-bottom:10px; text-align: justify; }
                    .pdf-indent { text-indent:36px; }
                    .pdf-u { text-decoration: underline; font-weight:700; }
                    .pdf-label { font-weight:700;}
                    .pdf-sign { margin-top: 40px; display:flex; justify-content:space-between; align-items:flex-start; }
                    .pdf-sigline { border-top:1px solid #000; width:260px; text-align:center; padding-top:4px; font-size:11pt; }
                    .pdf-approved { text-align:left;}
                    .pdf-small { font-size:11pt; text-align: center;}
                    .highlight-title { background-color: yellow; padding:2px 4px; font-size: 13pt;}
                </style>
                <div class="pdf-font" style="margin-left:42px; margin-right:42px;">
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
                    <div class="pdf-row" style="text-align:left;"><strong>TO WHOM IT MAY CONCERN:</strong></div>
                    <div class="pdf-row pdf-indent">
                        <strong>THIS IS TO CERTIFY</strong> that <span class="pdf-u">{{ $req->requester_name }}</span>, Filipino, (Gender)
                        <span class="pdf-u">{{ $req->gender ?? '' }}</span>, (Age)
                        <span class="pdf-u">{{ isset($age) ? $age : '' }}</span> years old and presently residing at
                        <span class="pdf-u">{{ $addrClean ?: '' }}</span> St., Kalawag II, Isulan, Sultan Kudarat has appeared before me personally and requested clearance from
                        <span class="pdf-u">PUROK {{ strtoupper($purokNameForTitle) }}</span> with the following findings:
                    </div>
                    <div class="pdf-row pdf-indent"><span class="pdf-u" style="font-weight:700;">No derogatory record on file as of this date.</span></div>
                    <div class="pdf-row pdf-indent"><span class="pdf-label">Purpose:</span> <span class="pdf-u">{{ $req->purpose }}</span></div>
                    <div class="pdf-row" style="margin-left:108px;">Issued this <span class="pdf-u">{{ \Carbon\Carbon::parse($issue_date)->format('jS') }}</span> day of <span class="pdf-u">{{ \Carbon\Carbon::parse($issue_date)->format('F') }}</span>, {{ \Carbon\Carbon::parse($issue_date)->format('Y') }}.</div>
                    <div class="pdf-sign">
                        <div class="pdf-sigline pdf-small" style="margin-left: 36px;">Signature of Applicant</div>
                        <div class="pdf-approved">
                            <div style="text-align:left; margin-right:250px; margin-bottom:20px; font-weight:700;">APPROVED BY:</div>
                            <div class="pdf-small" style=" margin-left: 108px; text-decoration:underline; font-weight:700;">{{ strtoupper(optional($req->purokLeader)->name ?? '') }}</div>
                            <div class="pdf-small" style="margin-left: 108px;">{{ $purokNameWithPrefix }} President</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Accept / Reject below the template -->
        @if ($showDecisionButtons)
            <div class="mt-4 flex items-center gap-2 justify-end">
                @if ($canReject)
                    <button type="button" id="open-reject-modal" class="px-3 py-1.5 text-sm rounded-md border border-red-300 text-red-700 hover:bg-red-50">Reject</button>
                @endif
                @if ($canApprovePurok || $canApproveBarangay)
                    <button type="button" id="open-accept-modal" class="px-3 py-1.5 text-sm rounded-md bg-green-600 text-white hover:bg-green-700">Approve</button>
                @endif
            </div>
        @endif
    </div>
    <div id="right-col">
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold">Applicant details</h2>
                <button type="button" id="hide-details-btn" class="text-sm px-2 py-1 rounded border border-gray-300 hover:bg-gray-50">Hide</button>
            </div>
            <div class="text-sm space-y-2">
                <div><span class="text-gray-500">Name:</span> <span class="font-medium">{{ $req->requester_name }}</span></div>
                <div><span class="text-gray-500">Gender:</span> <span class="font-medium">{{ $req->gender ?: '—' }}</span></div>
                <div><span class="text-gray-500">Age:</span> <span class="font-medium">{{ $age ?: '—' }}</span></div>
                <div><span class="text-gray-500">Address:</span> <span class="font-medium">{{ $req->address ?: '—' }}</span></div>
                <div><span class="text-gray-500">Purpose:</span> <span class="font-medium">{{ $req->purpose ?: '—' }}</span></div>
                <div><span class="text-gray-500">Contact:</span> <span class="font-medium">{{ $req->contact_number ?: '—' }}</span></div>
                <div><span class="text-gray-500">Email:</span> <span class="font-medium">{{ $req->email ?: '—' }}</span></div>
            </div>
            @php
                $frontPath = $req->valid_id_front_path; // may contain face photo or ID front
                $backPath = $req->valid_id_back_path;
            @endphp
            @if ($frontPath || $backPath)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Identity attachment</h3>
                    <div class="grid grid-cols-1 gap-3">
                        @if ($frontPath)
                            @php
                                $frontUrl = asset('storage/'.ltrim($frontPath,'/'));
                                $frontExt = strtolower(pathinfo($frontPath, PATHINFO_EXTENSION));
                                $isFrontImg = in_array($frontExt, ['jpg','jpeg','png','gif','bmp','webp']);
                            @endphp
                            <div>
                                <div class="text-xs text-gray-500 mb-1">Front ID / Face Photo</div>
                                @if ($isFrontImg)
                                    <a href="{{ $frontUrl }}" target="_blank" class="block">
                                        <img src="{{ $frontUrl }}" alt="Front ID / Face" class="w-full max-h-64 object-contain rounded border border-gray-200 bg-gray-50" />
                                    </a>
                                @else
                                    <a href="{{ $frontUrl }}" target="_blank" class="text-blue-600 hover:underline">Open front attachment ({{ strtoupper($frontExt) }})</a>
                                @endif
                            </div>
                        @endif
                        @if ($backPath)
                            @php
                                $backUrl = asset('storage/'.ltrim($backPath,'/'));
                                $backExt = strtolower(pathinfo($backPath, PATHINFO_EXTENSION));
                                $isBackImg = in_array($backExt, ['jpg','jpeg','png','gif','bmp','webp']);
                            @endphp
                            <div>
                                <div class="text-xs text-gray-500 mb-1">Back ID</div>
                                @if ($isBackImg)
                                    <a href="{{ $backUrl }}" target="_blank" class="block">
                                        <img src="{{ $backUrl }}" alt="Back ID" class="w-full max-h-64 object-contain rounded border border-gray-200 bg-gray-50" />
                                    </a>
                                @else
                                    <a href="{{ $backUrl }}" target="_blank" class="text-blue-600 hover:underline">Open back attachment ({{ strtoupper($backExt) }})</a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Request Modal -->
<div id="reject-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" data-dismiss="reject"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-lg border border-gray-200">
            <div class="px-5 py-4 border-b flex items-center justify-between">
                <h3 class="text-base font-semibold">Reject request</h3>
                <button type="button" class="text-gray-500 hover:text-gray-700" data-dismiss="reject">✕</button>
            </div>
            <form method="POST" action="{{ route('requests.reject', $req) }}" class="px-5 py-4 space-y-3">
                @csrf
                <label class="block text-sm font-medium">Reason</label>
                <textarea name="rejection_reason" class="w-full rounded-md border border-gray-300 p-2 focus:outline-none focus:ring-2 focus:ring-red-400" rows="4" required placeholder="Provide the reason for rejection"></textarea>
                <div class="mt-2 flex items-center justify-end gap-2">
                    <button type="button" class="px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-50" data-dismiss="reject">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">Reject</button>
                </div>
            </form>
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
    const rejectModal = document.getElementById('reject-modal');
    const openReject = document.getElementById('open-reject-modal');
    function hideReject(){ if (rejectModal) rejectModal.classList.add('hidden'); }
    function showReject(){ if (rejectModal) rejectModal.classList.remove('hidden'); }
    if (openReject) openReject.addEventListener('click', showReject);
    if (rejectModal){
        rejectModal.addEventListener('click', function(e){ if (e.target.closest('[data-dismiss="reject"]')) hideReject(); });
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') hideReject(); });
    }

    const acceptModal = document.getElementById('accept-modal');
    const openAccept = document.getElementById('open-accept-modal');
    function hideAccept(){ if (acceptModal) acceptModal.classList.add('hidden'); }
    function showAccept(){ if (acceptModal) acceptModal.classList.remove('hidden'); }
    if (openAccept) openAccept.addEventListener('click', showAccept);
    if (acceptModal){
        acceptModal.addEventListener('click', function(e){ if (e.target.closest('[data-dismiss="accept"]')) hideAccept(); });
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') hideAccept(); });
    }

    // PDF generation confirmation
    const pdfForm = document.getElementById('pdf-form');
    const pdfModal = document.getElementById('pdf-confirm-modal');
    const confirmPdfBtn = document.getElementById('confirm-pdf-btn');
    
    function showPdfModal(){ if (pdfModal) pdfModal.classList.remove('hidden'); }
    function hidePdfModal(){ if (pdfModal) pdfModal.classList.add('hidden'); }
    
    if (pdfForm) {
        pdfForm.addEventListener('submit', function(e) {
            e.preventDefault();
            showPdfModal();
        });
    }
    
    if (confirmPdfBtn) {
        confirmPdfBtn.addEventListener('click', function() {
            if (pdfForm) {
                pdfForm.submit();
            }
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
    // Toggle Applicant Details column to allow full-width template view
    const leftCol = document.getElementById('left-col');
    const rightCol = document.getElementById('right-col');
    const showBtn = document.getElementById('show-details-btn');
    const hideBtn = document.getElementById('hide-details-btn');
    const key = 'pl_clearance_view_details_hidden_{{ $req->id }}';
    function applyHiddenState(hidden){
        if (hidden){
            rightCol && rightCol.classList.add('hidden');
            if (leftCol){
                leftCol.classList.remove('lg:col-span-2');
                leftCol.classList.add('lg:col-span-3');
            }
            showBtn && showBtn.classList.remove('hidden');
        } else {
            rightCol && rightCol.classList.remove('hidden');
            if (leftCol){
                leftCol.classList.remove('lg:col-span-3');
                leftCol.classList.add('lg:col-span-2');
            }
            showBtn && showBtn.classList.add('hidden');
        }
        try { localStorage.setItem(key, hidden ? '1' : '0'); } catch(e){}
    }
    if (hideBtn) hideBtn.addEventListener('click', function(){ applyHiddenState(true); });
    if (showBtn) showBtn.addEventListener('click', function(){ applyHiddenState(false); });
    try {
        const saved = localStorage.getItem(key);
        if (saved === '1') applyHiddenState(true);
    } catch(e){}
})();
</script>
<script>
// no-op placeholder
</script>
@endpush
