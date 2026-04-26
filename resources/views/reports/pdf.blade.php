<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan SIRA</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
        }

        h1 {
            margin-bottom: 4px;
            font-size: 20px;
        }

        p {
            margin-top: 0;
            color: #4b5563;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
        }

        .grid {
            width: 100%;
            margin-top: 12px;
        }

        .card {
            border: 1px solid #d1d5db;
            padding: 8px;
            margin-bottom: 8px;
        }
    </style>
</head>

<body>
    <h1>Laporan Sistem SIRA</h1>
    <p>Dihasilkan pada {{ now()->format('d-m-Y H:i') }}</p>

    <div class="grid">
        <div class="card">Total Santri: {{ $summary['total_students'] }}</div>
        <div class="card">Total Pelanggaran: {{ $summary['total_violations'] }}</div>
        <div class="card">Total Prediksi: {{ $summary['total_predictions'] }}</div>
        <div class="card">Santri Risiko Tinggi: {{ $summary['high_risk_students'] }}</div>
    </div>

    <h2>Top Risiko Pelanggaran Ulang</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama</th>
                <th>Risk Probability</th>
                <th>Rank Score</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($topRisk as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->student?->nis }}</td>
                    <td>{{ $item->student?->name }}</td>
                    <td>{{ number_format((float) $item->risk_probability * 100, 2) }}%</td>
                    <td>{{ $item->rank_score }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Belum ada data prediksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
