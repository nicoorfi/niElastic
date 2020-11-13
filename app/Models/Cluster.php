<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Cluster extends Model
{
    use SoftDeletes;
    use HasApiTokens;
    use HasFactory;

    public const QUEUED_DESTROY = 'queued_destroy';

    public const QUEUED_CREATE = 'queued_create';

    public const CREATED = 'created';

    public const RUNNING = 'running';

    public const DESTROYED = 'destroyed';

    public const FAILED = 'failed';

    protected $casts = [
        'admin_token_active' => 'boolean',
        'search_token_active' => 'boolean'
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function isAdminTokenActive(): bool
    {
        return $this->getAttribute('admin_token_active');
    }

    public function isSearchTokenActive(): bool
    {
        return $this->getAttribute('search_token_active');
    }

    public function tokens()
    {
        return $this->morphMany(Token::class, 'tokenable');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function findUser()
    {
        return $this->getAttribute('project')->getAttribute('user');
    }

    public function isOwnedBy(User $user)
    {
        return $this->getAttribute('project')->user->id === $user->id;
    }
}
