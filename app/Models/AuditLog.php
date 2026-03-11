<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'admin_user_id',
        'action',
        'entity_type',
        'entity_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id');
    }

    public static function record(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        static::create([
            'admin_user_id' => auth('admin')->id(),
            'action'        => $action,
            'entity_type'   => $entityType,
            'entity_id'     => $entityId,
            'old_values'    => $oldValues,
            'new_values'    => $newValues,
            'ip_address'    => request()->ip(),
            'user_agent'    => substr(request()->userAgent() ?? '', 0, 255),
            'created_at'    => now(),
        ]);
    }
}
