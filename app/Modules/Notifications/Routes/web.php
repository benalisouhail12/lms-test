<?php

use Illuminate\Support\Facades\Route;

Route::prefix('notifications')->group(function () {
    Route::get('/unsubscribe', function () {
        // Simple unsubscribe handler
        $userId = decrypt(request('token'));

        if ($userId) {
            $preferences = \App\Modules\Notifications\Models\NotificationPreference::where('user_id', $userId)->first();

            if ($preferences) {
                $channels = $preferences->channels;
                $channels['email']['enabled'] = false;
                $preferences->channels = $channels;
                $preferences->save();

                return view('notifications::unsubscribe_success');
            }
        }

        return view('notifications::unsubscribe_error');
    });

    Route::get('/preferences', function () {
        // Redirect to preferences page in the app
        return redirect('/account/preferences');
    });
});
