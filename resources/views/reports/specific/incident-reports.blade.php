<!DOCTYPE html>
<html>
<head>
    <title>Selected Incident Reports - {{ now()->format('F j, Y') }}</title>
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
        .status-pending { color: #f39c12; }
        .status-in-progress { color: #3498db; }
        .status-resolved { color: #00a65a; }
        .status-rejected { color: #dd4b39; }
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
        .incident-details {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #eee;
            border-radius: 4px;
        }
        .incident-details h4 {
            margin-top: 0;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
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
            .incident-details {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="text-center">
            @if(file_exists(public_path('images/logo.png')))
                <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Logo">
            @endif
            <h1>BARANGAY KALAWAG II</h1>
            <p>City of Isulan, Sultan Kudarat</p>
            <p>OFFICE OF THE BARANGAY CAPTAIN</p>
        </div>
        
        <div class="report-title">SELECTED INCIDENT REPORTS</div>
        <div class="report-meta">
            Generated on: {{ now()->format('F j, Y h:i A') }}<br>
            Total Records: {{ $reports->count() }}
        </div>
    </div>

    @foreach($reports as $index => $report)
    <div class="incident-details">
        <h4>INCIDENT REPORT #{{ $report->id }}</h4>
        <table style="width: 100%; margin-bottom: 15px;">
            <tr>
                <th style="width: 20%;">Incident Date:</th>
                <td style="width: 30%;">{{ $report->incident_date->format('F j, Y h:i A') }}</td>
                <th style="width: 20%;">Reported By:</th>
                <td style="width: 30%;">{{ $report->reporter->full_name ?? 'Anonymous' }}</td>
            </tr>
            <tr>
                <th>Type:</th>
                <td>{{ $report->type }}</td>
                <th>Status:</th>
                <td class="status-{{ strtolower($report->status) }}">
                    {{ ucfirst($report->status) }}
                </td>
            </tr>
            <tr>
                <th>Location:</th>
                <td colspan="3">{{ $report->location }}</td>
            </tr>
            <tr>
                <th>Description:</th>
                <td colspan="3">{{ $report->description }}</td>
            </tr>
            @if($report->responder)
            <tr>
                <th>Responder:</th>
                <td>{{ $report->responder->name }}</td>
                <th>Responded At:</th>
                <td>{{ $report->responded_at ? $report->responded_at->format('F j, Y h:i A') : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Resolution:</th>
                <td colspan="3">{{ $report->resolution ?? 'No resolution provided' }}</td>
            </tr>
            @endif
        </table>
    </div>
    @endforeach

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
            const totalPages = Math.ceil(document.body.scrollHeight / 1000); // Approximate pages based on content
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
