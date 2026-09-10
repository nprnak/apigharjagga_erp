<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SiteInspection;
use Illuminate\Auth\Access\HandlesAuthorization;

class SiteInspectionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SiteInspection');
    }

    public function view(AuthUser $authUser, SiteInspection $siteInspection): bool
    {
        return $authUser->can('View:SiteInspection');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SiteInspection');
    }

    public function update(AuthUser $authUser, SiteInspection $siteInspection): bool
    {
        return $authUser->can('Update:SiteInspection');
    }

    public function delete(AuthUser $authUser, SiteInspection $siteInspection): bool
    {
        return $authUser->can('Delete:SiteInspection');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SiteInspection');
    }

    public function restore(AuthUser $authUser, SiteInspection $siteInspection): bool
    {
        return $authUser->can('Restore:SiteInspection');
    }

    public function forceDelete(AuthUser $authUser, SiteInspection $siteInspection): bool
    {
        return $authUser->can('ForceDelete:SiteInspection');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SiteInspection');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SiteInspection');
    }

    public function replicate(AuthUser $authUser, SiteInspection $siteInspection): bool
    {
        return $authUser->can('Replicate:SiteInspection');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SiteInspection');
    }

}