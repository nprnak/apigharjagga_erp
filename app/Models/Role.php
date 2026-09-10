<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Staff role lookup (Admin, Manager, Engineer, ...) with a dynamic
 * list of permission keys that drives admin-panel resource access.
 *
 * @property int $role_id
 * @property string $role_name
 * @property array<int, string>|null $permissions
 */
class Role extends Model
{
    protected $primaryKey = 'role_id';

    public $timestamps = false;

    protected $fillable = ['role_name', 'permissions'];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }

    public function hasPermission(string $permission): bool
    {
        $permissions = $this->permissions ?? [];

        return in_array('*', $permissions, true) || in_array($permission, $permissions, true);
    }

    /**
     * Permission keys assignable to a role, keyed for use as Filament checkbox options.
     * Add an entry here whenever a new admin resource needs its own view/manage gate.
     */
    public static function availablePermissions(): array
    {
        return [
            '*' => 'Full Access (Super Admin)',
            'users.view' => 'View Users',
            'users.manage' => 'Manage Users',
            'clients.view' => 'View Clients',
            'clients.manage' => 'Manage Clients',
            'properties.view' => 'View Properties',
            'properties.manage' => 'Manage Properties',
            'properties.approve' => 'Approve / Reject Property Listings',
            'inquiries.view' => 'View Inquiries',
            'inquiries.manage' => 'Manage Inquiries',
            'kyc.view' => 'View KYC Verifications',
            'kyc.manage' => 'Manage KYC Verifications',
            'staff.view' => 'View Staff Directory',
            'staff.manage' => 'Manage Staff Directory',
            'complaints.view' => 'View Complaints',
            'complaints.manage' => 'Manage Complaints',
            'complaints.assign' => 'Assign Complaints',
            'complaints.resolve' => 'Resolve Complaints',
            'valuations.view' => 'View Valuation & Survey Requests',
            'valuations.manage' => 'Manage Valuation & Survey Requests',
            'valuations.assign' => 'Assign Valuer/Surveyor',
            'valuations.conduct' => 'Conduct Valuation (Draft Report)',
            'valuations.review' => 'Review & Approve Valuation Report',
            'verifications.view' => 'View Property Verifications',
            'verifications.manage' => 'Manage Property Verifications',
            'inspections.view' => 'View Site Inspections',
            'inspections.manage' => 'Manage Site Inspections',
            'inspections.schedule' => 'Schedule Site Inspections',
            'inspections.conduct' => 'Conduct Inspection & Upload Report',
            'agreements.view' => 'View / Track Agreements',
            'agreements.manage' => 'Create & Edit Agreements',
            'agreements.review' => 'Review Agreements',
            'handovers.view' => 'View Property Handovers',
            'handovers.manage' => 'Manage Property Handovers',
            'service_orders.view' => 'View Service Orders',
            'service_orders.manage' => 'Manage Service Orders',
            'completions.view' => 'View Service Completion Certificates',
            'completions.manage' => 'Manage Service Completion Certificates',
            'payments.view' => 'View Payment Receipts',
            'payments.manage' => 'Manage Payment Receipts',
            'system.audit_logs' => 'View Audit Logs',
        ];
    }
}
