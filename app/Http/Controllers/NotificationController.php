<?php

namespace App\Http\Controllers;

use App\Services\Notifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/** Notifications de l'utilisateur connecté au panneau web (cloche de la barre du haut). */
class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notifier::forUser(Auth::id(), 100);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markRead(Request $request)
    {
        Notifier::markRead(Auth::id(), $request->filled('ids') ? (array) $request->ids : null);
        return $request->expectsJson() ? response()->json(['success' => true]) : back();
    }
}
