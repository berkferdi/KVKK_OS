<?php

namespace App\Http\Controllers\Web\Notifications;

use App\Application\Services\Notifications\NotificationService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $notifications,
    ) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()?->can('notifications.view') || $request->user()?->is_super_admin, 403);

        /** @var User $user */
        $user = $request->user();

        return view('notifications.index', [
            'notifications' => $this->notifications->paginateForUser($user),
            'unreadCount' => $this->notifications->unreadCount($user),
        ]);
    }

    public function markRead(Request $request, string $notification): RedirectResponse
    {
        abort_unless($request->user()?->can('notifications.view') || $request->user()?->is_super_admin, 403);

        /** @var User $user */
        $user = $request->user();
        $found = $this->notifications->markAsRead($user, $notification);
        abort_if($found === null, 404);

        $url = data_get($found->data, 'url');

        if (is_string($url) && $url !== '') {
            return redirect()->to($url)->with('success', 'Bildirim okundu.');
        }

        return redirect()
            ->route('notifications.index')
            ->with('success', 'Bildirim okundu.');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->can('notifications.view') || $request->user()?->is_super_admin, 403);

        /** @var User $user */
        $user = $request->user();
        $count = $this->notifications->markAllAsRead($user);

        return redirect()
            ->route('notifications.index')
            ->with('success', "{$count} bildirim okundu olarak işaretlendi.");
    }
}
