<?php

namespace App\Custom;

use App\Asset;
use App\Notification;
use App\Stock;
use App\User;
use Carbon\Carbon;

class TechKen
{
    public $low_stock_title = 'Low Stock Item(s)';
    public $low_stock_message = '';

    public static function AddNotification($title, $message)
    {
        // Check if there are low stock items
        $low_stock_message = '';
        $found = false;

        $stocks = Stock::all();
        //return $stocks[0]->asset();
        foreach ($stocks as $stock) {
            $asset = $stock->asset;
            if ($stock->current_stock < $asset->danger_level) {
                $low_stock_message .= '<ul><li>' . $asset->name . ' current stock is ' . $stock->current_stock . '</li></ul>';
                $found = true;
            }
        }

        if ($found) {
            // If there are, add notification to each users.
            $low_stock_message .= '<p>Check Asset Stock Management for more details.</p>';

            $users = User::where('id', '!=', 1)->get();
            foreach ($users as $user) {
                $notif = Notification::create([
                    'user_id' => $user->id,
                    'title' => $title,
                    'message' => $low_stock_message,
                ]);
            }
        }

        // Clean up and remove 15 days old notifications.
        Notification::where('created_at', '<=', Carbon::now()->subDay(15))->delete();
    }
}
