<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — Zverse</title>
    <style>
        body { font-family: 'Inter', 'Segoe UI', sans-serif; background: #f8fafc; margin: 0; padding: 0; }
        .container { max-width: 480px; margin: 40px auto; background: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06); }
        .header { padding: 32px 32px 0; text-align: center; }
        .header h1 { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 4px; letter-spacing: -0.5px; }
        .header p { font-size: 13px; color: #64748b; margin: 0; }
        .body { padding: 24px 32px 32px; }
        .body p { font-size: 14px; line-height: 1.7; color: #334155; margin: 0 0 20px; }
        .btn { display: block; text-align: center; background: #0ea5e9; color: #ffffff !important; text-decoration: none; font-weight: 700; font-size: 14px; padding: 14px 24px; border-radius: 12px; margin: 24px 0; }
        .btn:hover { background: #0284c7; }
        .footer { padding: 0 32px 32px; text-align: center; }
        .footer p { font-size: 12px; color: #94a3b8; margin: 0 0 4px; }
        .footer a { color: #0ea5e9; text-decoration: none; }
        .divider { height: 1px; background: #e2e8f0; margin: 24px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Zverse</h1>
            <p>Media & Tech</p>
        </div>
        <div class="body">
            <p>Halo,</p>
            <p>Kami menerima permintaan reset password untuk akun Zverse kamu. Klik tombol di bawah untuk membuat password baru:</p>
            <a href="{{ route('password.reset', $token) }}" class="btn">Reset Password</a>
            <p style="font-size: 13px; color: #94a3b8;">Link ini akan kedaluwarsa dalam 60 menit. Jika kamu tidak meminta reset password, abaikan email ini.</p>
            <div class="divider"></div>
            <p style="font-size: 13px; color: #64748b;">Jika tombol di atas tidak berfungsi, salin dan buka link berikut di browser:</p>
            <p style="font-size: 12px; color: #0ea5e9; word-break: break-all;">{{ route('password.reset', $token) }}</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Zverse — Portal Entertainment & Tech Modern</p>
        </div>
    </div>
</body>
</html>
