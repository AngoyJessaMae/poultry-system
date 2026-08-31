<?php

namespace App\Providers;

use App\Http\View\Composers\NotificationComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('layouts.app', NotificationComposer::class);
    }

    public function register()
    {
        //
    }
}