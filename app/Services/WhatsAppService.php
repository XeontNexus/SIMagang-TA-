<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Format nomor ke format internasional Indonesia (62xxxxxxxxxx).
     */
    public function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }

        if (!str_starts_with($phone, '62')) {
            return '62' . $phone;
        }

        return $phone;
    }

    /**
     * Pesan notifikasi akun PKL/SIMagang baru.
     */
    public function buildAccountCreatedMessage(User $user, string $plainPassword): string
    {
        $loginUrl = url('/login');

        return implode("\n", [
            "Halo *{$user->nama_lengkap}*,",
            '',
            'Admin SIMagang menginformasikan bahwa *akun PKL Anda sudah siap digunakan*.',
            '',
            'Detail login:',
            "Username: *{$user->username}*",
            "Password: *{$plainPassword}*",
            '',
            "Link login: {$loginUrl}",
            '',
            '_Segera ganti password setelah login pertama demi keamanan._',
        ]);
    }

    public function buildPasswordResetMessage(User $user, string $resetUrl): string
    {
        return implode("\n", [
            "Halo *{$user->nama_lengkap}*,",
            '',
            'Admin SIMagang mengirimkan permintaan reset password akun SIMagang Anda.',
            '',
            "Link reset password: {$resetUrl}",
            '',
            '_Link berlaku terbatas. Jika Anda tidak meminta reset, abaikan pesan ini._',
        ]);
    }

    public function sendPasswordReset(User $user, string $resetUrl): array
    {
        if (empty($user->no_hp)) {
            return ['success' => false, 'message' => 'Nomor WhatsApp siswa belum diisi.'];
        }

        return $this->send($user->no_hp, $this->buildPasswordResetMessage($user, $resetUrl));
    }

    /**
     * Kirim pesan WhatsApp ke nomor tujuan.
     */
    public function send(string $phone, string $message): array
    {
        if (!config('whatsapp.enabled')) {
            return ['success' => false, 'message' => 'Layanan WhatsApp dinonaktifkan.'];
        }

        $token = config('whatsapp.fonnte_token');
        if (empty($token)) {
            return ['success' => false, 'message' => 'Token Fonnte belum dikonfigurasi. Isi FONNTE_TOKEN di file .env'];
        }

        $target = $this->formatPhone($phone);

        try {
            $response = Http::timeout(30)
                ->withHeaders(['Authorization' => $token])
                ->post(config('whatsapp.api_url'), [
                    'target' => $target,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

            $body = $response->json() ?? [];
            $ok = $response->successful()
                && (isset($body['status']) ? (bool) $body['status'] : true);

            if (!$ok) {
                $detail = $body['reason'] ?? $body['message'] ?? $response->body();
                Log::error('WhatsApp Fonnte gagal', ['target' => $target, 'response' => $body]);

                return ['success' => false, 'message' => 'Gagal mengirim WhatsApp: ' . $detail];
            }

            return ['success' => true, 'message' => 'Pesan WhatsApp berhasil dikirim.'];
        } catch (\Exception $e) {
            Log::error('WhatsApp Fonnte exception: ' . $e->getMessage());

            return ['success' => false, 'message' => 'Gagal mengirim WhatsApp: ' . $e->getMessage()];
        }
    }

    /**
     * Kirim notifikasi akun baru ke siswa.
     */
    public function sendAccountCreated(User $user, string $plainPassword): array
    {
        if (empty($user->no_hp)) {
            return ['success' => false, 'message' => 'Nomor WhatsApp siswa belum diisi.'];
        }

        $message = $this->buildAccountCreatedMessage($user, $plainPassword);

        return $this->send($user->no_hp, $message);
    }

    /**
     * Link wa.me sebagai cadangan jika API gagal (admin bisa klik sekali).
     */
    public function waMeLink(string $phone, string $message): string
    {
        $target = $this->formatPhone($phone);

        return 'https://wa.me/' . $target . '?text=' . rawurlencode($message);
    }
}
