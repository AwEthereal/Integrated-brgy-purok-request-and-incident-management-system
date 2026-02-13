<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        /* Embed Microsoft Consolas (provide the .ttf files under public/fonts) */
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
        .pdf-header { text-align:center; line-height:1.25; margin-bottom: 0px; }
        .pdf-title { text-align:center; font-family: 'Consolas', monospace; font-weight:700; text-transform:uppercase; margin: 0px 0 18px; }
        .pdf-row { margin-bottom:15px; text-align: justify; }
        .pdf-indent { text-indent:36px; }
        .pdf-u { text-decoration: underline; font-weight: 700; }
        .pdf-label { text-align: right; margin-bottom: 5px; }
        .pdf-sign { margin-top: 40px; display:flex; justify-content:space-between; align-items:flex-start; }
        .pdf-sigline { border-top:1px solid #000;width:260px; text-align:center; padding-top:4px; font-size:11pt; }
        .pdf-approved { text-align:right; }
        .pdf-small { font-size:11pt; text-align:center; margin-left:65%; margin-top:10px; }
        .highlight-title { background-color: yellow; padding:2px 4px;}
    </style>
</head>
<body>
    <div class="pdf-font">
        @php
            $addrClean = $req->address ?? '';
            if (is_string($addrClean)) {
                $addrClean = preg_replace('/\b(street|st\.?|st)\b\.?/i', '', $addrClean);
                $addrClean = trim($addrClean);
                $addrClean = rtrim($addrClean, ' ,.-');
            }

            $purokNameRaw = optional($req->purok)->name;
            $purokNameNoPrefix = is_string($purokNameRaw) ? preg_replace('/^\s*purok\s+/i', '', $purokNameRaw) : null;
            $purokNameNoPrefix = is_string($purokNameNoPrefix) ? trim($purokNameNoPrefix) : null;
            $purokNameForTitle = $purokNameNoPrefix ?: '________';
            $purokNameWithPrefix = 'Purok ' . ($purokNameNoPrefix ?: '________');
        @endphp
        <div class="pdf-header">
            <div>Republic of the Philippines</div>
            <div>Province of Sultan Kudarat</div>
            <div>Municipality of Isulan</div>
            <div style="font-weight:700;">BARANGAY GOVERNMENT OF KALAWAG II</div>
            <div class="pdf-title" style="font-size: 13pt;"><span class="highlight-title">PUROK {{ strtoupper($purokNameForTitle) }} CLEARANCE</span></div>
        </div>
        <div class="pdf-row" style="text-align:left;"><strong>TO WHOM IT MAY CONCERN:</strong></div>
        <div class="pdf-row pdf-indent">
            <strong>THIS IS TO CERTIFY</strong> that <span class="pdf-u">{{ $req->requester_name }}</span>, Filipino, (Gender)
            <span class="pdf-u">{{ $req->gender ?? '_____' }}</span>, (Age)
            <span class="pdf-u">{{ isset($age) ? $age : '_____' }}</span> years old and presently residing at
            <span class="pdf-u">{{ $addrClean ?: '________________' }}</span> St., Kalawag II, Isulan, Sultan Kudarat has appeared before me personally and requested clearance from
            <span class="pdf-u">PUROK {{ strtoupper($purokNameForTitle) }}</span> with the following findings:
        </div>
        <div class="pdf-row pdf-indent"><span class="pdf-u">No derogatory record on file as of this date.</span></div>
        <div class="pdf-row pdf-indent"><span class="pdf-label">Purpose:</span> <span class="pdf-u">{{ $req->purpose }}</span></div>
        <div class="pdf-row" style="margin-left:108px;">Issued this <span class="pdf-u">{{ \Carbon\Carbon::parse($issue_date)->format('jS') }}</span> day of <span class="pdf-u">{{ \Carbon\Carbon::parse($issue_date)->format('F') }}</span>, {{ \Carbon\Carbon::parse($issue_date)->format('Y') }}.</div>
        <div class="pdf-sign">
            <div class="pdf-sigline pdf-small" style="margin-left: 36px; margin-top: 20px;">Signature of Applicant</div>
            <div class="pdf-approved">
                <div style="font-weight:700; margin-top: -45px; margin-right:190px;">APPROVED BY:</div>
                <div class="pdf-small" style="text-decoration:underline; font-weight:700;">{{ strtoupper(optional($req->purokLeader)->name ?? '________________') }}</div>
                <div class="pdf-small">{{ $purokNameWithPrefix }} President</div>
            </div>
        </div>
    </div>
</body>
</html>
