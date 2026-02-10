<!DOCTYPE html>
<html>
<head>
    <title>RBI Residents Report - {{ now()->format('F j, Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header p {
            margin: 4px 0 0;
            color: #666;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 16px;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 6px;
            text-align: right;
        }
        @page {
            margin: 18px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BARANGAY KALAWAG II</h1>
        <p>RBI Resident Records (Form B)</p>
        <p>Generated on {{ now()->format('F j, Y g:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Name</th>
                <th>Sex</th>
                <th>Birth Date</th>
                <th>Contact</th>
                <th>Purok</th>
                <th>Purok Leader</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $r)
                @php($leader = ($leadersByPurok[$r->purok_id] ?? null))
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $r->last_name }}, {{ $r->first_name }} {{ $r->middle_name }}</td>
                    <td>{{ $r->sex }}</td>
                    <td>{{ optional($r->birth_date)->format('Y-m-d') }}</td>
                    <td>{{ $r->contact_number }}</td>
                    <td>{{ $r->purok->name ?? 'N/A' }}</td>
                    <td>
                        {{ $leader?->name ?? ($leader?->first_name ? ($leader->first_name.' '.$leader->last_name) : 'N/A') }}
                        @if($leader?->username)
                            <br><small>({{ $leader->username }})</small>
                        @elseif($leader?->contact_number)
                            <br><small>{{ $leader->contact_number }}</small>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Total Records: {{ count($records) }}
    </div>
</body>
</html>
