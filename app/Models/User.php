<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'linkedin_url',
        'github_url',
        'portfolio_url',
        'phone',
        'city',
        'position',
        'job_titles',
        'skills',
        'department',
        'bio',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'job_titles' => 'array',
            'skills' => 'array',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && $panel->getId() === 'admin';
    }

    // Relationships
    public function managedClients(): HasMany
    {
        return $this->hasMany(Client::class, 'account_manager_id');
    }

    public function assignedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function leadNotes(): HasMany
    {
        return $this->hasMany(LeadNote::class, 'created_by');
    }

    public function createdQuotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'created_by');
    }

    public function managedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'project_manager_id');
    }

    public function projectMemberships(): HasMany
    {
        return $this->hasMany(ProjectMember::class, 'user_id');
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class, 'assigned_to');
    }

    public function createdTasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class, 'created_by');
    }

    public function createdDocuments(): HasMany
    {
        return $this->hasMany(ProjectDocument::class, 'created_by');
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(TimeLog::class, 'user_id');
    }
}
