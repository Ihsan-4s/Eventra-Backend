<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #F4F6FF;
            padding: 30px;
        }

        .ticket {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            max-width: 500px;
            margin: 0 auto;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: #6366F1;
            color: white;
            padding: 28px;
        }

        .header .org {
            font-size: 11px;
            opacity: 0.7;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .header h1 {
            font-size: 22px;
            font-weight: bold;
            line-height: 1.3;
        }

        .header .meta {
            display: flex;
            gap: 16px;
            margin-top: 14px;
            font-size: 12px;
            opacity: 0.85;
        }

        .divider {
            border-top: 2px dashed #e2e8f0;
            margin: 0 24px;
        }

        .body {
            padding: 24px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 13px;
        }

        .row .label {
            color: #94a3b8;
        }

        .row .value {
            font-weight: 600;
            color: #0F172A;
        }

        .code {
            color: #6366F1;
            font-family: monospace;
        }

        .qr-section {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
        }

        .qr-section img {
            width: 140px;
            height: 140px;
        }

        .qr-section p {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 8px;
        }

        .footer {
            background: #f8fafc;
            padding: 14px 24px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
        }
    </style>
</head>

<body>
    <div class="ticket">
        <div class="header">
            <div class="org">{{ $event->organization_name }}</div>
            <h1>{{ $event->title }}</h1>
            <div class="meta">
                <span>📅 {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}</span>
                <span>📍 {{ $event->location }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <div class="body">
            <div class="row">
                <span class="label">Name</span>
                <span class="value">{{ $registration->full_name }}</span>
            </div>
            <div class="row">
                <span class="label">Email</span>
                <span class="value">{{ $registration->email }}</span>
            </div>
            <div class="row">
                <span class="label">Phone</span>
                <span class="value">{{ $registration->phone_number }}</span>
            </div>
            <div class="row">
                <span class="label">Registration Code</span>
                <span class="value code">{{ $registration->registration_code }}</span>
            </div>
            <div class="row">
                <span class="label">Ticket Code</span>
                <span class="value code">{{ $ticket->ticket_code }}</span>
            </div>
            <div class="row">
                <span class="label">Amount Paid</span>
                <span class="value" style="color: #10b981;">Rp {{ number_format($event->price, 0, ',', '.') }}</span>
            </div>

            <div class="qr-section">
                <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code" />
                <p>Scan this QR code at the event entrance</p>
            </div>
        </div>

        <div class="footer">
            Powered by Eventra • {{ $event->organization_name }}
        </div>
    </div>
</body>
</html>
