<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @font-face { font-family: 'Consolas'; src: url("file://{{ public_path('fonts/consola.ttf') }}") format('truetype'); font-weight:400; font-style:normal; }
        @font-face { font-family: 'Consolas'; src: url("file://{{ public_path('fonts/consolab.ttf') }}") format('truetype'); font-weight:700; font-style:normal; }
        body { font-family: 'Consolas', monospace; font-size: 11pt; color:#000; }
        .row { margin-bottom: 8px; }
        .label { display:inline-block; width: 200px; font-weight:700; }
        .value { display:inline-block; min-width: 200px; border-bottom: 1px solid #000; padding: 0 4px; }
        .header { text-align:center; margin-bottom: 10px; }
        .title { font-weight:700; text-transform:uppercase; margin: 6px 0 12px; }
        .section { margin-top: 12px; margin-bottom: 8px; font-weight:700; text-transform:uppercase; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; grid-gap: 6px 16px; }
        .grid .label { width:auto; }
        .grid .value { width:100%; }
        .small { font-size: 10pt; }
        .thumbs { display:grid; grid-template-columns: 1fr 1fr 1fr; grid-gap: 12px; margin-top: 6px; }
        .box { border: 1px solid #000; height: 120px; display:flex; align-items:center; justify-content:center; }
        .consent { border:1px solid #000; padding:8px; margin-top: 12px; text-align: justify; }
        .check { display:inline-block; width:12px; height:12px; border:1px solid #000; margin-right:6px; text-align:center; line-height:12px; font-size:10pt; }
    </style>
</head>
<body>
    <div class="header">
        <div class="small">RBI Form B (Revised 2024)</div>
        <div class="title">INDIVIDUAL RECORDS OF BARANGAY INHABITANT</div>
        <div class="small">Barangay Government of Kalawag II • Municipality of Isulan • Province of Sultan Kudarat</div>
    </div>

    <div class="section">Personal Information</div>
    <div class="grid">
        <div><span class="label">PhilSys Card No.</span> <span class="value">{{ $record->philsys_card_no }}</span></div>
        <div><span class="label">Citizenship</span> <span class="value">{{ $record->citizenship }}</span></div>
        <div><span class="label">Last Name</span> <span class="value">{{ $record->last_name }}</span></div>
        <div><span class="label">First Name</span> <span class="value">{{ $record->first_name }}</span></div>
        <div><span class="label">Middle Name</span> <span class="value">{{ $record->middle_name }}</span></div>
        <div><span class="label">Suffix</span> <span class="value">{{ $record->suffix }}</span></div>
        <div><span class="label">Birth Date</span> <span class="value">{{ optional($record->birth_date)->format('Y-m-d') }}</span></div>
        <div><span class="label">Birth Place</span> <span class="value">{{ $record->birth_place }}</span></div>
        <div><span class="label">Sex</span> <span class="value">{{ $record->sex }}</span></div>
        <div><span class="label">Civil Status</span> <span class="value">{{ $record->civil_status }}</span></div>
        <div class="" style="grid-column:1 / span 2"><span class="label">Residence Address</span> <span class="value" style="min-width: 480px;">{{ $record->residence_address }}</span></div>
        <div><span class="label">Region</span> <span class="value">{{ $record->region }}</span></div>
        <div><span class="label">Province</span> <span class="value">{{ $record->province }}</span></div>
        <div><span class="label">City/Municipality</span> <span class="value">{{ $record->city_municipality }}</span></div>
        <div><span class="label">Barangay</span> <span class="value">{{ $record->barangay }}</span></div>
        <div><span class="label">Contact Number</span> <span class="value">{{ $record->contact_number }}</span></div>
        <div><span class="label">E-mail Address</span> <span class="value">{{ $record->email }}</span></div>
        <div><span class="label">Religion</span> <span class="value">{{ $record->religion }}</span></div>
        <div><span class="label">Occupation</span> <span class="value">{{ $record->occupation }}</span></div>
    </div>

    <div class="section">Highest Educational Attainment</div>
    <div class="grid">
        <div><span class="label">Attainment</span> <span class="value">{{ match($record->highest_educ_attainment){'elementary'=>'Elementary','high_school'=>'High School','college'=>'College','post_grad'=>'Post Grad','vocational'=>'Vocational', default => ''} }}</span></div>
        <div><span class="label">Specify</span> <span class="value">{{ $record->educ_specify }}</span></div>
        <div>
            <span class="label">Graduate</span>
            <span class="check">@if($record->is_graduate)✓@endif</span>
        </div>
        <div>
            <span class="label">Undergraduate</span>
            <span class="check">@if($record->is_undergraduate)✓@endif</span>
        </div>
    </div>

    <div class="consent">
        <div class="small">
            I hereby certify that the above information is true and correct to the best of my knowledge. I consent to the processing of my personal information subject to the Philippine Data Privacy Act of 2012.
        </div>
    </div>

    <div class="section">Thumbmarks / Signature</div>
    <div class="thumbs">
        <div>
            <div class="small">Left Thumbmark</div>
            <div class="box">
                @if($record->left_thumbmark_path)
                    <img src="{{ public_path('storage/'.ltrim($record->left_thumbmark_path,'/')) }}" style="max-width:100%; max-height:100%" />
                @endif
            </div>
        </div>
        <div>
            <div class="small">Right Thumbmark</div>
            <div class="box">
                @if($record->right_thumbmark_path)
                    <img src="{{ public_path('storage/'.ltrim($record->right_thumbmark_path,'/')) }}" style="max-width:100%; max-height:100%" />
                @endif
            </div>
        </div>
        <div>
            <div class="small">Signature</div>
            <div class="box">
                @if($record->signature_path)
                    <img src="{{ public_path('storage/'.ltrim($record->signature_path,'/')) }}" style="max-width:100%; max-height:100%" />
                @endif
            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 16px;">
        <span class="label">Date Accomplished</span> <span class="value">{{ optional($record->date_accomplished)->format('Y-m-d') }}</span>
    </div>
</body>
</html>
