<?php

namespace App\Services\Stays;

use App\Models\Owner;

class StaysAdapterFactory
{
    /**
     * Cria o adapter Stays (HTTP ou Mock) para um owner.
     */
    public static function forOwner(Owner $owner): StaysAdapterInterface
    {
        $baseUrl = $owner->stays_base_url ?? config('stays.http.base_url');
        $hasClientId = ! empty($owner->stays_client_id);

        if (! $hasClientId) {
            return new MockStaysAdapter();
        }

        return new HttpStaysAdapter(
            $baseUrl,
            $owner->stays_client_id,
            $owner->stays_client_secret ?? '',
        );
    }
}
