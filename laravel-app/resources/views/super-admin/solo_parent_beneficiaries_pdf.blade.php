<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Solo Parent Beneficiaries Report</title>

    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12px;
            margin: 30px;
        }

        /* ===== HEADER ===== */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 14px;
            margin: 0;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 13px;
            margin: 3px 0;
            font-weight: bold;
        }

        .header h3 {
            font-size: 12px;
            margin: 3px 0;
            font-weight: normal;
        }

        .divider {
            border-top: 2px solid #000;
            margin: 15px 0;
        }

        /* ===== REPORT TITLE ===== */
        .report-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .report-subtitle {
            text-align: center;
            font-size: 11px;
            margin-bottom: 15px;
        }

        .meta {
            font-size: 11px;
            margin-bottom: 10px;
        }

        /* ===== TABLE ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }

        th {
            text-align: center;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        /* ===== FOOTER ===== */
        .footer {
            margin-top: 30px;
            font-size: 11px;
        }

        .signature {
            margin-top: 40px;
            width: 250px;
        }

        .signature-name {
            font-weight: bold;
            text-transform: uppercase;
        }

        .signature-title {
            font-size: 11px;
        }
    </style>
</head>
<body>

    <!-- ===== HEADER ===== -->
    <div class="header">
        <h1>Republic of the Philippines</h1>
        <h2>City of General Trias, Cavite</h2>
        <h3>City Social Welfare and Development Office</h3>
    </div>

    <div class="divider"></div>

    <!-- ===== REPORT TITLE ===== -->
    <div class="report-title">
        List of Solo Parent Beneficiaries
    </div>

    <div class="report-subtitle">
        As of {{ now()->format('F d, Y') }}<br>
        City of General Trias, Cavite
    </div>

    <div class="meta">
        This report reflects the consolidated list of registered solo parent
        beneficiaries across all barangays of the City of General Trias,
        Cavite, as recorded by the City Social Welfare and Development Office.
    </div>

    <!-- ===== TABLE ===== -->
    <table>
        <thead>
            <tr>
                <th style="width:4%">No.</th>
                <th style="width:12%">Reference No.</th>
                <th style="width:16%">Beneficiary Name</th>
                <th style="width:22%">Barangay</th>
                <th style="width:12%">Category</th>
                <th style="width:12%">Assistance Status</th>
                <th style="width:12%">Date Added</th>
            </tr>
        </thead>
        <tbody>
            @forelse($beneficiaries as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->reference_no ?? '-' }}</td>
                    <td>{{ $item->last_name }}, {{ $item->first_name }}</td>
                    <td>{{ $item->barangay }}</td>
                    <td>{{ $item->category ?? '-' }}</td>
                    <td>{{ $item->assistance_status }}</td>
                    <td class="text-center">{{ $item->created_at->format('Y-m-d') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No records available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- ===== FOOTER ===== -->
    <div class="footer">
        <div class="signature">
            <div class="signature-name">Prepared By:</div>
            <br><br>
            <div class="signature-name">______________________________</div>
            <div class="signature-title">
                CSWDO Staff / Administrator
            </div>
        </div>
    </div>

</body>
</html>
