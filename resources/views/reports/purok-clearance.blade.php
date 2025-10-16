<!DOCTYPE html>
<html>
<head>
    <title>Purok Clearance Requests - {{ now()->format('F j, Y') }}</title>
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
        .status-approved { color: #00a65a; }
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
    <div class="header">
        <div class="text-center">
            @if(file_exists(public_path('images/logo.png')))
                <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Logo">
            @endif
            <h1>BARANGAY KALAWAG II</h1>
            <p>City of Isulan, Sultan Kudarat</p>
            <p>OFFICE OF THE BARANGAY CAPTAIN</p>
        </div>
        
        <div class="report-title">LIST OF PUROK CLEARANCE REQUESTS</div>
        <div class="report-meta">
            Generated on: {{ now()->format('F j, Y h:i A') }}<br>
            Total Records: {{ $requests->count() }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Request ID</th>
                <th>Resident Name</th>
                <th>Purok</th>
                <th>Purpose</th>
                <th>Status</th>
                <th>Date Requested</th>
                <th>Approved By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $index => $request)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>REQ-{{ str_pad($request->id, 6, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $request->user->last_name }}, {{ $request->user->first_name }}</td>
                <td>{{ $request->purok->name ?? 'N/A' }}</td>
                <td>{{ $request->purpose }}</td>
                <td class="status-{{ strtolower($request->status) }}">
                    {{ format_label($request->status) }}
                </td>
                <td>{{ $request->created_at->format('M j, Y h:i A') }}</td>
                <td>
                    @if($request->barangayApprover)
                        {{ $request->barangayApprover->name }}
                    @elseif($request->purokApprover)
                        {{ $request->purokApprover->name }}
                    @else
                        N/A
                    @endif
                </td>
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
            const totalPages = Math.ceil(document.querySelectorAll('tr').length / 20); // Adjust based on your rows per page
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
