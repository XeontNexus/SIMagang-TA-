<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password SIMagang</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f4f6f9; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%); padding: 32px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700; letter-spacing: 0.5px;">
                                📚 SIMagang
                            </h1>
                            <p style="margin: 8px 0 0; color: rgba(255,255,255,0.9); font-size: 14px;">
                                Reset Password
                            </p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="margin: 0 0 8px; color: #2d3748; font-size: 20px; font-weight: 600;">
                                Halo, {{ $student->nama_lengkap }}! 🔐
                            </h2>
                            <p style="margin: 0 0 24px; color: #718096; font-size: 15px; line-height: 1.6;">
                                Admin SIMagang mengirimkan permintaan <strong style="color: #2d3748;">reset password</strong> untuk akun SIMagang Anda.
                                Klik tombol di bawah untuk mengatur password baru.
                            </p>

                            {{-- Reset Button --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $resetUrl }}" target="_blank" style="display: inline-block; background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%); color: #ffffff; text-decoration: none; padding: 14px 36px; border-radius: 8px; font-size: 15px; font-weight: 600; letter-spacing: 0.3px;">
                                            🔄 Reset Password Sekarang
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            {{-- Fallback link --}}
                            <p style="margin: 0 0 24px; color: #a0aec0; font-size: 12px; line-height: 1.5; word-break: break-all;">
                                Jika tombol tidak berfungsi, salin dan tempel link berikut di browser Anda:<br>
                                <a href="{{ $resetUrl }}" style="color: #4e73df;">{{ $resetUrl }}</a>
                            </p>

                            {{-- Warning --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fffbeb; border: 1px solid #fcd34d; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 14px 20px;">
                                        <p style="margin: 0; color: #92400e; font-size: 13px; line-height: 1.5;">
                                            ⚠️ Link ini berlaku terbatas. Jika Anda tidak meminta reset password, silakan abaikan email ini.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f8fafc; padding: 20px 40px; border-top: 1px solid #e2e8f0; text-align: center;">
                            <p style="margin: 0; color: #a0aec0; font-size: 12px; line-height: 1.6;">
                                Email ini dikirim secara otomatis oleh sistem SIMagang.<br>
                                Jika Anda tidak merasa memiliki akun, silakan abaikan email ini.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
