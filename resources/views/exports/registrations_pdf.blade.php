<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Peserta - {{ $event->title }}</title>
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
    <h2>Data Peserta - {{ $event->title }}</h2>
    <p>{{ $event->location }} | {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Registrasi</th>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>No. HP</th>
                <th>Status Registrasi</th>
                <th>Status Pembayaran</th>
                <th>Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registrations as $i => $reg)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $reg->registration_code }}</td>
                <td>{{ $reg->full_name }}</td>
                <td>{{ $reg->email }}</td>
                <td>{{ $reg->phone_number }}</td>
                <td>{{ $reg->status }}</td>
                <td>{{ $reg->payment?->payment_status ?? '-' }}</td>
                <td>{{ $reg->attendance?->attendance_status ?? 'absent' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
