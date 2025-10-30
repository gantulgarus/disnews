<?php

namespace App\Providers;

use App\Models\TelephoneMessage;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.header', function ($view) {
            $userOrgId = auth()->user()->organization_id;

            $newMessages = TelephoneMessage::whereHas('receivers', function ($q) use ($userOrgId) {
                $q->where('organization_id', $userOrgId)
                    ->where('status', 'Шинээр ирсэн');
            })->with('senderOrganization')->latest()->get();

            $newMessagesCount = $newMessages->count();

            $view->with(compact('newMessages', 'newMessagesCount'));
        });

        Paginator::useBootstrap();
    }
}
