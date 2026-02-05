<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Solo Parent Applications Report</title>

    <style>
        body {
            font-family: "Arial", sans-serif;
            font-size: 11px;
            margin: 25px;
        }

        /* ===== HEADER ===== */
        .header {
            text-align: center;
            margin-bottom: 15px;
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
            margin: 2px 0;
            font-weight: normal;
        }

        .divider {
            border-top: 2px solid #000;
            margin: 10px 0 15px 0;
        }

        /* ===== REPORT TITLE ===== */
        .report-title {
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .report-subtitle {
            text-align: center;
            font-size: 11px;
            margin-bottom: 15px;
        }

        /* ===== TABLE ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
        }

        th {
            text-align: center;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .small-text {
            font-size: 9px;
        }

        /* ===== FOOTER ===== */
        .footer {
            margin-top: 25px;
            font-size: 11px;
        }

        .signature {
            margin-top: 35px;
            width: 250px;
        }

        .signature-name {
            font-weight: bold;
            text-transform: uppercase;
        }

        .signature-title {
            font-size: 10px;
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
        Solo Parent Applications Report
    </div>

    <div class="report-subtitle">
        As of {{ now()->format('F d, Y') }}<br>
        Barangay Tejero, City of General Trias, Cavite
    </div>

    <!-- ===== TABLE ===== -->
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Reference No</th>
                <th>Full Name</th>
                <th>Sex</th>
                <th>Age</th>
                <th>Birth Date</th>
                <th>Address</th>
                <th>Educational Attainment</th>
                <th>Civil Status</th>
                <th>Occupation</th>
                <th>Monthly Income</th>
                <th>Employment Status</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Pantawid</th>
                <th>Indigenous</th>
                <th>LGBTQ</th>
                <th>PWD</th>
                <th>Category</th>
                <th>Stage</th>
                <th>Status</th>
                <th>Rejection Reason</th>
                <th>Application Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($applications as $index => $app)
                @if(strtolower($app->barangay) === 'tejero')
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $app->reference_no }}</td>
                    <td>{{ $app->full_name ?? $app->last_name . ', ' . $app->first_name }}</td>
                    <td class="text-center">{{ $app->sex ?? '-' }}</td>
                    <td class="text-center">{{ $app->age ?? '-' }}</td>
                    <td class="text-center">{{ $app->birth_date ?? '-' }}</td>
                    <td>
                        {{ $app->street }}, {{ $app->barangay }}, 
                        {{ $app->municipality }}, {{ $app->province }}
                    </td>
                    <td>{{ $app->educational_attainment ?? '-' }}</td>
                    <td>{{ $app->civil_status ?? '-' }}</td>
                    <td>{{ $app->occupation ?? '-' }}</td>
                    <td>{{ $app->monthly_income ?? '-' }}</td>
                    <td>{{ $app->employment_status ?? '-' }}</td>
                    <td>{{ $app->contact_number ?? '-' }}</td>
                    <td>{{ $app->email ?? '-' }}</td>
                    <td>{{ $app->pantawid ?? '-' }}</td>
                    <td>{{ $app->indigenous_person ?? '-' }}</td>
                    <td>{{ $app->lgbtq ?? '-' }}</td>
                    <td>{{ $app->pwd ?? '-' }}</td>
                    <td>{{ $app->category ?? '-' }}</td>
                    <td>{{ $app->application_stage ?? 'Review Application' }}</td>
                    <td>{{ $app->status ?? 'Pending' }}</td>
                    <td>{{ $app->rejection_reason ?? '-' }}</td>
                    <td>{{ $app->created_at->format('Y-m-d') }}</td>
                </tr>
                @endif
            @empty
                <tr>
                    <td colspan="23" class="text-center">No records available.</td>
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
            <div class="signature-title">CSWDO Staff / Administrator</div>
        </div>
    </div>

</body>
</html>
