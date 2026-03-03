<?php

namespace App\Providers;

use App\Services\Stays\HttpStaysAdapter;
use App\Services\Stays\MockStaysAdapter;
use App\Services\Stays\StaysAdapterInterface;
use Illuminate\Support\ServiceProvider;

class StaysServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(StaysAdapterInterface::class, function ($app) {
            $adapter = config('stays.adapter', 'mock');
            
            if ($adapter === 'http') {
                return new HttpStaysAdapter(config('stays.http'));
            }
            
            return new MockStaysAdapter();
        });
    }

    public function boot(): void
    {
        //
    }
}
