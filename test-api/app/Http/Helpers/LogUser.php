<?php
namespace App\Http\Helpers;

use App\Models\{ActivityUser};

class LogUser
{
    public static function log_user_update($id_user, $feature) 
    {
        $activity = [
            'id_user' => $id_user,
            'activity' => $feature
        ];

        $save_activity = ActivityUser::create($activity);

        return $save_activity;
    }
}