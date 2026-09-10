<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Each role's permissions list drives which admin-panel resources it
     * can view ("{key}.view"), create/edit/delete ("{key}.manage"), or
     * perform a specific workflow action on ("{key}.assign", ".conduct",
     * ".review", ".approve", ".resolve", ".schedule"). '*' grants
     * unrestricted access. This roster mirrors the company's org chart —
     * one row per job title reporting into Top Management, Finance,
     * Technical, Sales/Brokerage, Customer Service, Legal/Documentation,
     * or IT/Admin. Edit this map (or the Role model's data) to change
     * access dynamically without touching resource code.
     */
    public function run(): void
    {
        $roles = [
            // Top Management
            'Admin' => ['*'],
            'Managing Director' => [
                'users.view', 'clients.view',
                'properties.view', 'properties.approve',
                'valuations.view', 'valuations.review',
                'verifications.view', 'inspections.view',
                'agreements.view', 'agreements.review',
                'complaints.view',
                'handovers.view', 'service_orders.view', 'completions.view',
                'payments.view', 'staff.view',
                'system.audit_logs',
                'finance_accounts.view', 'budgets.view', 'invoices.view',
                'vouchers.view', 'finance_reports.view',
                'attendance.view', 'leave.view', 'payroll.view', 'performance.view',
                'projects.view', 'contractors.view',
            ],
            'General Manager' => [
                'users.view', 'clients.view', 'clients.manage',
                'properties.view', 'properties.manage', 'properties.approve',
                'inquiries.view', 'inquiries.manage',
                'kyc.view', 'kyc.manage',
                'staff.view', 'staff.manage',
                'complaints.view', 'complaints.assign',
                'valuations.view', 'valuations.assign', 'valuations.review',
                'verifications.view', 'verifications.manage',
                'inspections.view', 'inspections.schedule',
                'agreements.view', 'agreements.manage', 'agreements.review',
                'handovers.view', 'service_orders.view', 'service_orders.manage',
                'completions.view', 'completions.manage',
                'payments.view',
                'system.audit_logs',
                'finance_accounts.view', 'budgets.view', 'invoices.view',
                'vouchers.view', 'finance_reports.view',
                'attendance.view', 'attendance.manage',
                'leave.view', 'leave.manage', 'leave.approve',
                'performance.view', 'performance.manage',
                'payroll.view',
                'projects.view', 'projects.manage', 'contractors.view', 'contractors.manage',
                'content.view', 'careers.view', 'site_documents.view', 'contact_messages.view',
            ],

            // Finance & Accounts
            'Finance Manager' => [
                'clients.view', 'clients.manage',
                'staff.view',
                'agreements.view',
                'payments.view', 'payments.manage',
                'finance_accounts.view', 'finance_accounts.manage',
                'budgets.view', 'budgets.manage',
                'invoices.view', 'invoices.manage',
                'vouchers.view', 'vouchers.manage', 'vouchers.approve',
                'finance_reports.view',
                'payroll.view', 'payroll.manage', 'payroll.approve',
            ],

            // Marketing Department
            'Marketing Manager' => [
                'properties.view', 'properties.manage',
                'inquiries.view',
                'content.view', 'content.manage',
                'careers.view', 'careers.manage',
                'site_documents.view', 'site_documents.manage',
                'contact_messages.view', 'contact_messages.manage',
            ],

            // Technical Department
            'Technical Manager' => [
                'users.view', 'clients.view', 'staff.view', 'staff.manage',
                'properties.view', 'properties.manage', 'properties.approve',
                'valuations.view', 'valuations.assign', 'valuations.review',
                'verifications.view', 'verifications.manage',
                'inspections.view', 'inspections.schedule',
                'complaints.view',
                'projects.view', 'projects.manage', 'projects.log',
                'contractors.view', 'contractors.manage',
            ],
            'Site Engineer / Survey Officer' => [
                'properties.view',
                'inspections.view', 'inspections.conduct',
                'valuations.view',
                'complaints.view',
                'projects.view', 'projects.log',
            ],
            'Valuation Officer' => [
                'clients.view', 'properties.view',
                'valuations.view', 'valuations.conduct',
                'inspections.view',
                'complaints.view',
            ],
            'Survey Coordinator' => [
                'properties.view',
                'valuations.view', 'valuations.assign',
                'inspections.view', 'inspections.schedule',
                'verifications.view',
            ],

            // Sales/Brokerage
            'Sales/Brokerage Representative' => [
                'clients.view', 'clients.manage',
                'properties.view', 'properties.manage',
                'valuations.view',
                'agreements.view', 'agreements.manage',
                'complaints.view',
            ],

            // Customer Service
            'Customer Support Officer' => [
                'clients.view', 'clients.manage',
                'kyc.view', 'kyc.manage',
                'properties.view',
                'inquiries.view', 'inquiries.manage',
                'complaints.view', 'complaints.assign', 'complaints.resolve',
                'handovers.view', 'service_orders.view',
                'completions.view', 'completions.manage',
                'contact_messages.view', 'contact_messages.manage',
            ],
            'Receptionist / Front Desk Officer' => [
                'clients.view', 'clients.manage',
                'properties.view',
                'inquiries.view', 'inquiries.manage',
            ],

            // Legal/Documentation
            'Document Officer / Legal Coordinator' => [
                'clients.view',
                'kyc.view', 'kyc.manage',
                'verifications.view', 'verifications.manage',
                'agreements.view', 'agreements.manage',
            ],

            // IT & Admin
            'IT Support / System Administrator' => [
                'staff.view',
                'system.audit_logs',
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            DB::table('roles')->updateOrInsert(
                ['role_name' => $roleName],
                ['permissions' => json_encode($permissions)]
            );
        }
    }
}
