<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Notification;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    /**
     * @return View
     */
    public function index()
    {
        //abort_if(Gate::denies('transaction_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user_id = auth()->user()->id;
        $notifications = Notification::where('user_id', $user_id)
            ->orderBy('id', 'desc')
            ->skip(0)->take(10)
            ->get();

        $update_notification = Notification::where('user_id', $user_id)->update([
            "read" => 1
        ]);

        return view('admin.notifications.index', compact('notifications'));
    }
}
