<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Notifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Notifications du facilitateur pour l'appli mobile :
 *   GET  /api/notifications        → 50 dernières + nombre non lues
 *   POST /api/notifications/lues   → ids[] (ou rien = toutes) marquées lues
 */
class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return [
            'status' => 'ok',
            'non_lues' => Notifier::unreadCount($user->id),
            'notifications' => Notifier::forUser($user->id)->map(fn ($n) => [
                'id' => $n->id,
                'type' => $n->type,
                'titre' => $n->titre,
                'message' => $n->message,
                'report_table' => $n->report_table,
                'report_id' => $n->report_id,
                'mobile_ref' => $n->mobile_ref,
                'lu' => $n->lu_le !== null,
                'date' => (string) $n->created_at,
            ])->values(),
        ];
    }

    public function markRead(Request $request)
    {
        $count = Notifier::markRead(Auth::id(), $request->filled('ids') ? (array) $request->ids : null);
        return ['status' => 'ok', 'count' => $count];
    }
}
