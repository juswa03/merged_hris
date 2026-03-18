<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index()
    {
        $user          = auth()->user();
        $notifications = $user->notifications()->paginate(20);
        $unreadCount   = $user->unreadNotifications()->count();

        return view('admin.notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markRead(string $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return back();
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(string $id)
    {
        auth()->user()->notifications()->findOrFail($id)->delete();
        return back()->with('success', 'Notification deleted.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required|string|max:200',
            'message'   => 'required|string|max:1000',
            'type'      => 'required|in:info,success,warning,danger',
            'target'    => 'required|in:all,admins,single',
            'user_id'   => 'required_if:target,single|nullable|exists:tbl_users,id',
        ]);

        $notification = new SystemNotification(
            title:   $request->title,
            message: $request->message,
            type:    $request->type,
            link:    $request->link ?: null,
        );

        $recipients = match ($request->target) {
            'all'    => User::all(),
            'admins' => User::whereHas('role', fn ($q) => $q->where('name', 'Admin'))->get(),
            'single' => User::where('id', $request->user_id)->get(),
        };

        foreach ($recipients as $recipient) {
            $recipient->notify($notification);
        }

        return back()->with('success', "Notification sent to {$recipients->count()} recipient(s).");
    }

    public function unreadCount()
    {
        return response()->json([
            'count' => auth()->user()->unreadNotifications()->count(),
        ]);
    }

    public function recent()
    {
        $notifications = auth()->user()->notifications()->latest()->take(6)->get()->map(function ($n) {
            return [
                'id'         => $n->id,
                'title'      => $n->data['title'] ?? 'Notification',
                'message'    => $n->data['message'] ?? '',
                'type'       => $n->data['type'] ?? 'info',
                'link'       => $n->data['link'] ?? null,
                'read'       => !is_null($n->read_at),
                'time'       => $n->created_at->diffForHumans(),
            ];
        });

        return response()->json($notifications);
    }
}
