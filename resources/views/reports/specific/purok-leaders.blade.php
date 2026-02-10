<!DOCTYPE html>
<html>
<head>
    <title>Selected Purok Leaders Report - {{ now()->format('F j, Y') }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 { 
            margin: 0; 
            font-size: 20px;
            color: #333;
        }
        .header p { 
            margin: 5px 0 0;
            color: #666;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px;
            font-size: 11px;
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 6px; 
            text-align: left; 
        }
        th { 
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer { 
            margin-top: 20px; 
            text-align: right; 
            font-size: 10px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
        .logo {
            max-width: 80px;
            height: auto;
        }
        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0;
        }
        .report-meta {
            margin: 10px 0;
            font-size: 11px;
            color: #555;
        }
        .text-center {
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin: 40px 0 5px;
            display: inline-block;
        }
        .signature-label {
            font-size: 11px;
            color: #333;
        }
        @page {
            margin: 20px;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                font-size: 11px;
            }
            table {
                font-size: 10px;
            }
            .header h1 {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 10px; text-align: right;">
        <button type="button" onclick="window.print()" style="background:#16a34a;color:#fff;border:none;padding:8px 12px;border-radius:6px;cursor:pointer;">
            Print
        </button>
    </div>
    <div class="header">
        <div class="text-center">
            @if(file_exists(public_path('images/logo.png')))
                <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Logo">
            @endif
            <h1>BARANGAY KALAWAG II</h1>
            <p>City of Isulan, Sultan Kudarat</p>
            <p>OFFICE OF THE BARANGAY CAPTAIN</p>
        </div>
        
        <div class="report-title">SELECTED PUROK LEADERS REPORT</div>
        <div class="report-meta">
            Generated on: {{ now()->format('F j, Y h:i A') }}<br>
            Total Records: {{ $leaders->count() }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Purok</th>
                <th>Contact Number</th>
                <th>DOB</th>
                <th>Sex</th>
                <th>Date Assigned</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leaders as $index => $leader)
            @php($rbi = $leader->latestResidentRecord)
            <tr>
                <td>{{ strtoupper($rbi?->last_name ?? $leader->last_name) }}, {{ strtoupper($rbi?->first_name ?? $leader->first_name) }} {{ strtoupper($rbi?->middle_name ?? $leader->middle_name) }}</td>
                <td>{{ $leader->purok->name ?? 'N/A' }}</td>
                <td>{{ $rbi?->contact_number ?? $leader->contact_number ?? 'N/A' }}</td>
                <td>{{ $rbi?->birth_date?->format('Y-m-d') ?? ($leader->birth_date ? $leader->birth_date->format('Y-m-d') : ($leader->date_of_birth ?? 'N/A')) }}</td>
                <td>{{ $rbi?->sex ?? $leader->sex ?? $leader->gender ?? 'N/A' }}</td>
                <td>{{ $leader->created_at->format('M j, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div style="margin-top: 50px;">
            <div style="float: right; text-align: center; margin-left: 30px;">
                <div class="signature-line"></div>
                <div class="signature-label">Barangay Captain</div>
            </div>
            <div style="float: right; text-align: center; margin-right: 30px;">
                <div class="signature-line"></div>
                <div class="signature-label">Barangay Secretary</div>
            </div>
            <div style="clear: both;"></div>
        </div>
        
        <div style="margin-top: 20px; text-align: center; font-size: 10px; color: #777;">
            <p>This is a computer-generated document. No signature is required.</p>
            <p>Page <span class="page-number"></span> of <span class="page-count"></span></p>
        </div>
    </div>

    <script>
        // Add page numbers
        document.addEventListener('DOMContentLoaded', function() {
            const totalPages = Math.ceil(document.querySelectorAll('tr').length / 25); // Adjust based on your rows per page
            document.querySelector('.page-count').textContent = totalPages;
            
            // Simple page counter (for multi-page PDFs)
            const pageNumbers = document.querySelectorAll('.page-number');
            pageNumbers.forEach((el, index) => {
                el.textContent = index + 1;
            });
        });
    </script>
</body>
</html>
