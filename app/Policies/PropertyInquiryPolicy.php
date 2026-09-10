<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PropertyInquiry;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyInquiryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PropertyInquiry');
    }

    public function view(AuthUser $authUser, PropertyInquiry $propertyInquiry): bool
    {
        return $authUser->can('View:PropertyInquiry');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PropertyInquiry');
    }

    public function update(AuthUser $authUser, PropertyInquiry $propertyInquiry): bool
    {
        return $authUser->can('Update:PropertyInquiry');
    }

    public function delete(AuthUser $authUser, PropertyInquiry $propertyInquiry): bool
    {
        return $authUser->can('Delete:PropertyInquiry');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PropertyInquiry');
    }

    public function restore(AuthUser $authUser, PropertyInquiry $propertyInquiry): bool
    {
        return $authUser->can('Restore:PropertyInquiry');
    }

    public function forceDelete(AuthUser $authUser, PropertyInquiry $propertyInquiry): bool
    {
        return $authUser->can('ForceDelete:PropertyInquiry');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PropertyInquiry');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PropertyInquiry');
    }

    public function replicate(AuthUser $authUser, PropertyInquiry $propertyInquiry): bool
    {
        return $authUser->can('Replicate:PropertyInquiry');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PropertyInquiry');
    }

}