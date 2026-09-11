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
            'kyc.verify' => 'Verify KYC Submissions (Stage 1)',
            'kyc.approve' => 'Approve KYC Verifications (Stage 2)',
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
            'finance_accounts.view' => 'View Chart of Accounts',
            'finance_accounts.manage' => 'Manage Chart of Accounts',
            'budgets.view' => 'View Budgets',
            'budgets.manage' => 'Manage Budgets',
            'invoices.view' => 'View Invoices',
            'invoices.manage' => 'Manage Invoices',
            'vouchers.view' => 'View Payment Vouchers',
            'vouchers.manage' => 'Manage Payment Vouchers',
            'vouchers.approve' => 'Approve Payment Vouchers',
            'finance_reports.view' => 'View Cash Book, Ledger, P&L & Balance Sheet',
            'attendance.view' => 'View Attendance',
            'attendance.manage' => 'Manage Attendance',
            'leave.view' => 'View Leave Requests',
            'leave.manage' => 'Manage Leave Requests',
            'leave.approve' => 'Approve / Reject Leave Requests',
            'payroll.view' => 'View Payroll',
            'payroll.manage' => 'Manage Payroll (Generate Payslips)',
            'payroll.approve' => 'Finalize Payroll & Post to Ledger',
            'performance.view' => 'View Performance Reviews',
            'performance.manage' => 'Manage Performance Reviews',
            'projects.view' => 'View Engineering/Construction Projects',
            'projects.manage' => 'Manage Projects (Create/Edit, Milestones, BOQ)',
            'projects.log' => 'Log Site Visits, Progress, Materials & Inspections',
            'contractors.view' => 'View Contractors',
            'contractors.manage' => 'Manage Contractors',
            'content.view' => 'View Website Content (Team, Testimonials, Blog, Gallery)',
            'content.manage' => 'Manage Website Content (Team, Testimonials, Blog, Gallery)',
            'careers.view' => 'View Job Openings & Applications',
            'careers.manage' => 'Manage Job Openings & Applications',
            'site_documents.view' => 'View Public Document Downloads',
            'site_documents.manage' => 'Manage Public Document Downloads',
            'contact_messages.view' => 'View Contact Messages',
            'contact_messages.manage' => 'Manage Contact Messages',
            'poa.view' => 'View Power of Attorney Submissions',
            'poa.manage' => 'Verify / Approve Power of Attorney',
        ];
    }
}
