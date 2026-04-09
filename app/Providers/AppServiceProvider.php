<?php

namespace App\Providers;

use App\Models\ProjectComment;
use App\Models\ProjectDocument;
use App\Models\ProjectMember;
use App\Models\SupportTicket;
use App\Models\User;
use App\Observers\ProjectCommentObserver;
use App\Observers\ProjectDocumentObserver;
use App\Observers\ProjectMemberObserver;
use App\Observers\SupportTicketObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        ProjectComment::observe(ProjectCommentObserver::class);
        ProjectDocument::observe(ProjectDocumentObserver::class);
        ProjectMember::observe(ProjectMemberObserver::class);
        SupportTicket::observe(SupportTicketObserver::class);

        // Super Admin ultrapassa todas as policies
        Gate::before(function ($user, string $ability) {
            if ($user instanceof User && $user->hasRole('Super Admin')) {
                return true;
            }
        });
    }
}
