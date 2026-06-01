<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Transaksi - {{ $event->title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h2 { text-align: center; }
        p { text-align: center; color: gray; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #333; color: white; padding: 8px; text-align: left; }
        td { padding: 6px 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <h2>Data Transaksi - {{ $event->title }}</h2>
    <p>{{ $event->location }} | {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Transaction ID</th>
                <th>Nama Peserta</th>
                <th>Email</th>
                <th>Jumlah</th>
                <th>Status Pembayaran</th>
                <th>Tanggal Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $i => $payment)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $payment->transaction_id }}</td>
                <td>{{ $payment->registration?->full_name ?? '-' }}</td>
                <td>{{ $payment->registration?->email ?? '-' }}</td>
                <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                <td>{{ $payment->payment_status }}</td>
                <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
