<?php

namespace App\Services;

use App\Models\LocationChangeRequest;
use App\Models\Logbook;
use App\Models\Notification;
use App\Models\StudentNotification;
use App\Models\User;

class NotificationService
{
    public static function getUnreadCount($userId): int
    {
        return Notification::forUser($userId)->unread()->count();
    }

    public static function getNavbarBadgeCount(User $user): int
    {
        $count = self::getUnreadCount($user->id);

        if ($user->isSiswa()) {
            $count += StudentNotification::where('user_id', $user->id)
                ->whereNull('read_at')
                ->count();
        }

        return $count;
    }

    public static function getUnreadNotifications($userId, $limit = null)
    {
        $items = self::getBellItems(User::find($userId), $limit ?? 50);

        return $limit ? $items->take($limit) : $items;
    }

    /**
     * Gabungkan notifikasi dari tabel notifications + student_notifications (data lama).
     */
    public static function getBellItems(?User $user, int $limit = 8)
    {
        if (!$user) {
            return collect();
        }

        $items = Notification::forUser($user->id)
            ->unread()
            ->recent()
            ->get()
            ->map(fn ($n) => (object) [
                'id' => $n->id,
                'source' => 'notification',
                'title' => $n->title,
                'message' => $n->message,
                'type' => $n->type,
                'icon' => $n->icon ?? 'fa-bell',
                'link' => $n->link,
                'created_at' => $n->created_at,
            ]);

        if ($user->isSiswa()) {
            $legacy = StudentNotification::where('user_id', $user->id)
                ->whereNull('read_at')
                ->orderByDesc('created_at')
                ->get()
                ->map(fn ($n) => (object) [
                    'id' => $n->id,
                    'source' => 'student',
                    'title' => $n->title,
                    'message' => $n->message,
                    'type' => $n->type,
                    'icon' => 'fa-bell',
                    'link' => null,
                    'created_at' => $n->created_at,
                ]);

            $items = $items->concat($legacy);
        }

        return $items->sortByDesc('created_at')->take($limit)->values();
    }

    public static function markAllAsReadForUser(User $user): void
    {
        Notification::forUser($user->id)->unread()->update(['read_at' => now()]);

        if ($user->isSiswa()) {
            StudentNotification::where('user_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }
    }

    public static function markBellItemAsRead(User $user, string $source, int $id): bool
    {
        if ($source === 'student' && $user->isSiswa()) {
            $item = StudentNotification::where('user_id', $user->id)->find($id);
            if ($item) {
                $item->update(['read_at' => now()]);
                return true;
            }
            return false;
        }

        $item = Notification::forUser($user->id)->find($id);
        if ($item) {
            $item->markAsRead();
            return true;
        }

        return false;
    }

    public static function notifyAllAdmins(
        string $title,
        string $message,
        string $type = 'info',
        ?string $icon = 'fa-bell',
        ?string $link = null,
        ?string $relatedType = null,
        ?int $relatedId = null
    ): void {
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            if ($relatedType && $relatedId) {
                $exists = Notification::forUser($admin->id)
                    ->where('related_type', $relatedType)
                    ->where('related_id', $relatedId)
                    ->whereNull('read_at')
                    ->exists();

                if ($exists) {
                    continue;
                }
            }

            self::create($admin->id, $title, $message, $type, $icon, $link, $relatedType, $relatedId);
        }
    }

    public static function notifyLogbookSubmitted(Logbook $logbook): void
    {
        $logbook->loadMissing('user');
        $studentName = $logbook->user?->nama_lengkap ?? 'Siswa';

        self::notifyAllAdmins(
            'Logbook Baru Masuk',
            "{$studentName} mengirim logbook minggu ke-{$logbook->minggu_ke} (menunggu approval).",
            'warning',
            'fa-book',
            route('admin.logbooks.index', ['status' => 'submitted']),
            'logbook',
            $logbook->id
        );
    }

    public static function notifyLocationChangeRequest(LocationChangeRequest $request): void
    {
        $request->loadMissing('user');
        $studentName = $request->user?->nama_lengkap ?? 'Siswa';

        self::notifyAllAdmins(
            'Permintaan Ubah Lokasi',
            "{$studentName} meminta perubahan titik koordinat lokasi magang.",
            'warning',
            'fa-map-pin',
            route('admin.location-requests.index'),
            'location_change_request',
            $request->id
        );
    }

    public static function markRelatedAsRead(string $relatedType, int $relatedId): void
    {
        Notification::where('related_type', $relatedType)
            ->where('related_id', $relatedId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public static function create($userId, $title, $message, $type = 'info', $icon = null, $link = null, $relatedType = null, $relatedId = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'icon' => $icon,
            'link' => $link,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
        ]);
    }

    public static function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            return true;
        }
        return false;
    }

    public static function getAdminPendingCount(): int
    {
        $logbooks = Logbook::where('status', 'submitted')->count();
        $locationRequests = LocationChangeRequest::where('status', 'pending')->count();

        return $logbooks + $locationRequests;
    }

    public static function getAdminPendingItems(): array
    {
        $items = [];

        $logbooks = Logbook::where('status', 'submitted')
            ->with('user')
            ->get()
            ->sortBy(fn ($logbook) => $logbook->user?->nama_lengkap ?? '')
            ->take(5);

        foreach ($logbooks as $logbook) {
            $items[] = [
                'type' => 'logbook',
                'id' => $logbook->id,
                'title' => 'Logbook Baru Masuk',
                'message' => ($logbook->user?->nama_lengkap ?? 'Siswa') . " — minggu ke-{$logbook->minggu_ke}",
                'link' => route('admin.logbooks.index', ['status' => 'submitted']),
                'icon' => 'fa-book',
            ];
        }

        $locationRequests = LocationChangeRequest::where('status', 'pending')
            ->with('user')
            ->get()
            ->sortBy(fn ($request) => $request->user?->nama_lengkap ?? '')
            ->take(5);

        foreach ($locationRequests as $request) {
            $items[] = [
                'type' => 'location_change_request',
                'id' => $request->id,
                'title' => 'Permintaan Ubah Lokasi',
                'message' => ($request->user?->nama_lengkap ?? 'Siswa') . ' meminta perubahan lokasi magang',
                'link' => route('admin.location-requests.index'),
                'icon' => 'fa-map-pin',
            ];
        }

        usort($items, fn ($a, $b) => strcasecmp($a['message'], $b['message']));

        return array_slice($items, 0, 5);
    }
}
