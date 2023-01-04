<?php

namespace App\Extensions;

use Illuminate\Auth\EloquentUserProvider;

class CacheEloquentProvider extends EloquentUserProvider
{
    public function retrieveById($identifier)
    {
        $keyCache = "users:" . $identifier;
        $user = app('cache')->get($keyCache);
        if (!$user) {
            $user = parent::retrieveById($identifier);
            if ($user) {
                app('cache')->put($keyCache, $user, 3 * 60);
            }
        }

        return $user;
    }
}
