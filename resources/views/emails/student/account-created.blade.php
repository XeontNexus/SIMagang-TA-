<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun PKL SIMagang Anda Sudah Siap</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f4f6f9; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); padding: 32px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700; letter-spacing: 0.5px;">
                                📚 SIMagang
                            </h1>
                            <p style="margin: 8px 0 0; color: rgba(255,255,255,0.85); font-size: 14px;">
                                Sistem Informasi Magang
                            </p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="margin: 0 0 8px; color: #2d3748; font-size: 20px; font-weight: 600;">
                                Halo, {{ $student->nama_lengkap }}! 👋
                            </h2>
                            <p style="margin: 0 0 24px; color: #718096; font-size: 15px; line-height: 1.6;">
                                Admin SIMagang menginformasikan bahwa <strong style="color: #2d3748;">akun PKL Anda sudah siap digunakan</strong>.
                                Berikut adalah detail login Anda:
                            </p>

                            {{-- Credentials Box --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px 24px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="padding: 6px 0; color: #718096; font-size: 14px; width: 120px;">Username</td>
                                                <td style="padding: 6px 0; color: #2d3748; font-size: 14px; font-weight: 600;">{{ $student->username }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; color: #718096; font-size: 14px;">Password</td>
                                                <td style="padding: 6px 0;">
                                                    <code style="background-color: #edf2f7; padding: 3px 10px; border-radius: 4px; color: #e53e3e; font-size: 14px; font-weight: 600;">{{ $plainPassword }}</code>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Login Button --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $loginUrl }}" target="_blank" style="display: inline-block; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: #ffffff; text-decoration: none; padding: 14px 36px; border-radius: 8px; font-size: 15px; font-weight: 600; letter-spacing: 0.3px;">
                                            🔑 Login Sekarang
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            {{-- Warning --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fffbeb; border: 1px solid #fcd34d; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 14px 20px;">
                                        <p style="margin: 0; color: #92400e; font-size: 13px; line-height: 1.5;">
                                            ⚠️ <strong>Penting:</strong> Segera ganti password setelah login pertama demi keamanan akun Anda.
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
                                Jika Anda tidak merasa mendaftar, silakan abaikan email ini.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
