<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Gates a Filament resource's view/create/edit/delete access behind the
 * signed-in admin's staff role permissions, so access is configured via
 * the `roles.permissions` data instead of hardcoded per-resource checks.
 *
 * Resources using this trait must define permissionKey(), e.g. 'properties',
 * which is checked as "{key}.view" and "{key}.manage".
 */
trait AuthorizesViaRole
{
    protected static function permissionKey(): string
    {
        return 'default';
    }

    public static function canViewAny(): bool
    {
        return static::userHasPermission(static::permissionKey().'.view');
    }

    public static function canCreate(): bool
    {
        return static::userHasPermission(static::permissionKey().'.manage');
    }

    public static function canEdit(Model $record): bool
    {
        return static::userHasPermission(static::permissionKey().'.manage');
    }

    public static function canDelete(Model $record): bool
    {
        return static::userHasPermission(static::permissionKey().'.manage');
    }

    public static function canDeleteAny(): bool
    {
        return static::userHasPermission(static::permissionKey().'.manage');
    }

    protected static function userHasPermission(string $permission): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) $user?->hasPermission($permission);
    }
}
