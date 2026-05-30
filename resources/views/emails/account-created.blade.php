<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Akun SIMagang Anda Telah Aktif</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div style="background-color: #4e73df; color: #fff; padding: 20px; text-align: center;">
            <h2 style="margin: 0;">Selamat Datang di SIMagang</h2>
        </div>
        
        <div style="padding: 30px;">
            <p>Halo <strong>{{ $user->nama_lengkap }}</strong>,</p>
            
            <p>Akun Anda untuk sistem SIMagang telah berhasil dibuat dan <strong>sudah aktif</strong>. Anda sekarang dapat menggunakan sistem ini untuk keperluan magang/PKL Anda.</p>
            
            <div style="background-color: #f8f9fc; border-left: 4px solid #4e73df; padding: 15px; margin: 20px 0;">
                <p style="margin: 0 0 10px 0;">Berikut adalah detail login Anda:</p>
                <ul style="margin: 0; padding-left: 20px;">
                    <li><strong>Username:</strong> {{ $user->username }}</li>
                    <li><strong>Password:</strong> {{ $plainPassword }}</li>
                </ul>
            </div>
            
            <p>Silakan login menggunakan informasi di atas pada tautan berikut:</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/login') }}" style="background-color: #4e73df; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">Login ke SIMagang</a>
            </div>
            
            <p style="color: #e74a3b; font-size: 0.9em; margin-top: 30px;">
                * Demi keamanan, kami menyarankan Anda untuk segera mengganti password setelah berhasil login pertama kali.
            </p>
        </div>
        
        <div style="background-color: #f8f9fc; padding: 15px; text-align: center; font-size: 0.8em; color: #858796; border-top: 1px solid #e3e6f0;">
            <p style="margin: 0;">&copy; {{ date('Y') }} SIMagang. All rights reserved.</p>
            <p style="margin: 5px 0 0 0;">Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>
