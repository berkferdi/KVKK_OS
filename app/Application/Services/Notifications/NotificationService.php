<?php

namespace App\Application\Services\Notifications;

use App\Domain\Compliance\Models\AnalysisRun;
use App\Models\User;
use App\Notifications\AnalysisCompletedNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;

class NotificationService
{
    public function notifyAnalysisCompleted(AnalysisRun $run, ?User $actor = null): void
    {
        $run->loadMissing('company');

        $recipients = $this->tenantRecipients((int) $run->tenant_id);

        if ($actor !== null && ! $recipients->contains('id', $actor->id)) {
            $recipients->push($actor);
        }

        foreach ($recipients as $user) {
            if ($this->alreadyNotified($user, 'analysis:'.$run->id)) {
                continue;
            }
            $user->notify(new AnalysisCompletedNotification($run));
        }
    }

    /**
     * @return LengthAwarePaginator<int, DatabaseNotification>
     */
    public function paginateForUser(User $user, int $perPage = 20): LengthAwarePaginator
    {
        return $user->notifications()->paginate($perPage);
    }

    public function unreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    public function markAsRead(User $user, string $notificationId): ?DatabaseNotification
    {
        /** @var DatabaseNotification|null $notification */
        $notification = $user->notifications()->where('id', $notificationId)->first();
        if ($notification === null) {
            return null;
        }

        $notification->markAsRead();

        return $notification;
    }

    public function markAllAsRead(User $user): int
    {
        $count = $user->unreadNotifications()->count();
        $user->unreadNotifications->markAsRead();

        return $count;
    }

    /**
     * @return Collection<int, User>
     */
    public function tenantRecipients(int $tenantId): Collection
    {
        setPermissionsTeamId($tenantId);

        return User::query()
            ->where('is_active', true)
            ->whereHas('tenants', fn ($t) => $t->where('tenants.id', $tenantId))
            ->get()
            ->filter(function (User $user) use ($tenantId): bool {
                if ($user->is_super_admin) {
                    return true;
                }

                setPermissionsTeamId($tenantId);

                return $user->can('notifications.view');
            })
            ->values();
    }

    public function alreadyNotified(User $user, string $dedupeKey, int $withinHours = 24): bool
    {
        return $user->notifications()
            ->where('created_at', '>=', now()->subHours($withinHours))
            ->where('data->dedupe_key', $dedupeKey)
            ->exists();
    }
}
