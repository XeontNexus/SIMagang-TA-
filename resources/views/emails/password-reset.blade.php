<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Password SIMagang</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        .button { display: inline-block; padding: 12px 24px; background: #0d6efd; color: white; text-decoration: none; border-radius: 5px; margin: 15px 0; }
        .footer { color: #666; font-size: 12px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Halo, {{ $user->nama_lengkap }}!</h2>
        <p>Anda menerima email ini karena ada permintaan reset password untuk akun Anda di <strong>SIMagang</strong>.</p>
        <p>Klik tombol di bawah ini untuk mengatur password baru:</p>
        <a href="{{ $url }}" class="button">Reset Password</a>
        <p>Atau salin dan tempel link berikut ke browser Anda:</p>
        <p style="word-break: break-all;"><a href="{{ $url }}">{{ $url }}</a></p>
        <p>Link ini akan kadaluarsa dalam <strong>60 menit</strong>.</p>
        <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
        <div class="footer">
            <p>Salam,<br>Tim SIMagang</p>
        </div>
    </div>
</body>
</html>
