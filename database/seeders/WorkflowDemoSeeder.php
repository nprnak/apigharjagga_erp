<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Agreement;
use App\Models\AgreementParty;
use App\Models\AgreementWitness;
use App\Models\Attendance;
use App\Models\BlogPost;
use App\Models\BoqItem;
use App\Models\Budget;
use App\Models\Client;
use App\Models\Complaint;
use App\Models\ComplaintEvidence;
use App\Models\ContactMessage;
use App\Models\Contractor;
use App\Models\FinanceAccount;
use App\Models\FinanceTransaction;
use App\Models\GalleryAlbum;
use App\Models\GalleryImage;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\KycVerification;
use App\Models\LeaveRequest;
use App\Models\MaterialRecord;
use App\Models\PaymentReceipt;
use App\Models\PaymentVoucher;
use App\Models\PayrollRun;
use App\Models\Payslip;
use App\Models\PerformanceReview;
use App\Models\PowerOfAttorney;
use App\Models\Project;
use App\Models\ProjectContractor;
use App\Models\ProjectInspectionReport;
use App\Models\ProjectMilestone;
use App\Models\ProjectProgressLog;
use App\Models\ProjectSiteVisit;
use App\Models\Property;
use App\Models\PropertyHandoverCertificate;
use App\Models\PropertyInquiry;
use App\Models\PropertyListing;
use App\Models\PropertyPhoto;
use App\Models\PropertyVerification;
use App\Models\Role;
use App\Models\ServiceCompletionCertificate;
use App\Models\ServiceOrder;
use App\Models\SiteDocument;
use App\Models\SiteInspection;
use App\Models\Staff;
use App\Models\StaffContract;
use App\Models\StaffDocument;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\ValuationReport;
use App\Models\ValuationRequest;
use Carbon\CarbonInterface;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Populates every workflow-facing table with realistic, cross-linked demo
 * data so the 19 seeded logins (UserSeeder) each have something real to see
 * and act on — not just an empty list. Deliberately keyed as one all-or-
 * nothing unit (guarded by a single marker check) rather than per-row
 * upserts, since the point is a coherent, interlinked demo dataset — a
 * partial re-run halfway through would leave dangling references.
 *
 * Re-run safety: if the marker client (CLI-DEMO-OWNER) already exists, the
 * whole seeder is skipped rather than duplicating everything.
 */
class WorkflowDemoSeeder extends Seeder
{
    /** @var array<string, Staff> role_name => Staff */
    private array $staff = [];

    /** @var array<string, User> email => User */
    private array $users = [];

    public function run(): void
    {
        if (Client::where('client_code', 'CLI-DEMO-BUYER')->exists()) {
            $this->command->info('WorkflowDemoSeeder: demo data already present, skipping.');

            return;
        }

        DB::transaction(function () {
            $this->loadDemoUsers();
            $this->seedStaff();
            $this->seedFinanceOpeningBalance();

            [$clients, $kycOwner, $kycBuyer] = $this->seedClientsAndKyc();
            $properties = $this->seedPropertiesAndListings($clients);
            $this->seedInspectionsAndVerifications($properties);
            $this->seedValuations($clients, $properties);
            $this->seedAgreementsAndPayments($clients, $properties);
            $this->seedComplaints($clients, $properties);
            $this->seedServiceOrdersAndHandover($clients, $properties);
            $this->seedInquiries($properties);
            $this->seedPowerOfAttorney($clients);
            $this->seedFinanceExtras($clients, $properties);
            $this->seedHr();
            $this->seedProjects($clients, $properties);
            $this->seedCms();
        });

        $this->command->info('WorkflowDemoSeeder: demo workflow data seeded across all modules.');
    }

    private function loadDemoUsers(): void
    {
        foreach (
            [
                'admin@apigharjagga.com', 'md@apigharjagga.com', 'manager@apigharjagga.com',
                'finance@apigharjagga.com', 'marketing@apigharjagga.com', 'technical@apigharjagga.com',
                'engineer@apigharjagga.com', 'valuation@apigharjagga.com', 'survey@apigharjagga.com',
                'sales@apigharjagga.com', 'support@apigharjagga.com', 'reception@apigharjagga.com',
                'documents@apigharjagga.com', 'itsupport@apigharjagga.com',
                'user@apigharjagga.com', 'buyer@apigharjagga.com', 'investor@apigharjagga.com',
                'tenant@apigharjagga.com', 'agent@apigharjagga.com',
            ] as $email
        ) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $this->users[$email] = $user;
            }
        }
    }

    /**
     * One Staff record per org-chart role (the internal employee directory
     * used by HR/Projects/Finance — separate from the admin login accounts
     * in UserSeeder, since Staff and User are intentionally unlinked).
     */
    private function seedStaff(): void
    {
        $roster = [
            'Admin' => ['Anish Karki', '2021-01-05', 'Administration', 'full_time', 60000],
            'Managing Director' => ['Bishnu Prasad Adhikari', '2018-03-01', 'Executive', 'full_time', 250000],
            'General Manager' => ['Sunita Rana', '2019-06-15', 'Operations', 'full_time', 150000],
            'Finance Manager' => ['Rajesh Basnet', '2019-09-01', 'Finance', 'full_time', 120000],
            'Marketing Manager' => ['Priya Maharjan', '2020-02-10', 'Marketing', 'full_time', 95000],
            'Technical Manager' => ['Suresh Tamang', '2019-11-20', 'Engineering', 'full_time', 110000],
            'Site Engineer / Survey Officer' => ['Kiran Thapa', '2021-04-12', 'Engineering', 'full_time', 65000],
            'Valuation Officer' => ['Anita Shrestha', '2020-07-01', 'Valuation', 'full_time', 70000],
            'Survey Coordinator' => ['Dipesh Lama', '2021-08-15', 'Engineering', 'full_time', 60000],
            'Sales/Brokerage Representative' => ['Nabin Gurung', '2022-01-10', 'Sales', 'full_time', 55000],
            'Customer Support Officer' => ['Sabina Khadka', '2022-03-05', 'Customer Support', 'full_time', 45000],
            'Receptionist / Front Desk Officer' => ['Manisha Poudel', '2022-05-20', 'Administration', 'full_time', 35000],
            'Document Officer / Legal Coordinator' => ['Ramesh Bhattarai', '2020-10-01', 'Legal', 'full_time', 75000],
            'IT Support / System Administrator' => ['Sagar Shahi', '2021-06-01', 'IT', 'full_time', 65000],
        ];

        $seq = 1;
        foreach ($roster as $roleName => [$fullName, $doj, $department, $employmentType, $salary]) {
            $roleId = Role::where('role_name', $roleName)->value('role_id');

            $staff = Staff::updateOrCreate(
                ['employee_code' => sprintf('EMP-%03d', $seq)],
                [
                    'role_id' => $roleId,
                    'full_name' => $fullName,
                    'designation' => $roleName,
                    'department' => $department,
                    'employment_type' => $employmentType,
                    'date_of_joining' => $doj,
                    'basic_salary' => $salary,
                    'date_of_birth' => Carbon::parse($doj)->subYears(28)->toDateString(),
                    'gender' => $seq % 3 === 0 ? 'female' : ($seq % 2 === 0 ? 'male' : 'female'),
                    'address' => 'Kathmandu, Nepal',
                    'emergency_contact_name' => 'Family Contact',
                    'emergency_contact_phone' => '98'.rand(10000000, 99999999),
                    'mobile_no' => '98'.rand(10000000, 99999999),
                    'email' => 'staff.'.str()->slug($fullName).'@apigharjagga.com',
                    'is_active' => true,
                ],
            );

            $this->staff[$roleName] = $staff;
            $seq++;
        }
    }

    /**
     * Debit Cash / Credit Owner's Equity — a starting cash balance so
     * payroll and vouchers seeded later don't push the Cash Book negative.
     */
    private function seedFinanceOpeningBalance(): void
    {
        if (FinanceTransaction::where('reference_type', 'opening_balance')->exists()) {
            return;
        }

        $cash = FinanceAccount::where('account_code', FinanceAccount::CODE_CASH)->first();
        $equity = FinanceAccount::where('account_code', FinanceAccount::CODE_OWNERS_EQUITY)->first();

        if (! $cash || ! $equity) {
            return;
        }

        FinanceTransaction::postBalanced(
            [
                'transaction_date' => Carbon::now()->subMonths(3)->startOfMonth()->toDateString(),
                'reference_type' => 'opening_balance',
                'reference_id' => 0,
                'description' => 'Opening cash balance (demo data)',
                'created_by_staff_id' => $this->staff['Finance Manager']->staff_id,
            ],
            [
                ['account_id' => $cash->account_id, 'debit' => 3000000],
                ['account_id' => $equity->account_id, 'credit' => 3000000],
            ],
        );
    }

    private function address(string $tole, string $municipality, string $district, string $province, string $ward = '05'): Address
    {
        return Address::create([
            'province' => $province,
            'district' => $district,
            'municipality' => $municipality,
            'ward_no' => $ward,
            'tole_locality' => $tole,
            'full_address_text' => "{$tole}, {$municipality}, {$district}, {$province}",
            'gps_verified' => false,
        ]);
    }

    /**
     * @return array{0: array<string, Client>, 1: KycVerification|null, 2: KycVerification|null}
     */
    private function seedClientsAndKyc(): array
    {
        $ownerUser = $this->users['user@apigharjagga.com'] ?? null;
        $buyerUser = $this->users['buyer@apigharjagga.com'] ?? null;

        // Owner demo — resolvedClient() picks the FIRST client whose
        // mobile_app_user_id matches, so if one already exists for this user
        // (e.g. from an earlier self-service property listing), reuse it
        // rather than creating a second one that would never actually be
        // the one MyAgreement/MyPayment resolve to.
        $ownerClient = $ownerUser
            ? Client::where('mobile_app_user_id', (string) $ownerUser->id)->first()
            : null;

        if (! $ownerClient) {
            $ownerClient = Client::create([
                'client_code' => 'CLI-DEMO-OWNER',
                'client_type' => 'owner',
                'full_name' => 'Ramesh Bahadur Shrestha',
                'father_mother_name' => 'Krishna Bahadur Shrestha',
                'citizenship_no' => '27-01-70-00101',
                'nationality' => 'Nepali',
                'date_of_birth' => '1985-04-12',
                'gender' => 'male',
                'occupation' => 'Business',
                'mobile_no' => '9841000001',
                'email' => $ownerUser?->email,
                'mobile_app_user_id' => $ownerUser ? (string) $ownerUser->id : null,
                'registration_date' => Carbon::now()->subMonths(6)->toDateString(),
                'mis_entry_status' => 'completed',
                'is_active' => true,
            ]);
        }

        // Buyer demo — linked via citizenship-number matching against a KYC
        // record (the other resolvedClient() path), rather than the direct link.
        $buyerClient = Client::create([
            'client_code' => 'CLI-DEMO-BUYER',
            'client_type' => 'buyer',
            'full_name' => 'Sita Kumari Gurung',
            'father_mother_name' => 'Him Bahadur Gurung',
            'citizenship_no' => '27-01-72-00202',
            'nationality' => 'Nepali',
            'date_of_birth' => '1990-08-20',
            'gender' => 'female',
            'occupation' => 'Service',
            'mobile_no' => '9841000002',
            'email' => $buyerUser?->email,
            'registration_date' => Carbon::now()->subMonths(4)->toDateString(),
            'mis_entry_status' => 'completed',
            'is_active' => true,
        ]);

        // A second owner, unlinked to any web login — represents a walk-in
        // seller (Client can validly exist with no User account at all) and
        // is the owner the Agent-POA flow authorizes access to below.
        $ownerNoLogin = Client::create([
            'client_code' => 'CLI-DEMO-OWNER-02',
            'client_type' => 'owner',
            'full_name' => 'Gopal Prasad Neupane',
            'citizenship_no' => '27-01-68-00303',
            'nationality' => 'Nepali',
            'date_of_birth' => '1975-01-15',
            'gender' => 'male',
            'occupation' => 'Retired',
            'mobile_no' => '9841000003',
            'registration_date' => Carbon::now()->subMonths(8)->toDateString(),
            'mis_entry_status' => 'completed',
            'is_active' => true,
        ]);

        $ownerThree = Client::create([
            'client_code' => 'CLI-DEMO-OWNER-03',
            'client_type' => 'owner',
            'full_name' => 'Deepak Man Shakya',
            'citizenship_no' => '27-01-71-00404',
            'nationality' => 'Nepali',
            'date_of_birth' => '1982-11-02',
            'gender' => 'male',
            'occupation' => 'Business',
            'mobile_no' => '9841000004',
            'registration_date' => Carbon::now()->subMonths(10)->toDateString(),
            'mis_entry_status' => 'completed',
            'is_active' => true,
        ]);

        $buyerTwo = Client::create([
            'client_code' => 'CLI-DEMO-BUYER-02',
            'client_type' => 'buyer',
            'full_name' => 'Anjali Tamang',
            'citizenship_no' => '27-01-74-00505',
            'nationality' => 'Nepali',
            'date_of_birth' => '1993-05-30',
            'gender' => 'female',
            'occupation' => 'Service',
            'mobile_no' => '9841000005',
            'registration_date' => Carbon::now()->subMonths(2)->toDateString(),
            'mis_entry_status' => 'completed',
            'is_active' => true,
        ]);

        // KYC — owner and buyer demo logins are approved (unblocks their
        // portal features); a third pending record lets you test the admin
        // KYC approve/reject workflow itself. Keyed on user_id so a login
        // that already has a KYC record (e.g. the owner demo, from earlier
        // seed data) isn't given a confusing second one.
        $kycOwner = $ownerUser ? KycVerification::firstOrCreate(
            ['user_id' => $ownerUser->id],
            [
                'full_name' => $ownerClient->full_name,
                'citizenship_no' => $ownerClient->citizenship_no,
                'date_of_birth' => $ownerClient->date_of_birth,
                'gender' => 'male',
                'nationality' => 'Nepali',
                'mobile_no' => $ownerClient->mobile_no,
                'email' => $ownerUser->email,
                'permanent_province' => 'Bagmati',
                'permanent_district' => 'Kathmandu',
                'permanent_municipality' => 'Kathmandu Metropolitan City',
                'permanent_ward_no' => '10',
                'id_document_path' => 'demo/kyc/owner-citizenship.jpg',
                'id_type' => 'citizenship',
                'status' => 'approved',
                'submitted_at' => Carbon::now()->subMonths(6),
                'reviewed_at' => Carbon::now()->subMonths(6)->addDay(),
            ],
        ) : null;

        $kycBuyer = $buyerUser ? KycVerification::firstOrCreate(
            ['user_id' => $buyerUser->id],
            [
                'full_name' => $buyerClient->full_name,
                'citizenship_no' => $buyerClient->citizenship_no,
                'date_of_birth' => $buyerClient->date_of_birth,
                'gender' => 'female',
                'nationality' => 'Nepali',
                'mobile_no' => $buyerClient->mobile_no,
                'email' => $buyerUser->email,
                'permanent_province' => 'Bagmati',
                'permanent_district' => 'Lalitpur',
                'permanent_municipality' => 'Lalitpur Metropolitan City',
                'permanent_ward_no' => '05',
                'id_document_path' => 'demo/kyc/buyer-citizenship.jpg',
                'id_type' => 'citizenship',
                'status' => 'approved',
                'submitted_at' => Carbon::now()->subMonths(4),
                'reviewed_at' => Carbon::now()->subMonths(4)->addDay(),
            ],
        ) : null;

        if ($investorUser = $this->users['investor@apigharjagga.com'] ?? null) {
            KycVerification::firstOrCreate(
                ['user_id' => $investorUser->id],
                [
                    'full_name' => 'Investor Demo Account',
                    'citizenship_no' => '27-01-80-00606',
                    'date_of_birth' => '1988-02-14',
                    'gender' => 'male',
                    'nationality' => 'Nepali',
                    'mobile_no' => '9841000006',
                    'email' => $investorUser->email,
                    'permanent_province' => 'Bagmati',
                    'permanent_district' => 'Kathmandu',
                    'permanent_municipality' => 'Kathmandu Metropolitan City',
                    'permanent_ward_no' => '15',
                    'id_document_path' => 'demo/kyc/investor-citizenship.jpg',
                    'id_type' => 'citizenship',
                    'status' => 'pending',
                    'submitted_at' => Carbon::now()->subDays(2),
                ],
            );
        }

        return [
            [
                'owner' => $ownerClient,
                'buyer' => $buyerClient,
                'owner_no_login' => $ownerNoLogin,
                'owner_three' => $ownerThree,
                'buyer_two' => $buyerTwo,
            ],
            $kycOwner,
            $kycBuyer,
        ];
    }

    /**
     * @param  array<string, Client>  $clients
     * @return array<string, Property>
     */
    private function seedPropertiesAndListings(array $clients): array
    {
        $ownerUser = $this->users['user@apigharjagga.com'] ?? null;
        $properties = [];

        $defs = [
            'self_listed_sale' => [
                'owner' => $clients['owner'], 'linkUser' => $ownerUser, 'code' => 'PROP-DEMO-001',
                'type' => 'house', 'status' => 'listed', 'approval' => 'approved', 'listed' => true,
                'address' => ['Baluwatar', 'Kathmandu Metropolitan City', 'Kathmandu', 'Bagmati'],
                'purpose' => 'sale', 'price' => 32000000, 'app' => 'AGJ-DEMO-0001',
            ],
            'self_listed_rent' => [
                'owner' => $clients['owner'], 'linkUser' => null, 'code' => 'PROP-DEMO-002',
                'type' => 'apartment', 'status' => 'rented', 'approval' => 'approved', 'listed' => true,
                'address' => ['Jhamsikhel', 'Lalitpur Metropolitan City', 'Lalitpur', 'Bagmati'],
                'purpose' => 'rent', 'price' => 45000, 'app' => 'AGJ-DEMO-0002',
            ],
            'owner2_lease' => [
                'owner' => $clients['owner_no_login'], 'linkUser' => null, 'code' => 'PROP-DEMO-003',
                'type' => 'commercial_building', 'status' => 'leased', 'approval' => 'approved', 'listed' => true,
                'address' => ['New Road', 'Kathmandu Metropolitan City', 'Kathmandu', 'Bagmati'],
                'purpose' => 'lease', 'price' => 180000, 'app' => 'AGJ-DEMO-0003',
            ],
            'owner3_investment' => [
                'owner' => $clients['owner_three'], 'linkUser' => null, 'code' => 'PROP-DEMO-004',
                'type' => 'land', 'status' => 'listed', 'approval' => 'approved', 'listed' => true,
                'address' => ['Godawari', 'Lalitpur Metropolitan City', 'Lalitpur', 'Bagmati'],
                'purpose' => 'investment', 'price' => 15000000, 'app' => 'AGJ-DEMO-0004',
            ],
            'owner3_sold' => [
                'owner' => $clients['owner_three'], 'linkUser' => null, 'code' => 'PROP-DEMO-005',
                'type' => 'house', 'status' => 'sold', 'approval' => 'approved', 'listed' => false,
                'address' => ['Bhaisepati', 'Lalitpur Metropolitan City', 'Lalitpur', 'Bagmati'],
                'purpose' => 'sale', 'price' => 21000000, 'app' => 'AGJ-DEMO-0005',
            ],
            'pending_approval' => [
                'owner' => $clients['owner_no_login'], 'linkUser' => null, 'code' => 'PROP-DEMO-006',
                'type' => 'office_space', 'status' => 'draft', 'approval' => 'pending', 'listed' => false,
                'address' => ['Kupondole', 'Lalitpur Metropolitan City', 'Lalitpur', 'Bagmati'],
                'purpose' => 'rent', 'price' => 60000, 'app' => 'AGJ-DEMO-0006',
            ],
            'under_verification' => [
                'owner' => $clients['owner_three'], 'linkUser' => null, 'code' => 'PROP-DEMO-007',
                'type' => 'apartment', 'status' => 'under_verification', 'approval' => 'pending', 'listed' => false,
                'address' => ['Boudha', 'Kathmandu Metropolitan City', 'Kathmandu', 'Bagmati'],
                'purpose' => 'sale', 'price' => 12500000, 'app' => 'AGJ-DEMO-0007',
            ],
        ];

        $photoUrls = [
            'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1200&q=80',
        ];

        foreach ($defs as $key => $d) {
            [$tole, $muni, $district, $province] = $d['address'];
            $address = $this->address($tole, $muni, $district, $province);

            $property = Property::create([
                'property_code' => $d['code'],
                'owner_client_id' => $d['owner']->client_id,
                'user_id' => $d['linkUser']?->id,
                'ownership_role' => 'self',
                'property_type' => $d['type'],
                'address_id' => $address->address_id,
                'kitta_no' => (string) rand(100, 999),
                'area' => $d['type'] === 'land' ? '0-8-0-0 Ropani' : '0-6-0-0 Aana',
                'no_of_floors' => in_array($d['type'], ['land'], true) ? null : rand(1, 4),
                'covered_area' => in_array($d['type'], ['land'], true) ? null : rand(1200, 3500).' sq.ft',
                'structure_type' => 'RCC',
                'road_access' => 'blacktopped',
                'water_supply' => 'municipal',
                'electricity' => 'available',
                'current_building_condition' => 'good',
                'status' => $d['status'],
                'approval_status' => $d['approval'],
                'is_listed' => $d['listed'],
            ]);

            foreach ($photoUrls as $i => $url) {
                PropertyPhoto::create([
                    'property_id' => $property->property_id,
                    'source_type' => 'listing',
                    'photo_type' => $i === 0 ? 'front' : 'interior',
                    'file_ref' => $url,
                    'caption' => "{$d['code']} Photo ".($i + 1),
                    'uploaded_at' => now(),
                ]);
            }

            PropertyListing::create([
                'application_no' => $d['app'],
                'property_id' => $property->property_id,
                'applicant_client_id' => $d['owner']->client_id,
                'purpose_of_listing' => $d['purpose'],
                'expected_selling_price' => $d['purpose'] === 'sale' || $d['purpose'] === 'investment' ? $d['price'] : null,
                'rental_amount' => in_array($d['purpose'], ['rent', 'lease'], true) ? $d['price'] : null,
                'negotiable' => true,
                'inspection_required' => true,
                'valuation_required' => true,
                'photographs_received' => true,
                'gis_location_verified' => $d['approval'] === 'approved',
                'legal_verification_status' => $d['approval'] === 'approved' ? 'completed' : 'pending',
                'listing_status' => $d['approval'] === 'approved' ? 'approved' : 'pending',
                'received_by_staff_id' => $this->staff['Receptionist / Front Desk Officer']->staff_id,
                'assigned_officer_id' => $this->staff['Sales/Brokerage Representative']->staff_id,
                'effective_date' => now(),
                'date_received' => now()->subDays(rand(5, 60)),
            ]);

            $properties[$key] = $property;
        }

        return $properties;
    }

    /**
     * @param  array<string, Property>  $properties
     */
    private function seedInspectionsAndVerifications(array $properties): void
    {
        $engineer = $this->staff['Site Engineer / Survey Officer'];
        $survey = $this->staff['Survey Coordinator'];

        SiteInspection::create([
            'property_id' => $properties['self_listed_sale']->property_id,
            'listing_id' => $properties['self_listed_sale']->listings()->first()?->listing_id ?? null,
            'inspector_staff_id' => $engineer->staff_id,
            'inspection_date' => now()->subDays(30),
            'distance_from_main_road' => '50 meters',
            'nearby_facilities' => 'School, hospital, market within 1km',
            'commercial_potential' => 'Moderate',
            'residential_suitability' => 'Highly suitable',
            'final_status' => 'suitable_for_listing',
            'prepared_by_staff_id' => $engineer->staff_id,
            'prepared_date' => now()->subDays(29),
            'verified_by_staff_id' => $survey->staff_id,
            'verified_date' => now()->subDays(28),
        ]);

        SiteInspection::create([
            'property_id' => $properties['under_verification']->property_id,
            'inspector_staff_id' => $engineer->staff_id,
            'inspection_date' => now()->subDays(3),
            'distance_from_main_road' => '200 meters',
            'nearby_facilities' => 'Temple, bus stop nearby',
            'final_status' => 'requires_additional_verification',
            'prepared_by_staff_id' => $engineer->staff_id,
            'prepared_date' => now()->subDays(2),
        ]);

        PropertyVerification::create([
            'property_id' => $properties['self_listed_sale']->property_id,
            'verifier_staff_id' => $survey->staff_id,
            'approver_staff_id' => $this->staff['Technical Manager']->staff_id,
            'verification_date' => now()->subDays(27),
            'approved_date' => now()->subDays(25),
            'result' => 'verified',
            'gps_coordinates_recorded' => true,
            'mis_entry_completed' => true,
            'mobile_app_verification_completed' => true,
        ]);

        PropertyVerification::create([
            'property_id' => $properties['under_verification']->property_id,
            'verifier_staff_id' => $survey->staff_id,
            'verification_date' => now()->subDay(),
            'result' => 'additional_documents_required',
            'gps_coordinates_recorded' => true,
        ]);
    }

    /**
     * @param  array<string, Client>  $clients
     * @param  array<string, Property>  $properties
     */
    private function seedValuations(array $clients, array $properties): void
    {
        $valuator = $this->staff['Valuation Officer'];

        $req1 = ValuationRequest::create([
            'request_code' => 'VAL-DEMO-0001',
            'client_id' => $clients['owner']->client_id,
            'property_id' => $properties['self_listed_sale']->property_id,
            'purpose_of_valuation' => 'buying_selling',
            'requested_valuation_type' => 'market_value',
            'preferred_visit_date' => now()->subDays(20),
            'site_contact_person_name' => $clients['owner']->full_name,
            'site_contact_mobile' => $clients['owner']->mobile_no,
            'assigned_valuator_staff_id' => $valuator->staff_id,
            'field_visit_date' => now()->subDays(18),
            'application_received_date' => now()->subDays(22),
            'status' => 'report_issued',
        ]);

        ValuationReport::create([
            'report_no' => 'VR-DEMO-0001',
            'request_id' => $req1->request_id,
            'property_id' => $properties['self_listed_sale']->property_id,
            'valuation_type' => 'market_value',
            'land_area' => 400,
            'land_rate' => 60000,
            'building_area' => 2400,
            'building_rate' => 3500,
            'valuated_amount' => 30000000,
            'rate_basis' => 'Per-aana land rate + per-sqft construction rate, area average',
            'valuator_staff_id' => $valuator->staff_id,
            'approved_by_staff_id' => $this->staff['General Manager']->staff_id,
            'approval_status' => 'approved',
            'digitally_signed' => true,
            'issued_date' => now()->subDays(15),
        ]);

        $req2 = ValuationRequest::create([
            'request_code' => 'VAL-DEMO-0002',
            'client_id' => $clients['owner_three']->client_id,
            'property_id' => $properties['owner3_investment']->property_id,
            'purpose_of_valuation' => 'investment_decision',
            'requested_valuation_type' => 'market_value',
            'assigned_valuator_staff_id' => $valuator->staff_id,
            'application_received_date' => now()->subDays(10),
            'status' => 'in_progress',
        ]);

        ValuationRequest::create([
            'request_code' => 'VAL-DEMO-0003',
            'client_id' => $clients['owner_no_login']->client_id,
            'property_id' => $properties['pending_approval']->property_id,
            'purpose_of_valuation' => 'bank_loan_mortgage',
            'requested_valuation_type' => 'forced_sale_value',
            'application_received_date' => now()->subDays(2),
            'status' => 'received',
        ]);
    }

    /**
     * @param  array<string, Client>  $clients
     * @param  array<string, Property>  $properties
     */
    private function seedAgreementsAndPayments(array $clients, array $properties): void
    {
        $documentOfficer = $this->staff['Document Officer / Legal Coordinator'];

        // Completed sale-purchase agreement: owner sold to buyer, with a payment receipt.
        $saleAgreement = Agreement::create([
            'agreement_type' => 'sale_purchase',
            'property_id' => $properties['owner3_sold']->property_id,
            'house_description' => 'Two-storey residential house with attached garden',
            'agreement_date' => now()->subDays(40),
            'place' => 'API GharJagga Office, Kathmandu',
            'total_price' => 21000000,
            'total_price_words' => 'Two Crore Ten Lakh Rupees Only',
            'advance_payment' => 5000000,
            'balance_payment' => 16000000,
            'final_payment_date' => now()->subDays(10),
            'status' => 'completed',
            'governing_law' => 'Prevailing laws of Nepal',
        ]);

        AgreementParty::create([
            'agreement_id' => $saleAgreement->agreement_id,
            'party_role' => 'seller',
            'client_id' => $clients['owner_three']->client_id,
        ]);
        AgreementParty::create([
            'agreement_id' => $saleAgreement->agreement_id,
            'party_role' => 'buyer',
            'client_id' => $clients['buyer']->client_id,
        ]);
        AgreementWitness::create([
            'agreement_id' => $saleAgreement->agreement_id,
            'full_name' => 'Hari Prasad Oli',
            'citizenship_no' => '27-01-65-00909',
            'signed_at' => now()->subDays(40),
        ]);

        // Active listing/brokerage agreement for the owner-demo login.
        $brokerageAgreement = Agreement::create([
            'agreement_type' => 'listing_brokerage',
            'property_id' => $properties['self_listed_sale']->property_id,
            'agreement_date' => now()->subMonths(5),
            'place' => 'API GharJagga Office, Kathmandu',
            'commission_rate_percent' => 2.5,
            'agreement_period_months' => 12,
            'termination_notice_days' => 30,
            'status' => 'active',
            'governing_law' => 'Prevailing laws of Nepal',
        ]);

        AgreementParty::create([
            'agreement_id' => $brokerageAgreement->agreement_id,
            'party_role' => 'property_owner',
            'client_id' => $clients['owner']->client_id,
        ]);
        $companyId = DB::table('company_profile')->value('company_id');
        if (! $companyId) {
            $companyId = DB::table('company_profile')->insertGetId([
                'company_name' => 'API GharJagga Pvt. Ltd.',
                'broker_licence_no' => 'BRK1835451',
                'land_survey_licence_no' => 'BRS1873551',
                'is_active' => true,
            ]);
        }

        AgreementParty::create([
            'agreement_id' => $brokerageAgreement->agreement_id,
            'party_role' => 'company',
            'company_id' => $companyId,
            'representative_name' => $this->staff['Sales/Brokerage Representative']->full_name,
            'designation' => 'Sales/Brokerage Representative',
        ]);

        // Payment receipts — each auto-posts to the ledger via PaymentReceiptObserver.
        PaymentReceipt::create([
            'receipt_no' => 'RCP-DEMO-0001',
            'receipt_date' => now()->subDays(40),
            'client_id' => $clients['buyer']->client_id,
            'agreement_id' => $saleAgreement->agreement_id,
            'property_id' => $properties['owner3_sold']->property_id,
            'amount' => 5000000,
            'amount_in_words' => 'Fifty Lakh Rupees Only',
            'purpose' => 'property_registration_service',
            'mode_of_payment' => 'cheque',
            'cheque_no' => 'CHQ-10023',
            'bank_name' => 'Nepal Investment Bank',
            'cheque_date' => now()->subDays(40),
            'received_by_staff_id' => $documentOfficer->staff_id,
        ]);

        PaymentReceipt::create([
            'receipt_no' => 'RCP-DEMO-0002',
            'receipt_date' => now()->subMonths(5),
            'client_id' => $clients['owner']->client_id,
            'agreement_id' => $brokerageAgreement->agreement_id,
            'property_id' => $properties['self_listed_sale']->property_id,
            'amount' => 15000,
            'amount_in_words' => 'Fifteen Thousand Rupees Only',
            'purpose' => 'digital_marketing_charge',
            'mode_of_payment' => 'cash',
            'received_by_staff_id' => $this->staff['Receptionist / Front Desk Officer']->staff_id,
        ]);

        PaymentReceipt::create([
            'receipt_no' => 'RCP-DEMO-0003',
            'receipt_date' => now()->subDays(15),
            'client_id' => $clients['owner']->client_id,
            'property_id' => $properties['self_listed_sale']->property_id,
            'amount' => 5000,
            'amount_in_words' => 'Five Thousand Rupees Only',
            'purpose' => 'property_valuation_fee',
            'mode_of_payment' => 'cash',
            'received_by_staff_id' => $this->staff['Receptionist / Front Desk Officer']->staff_id,
        ]);

        PaymentReceipt::create([
            'receipt_no' => 'RCP-DEMO-0004',
            'receipt_date' => now()->subDays(5),
            'client_id' => $clients['owner_three']->client_id,
            'property_id' => $properties['owner3_investment']->property_id,
            'amount' => 3000,
            'amount_in_words' => 'Three Thousand Rupees Only',
            'purpose' => 'field_visit_charge',
            'mode_of_payment' => 'cash',
            'received_by_staff_id' => $this->staff['Receptionist / Front Desk Officer']->staff_id,
        ]);
    }

    /**
     * @param  array<string, Client>  $clients
     * @param  array<string, Property>  $properties
     */
    private function seedComplaints(array $clients, array $properties): void
    {
        $c1 = Complaint::create([
            'complaint_code' => 'CMP-DEMO-0001',
            'complaint_date' => now()->subDays(10),
            'received_through' => 'mobile_app',
            'received_by_staff_id' => $this->staff['Customer Support Officer']->staff_id,
            'client_id' => $clients['owner']->client_id,
            'property_id' => $properties['self_listed_sale']->property_id,
            'category' => 'digital_platform_issue',
            'description' => 'Photos not displaying correctly on the listing page.',
            'priority' => 'medium',
            'assigned_department' => 'IT',
            'assigned_officer_staff_id' => $this->staff['IT Support / System Administrator']->staff_id,
            'status' => 'resolved',
            'investigation_date' => now()->subDays(9),
            'findings' => 'Image cache was stale on the CDN.',
            'corrective_action_taken' => 'Cleared cache and re-uploaded photos.',
            'resolution_date' => now()->subDays(8),
            'satisfaction_level' => 'satisfied',
        ]);
        ComplaintEvidence::create([
            'complaint_id' => $c1->complaint_id,
            'evidence_type' => 'screenshot',
            'file_ref' => 'demo/complaints/screenshot-0001.png',
        ]);

        Complaint::create([
            'complaint_code' => 'CMP-DEMO-0002',
            'complaint_date' => now()->subDays(3),
            'received_through' => 'phone',
            'received_by_staff_id' => $this->staff['Customer Support Officer']->staff_id,
            'client_id' => $clients['buyer']->client_id,
            'property_id' => $properties['owner3_sold']->property_id,
            'category' => 'payment_billing_issue',
            'description' => 'Receipt amount does not match the agreed advance payment.',
            'priority' => 'high',
            'assigned_department' => 'Finance',
            'assigned_officer_staff_id' => $this->staff['Finance Manager']->staff_id,
            'status' => 'under_investigation',
            'investigation_date' => now()->subDays(2),
        ]);

        Complaint::create([
            'complaint_code' => 'CMP-DEMO-0003',
            'complaint_date' => now()->subDay(),
            'received_through' => 'website',
            'client_id' => $clients['owner_no_login']->client_id,
            'category' => 'site_visit_issue',
            'description' => 'Inspection team did not show up on the scheduled date.',
            'priority' => 'urgent',
            'status' => 'registered',
        ]);
    }

    /**
     * @param  array<string, Client>  $clients
     * @param  array<string, Property>  $properties
     */
    private function seedServiceOrdersAndHandover(array $clients, array $properties): void
    {
        $order = ServiceOrder::create([
            'order_no' => 'SO-DEMO-0001',
            'client_id' => $clients['owner']->client_id,
            'property_id' => $properties['self_listed_sale']->property_id,
            'order_date' => now()->subDays(35),
            'status' => 'completed',
        ]);

        ServiceCompletionCertificate::create([
            'certificate_no' => 'SCC-DEMO-0001',
            'issue_date' => now()->subDays(5),
            'client_id' => $clients['owner']->client_id,
            'property_id' => $properties['self_listed_sale']->property_id,
            'service_order_id' => $order->order_id,
            'service_start_date' => now()->subDays(35),
            'service_completion_date' => now()->subDays(5),
            'assigned_officer_staff_id' => $this->staff['Sales/Brokerage Representative']->staff_id,
            'technical_reviewer_staff_id' => $this->staff['Technical Manager']->staff_id,
            'final_status' => 'completed',
            'client_acceptance_date' => now()->subDays(4),
            'prepared_by_staff_id' => $this->staff['Sales/Brokerage Representative']->staff_id,
            'verified_by_staff_id' => $this->staff['General Manager']->staff_id,
        ]);

        ServiceOrder::create([
            'order_no' => 'SO-DEMO-0002',
            'client_id' => $clients['owner_three']->client_id,
            'property_id' => $properties['owner3_investment']->property_id,
            'order_date' => now()->subDays(8),
            'status' => 'in_progress',
        ]);

        PropertyHandoverCertificate::create([
            'certificate_no' => 'PHC-DEMO-0001',
            'handover_date' => now()->subDays(9),
            'place' => 'Property site, Bhaisepati',
            'owner_client_id' => $clients['buyer']->client_id,
            'company_rep_staff_id' => $this->staff['Document Officer / Legal Coordinator']->staff_id,
            'property_id' => $properties['owner3_sold']->property_id,
            'purpose' => 'sale_purchase_facilitation',
            'possession_status' => 'handed_over',
            'keys_status' => 'received',
            'ownership_docs_status' => 'received',
            'tax_docs_status' => 'received',
            'utility_docs_status' => 'received',
            'land_boundary_condition' => 'clear',
            'building_structure_condition' => 'good',
            'electrical_condition' => 'functional',
            'water_supply_condition' => 'available',
            'sanitation_condition' => 'available',
            'furniture_equipment_status' => 'none',
        ]);
    }

    /**
     * @param  array<string, Property>  $properties
     */
    private function seedInquiries(array $properties): void
    {
        $listing1 = $properties['self_listed_sale']->listings()->first();
        $listing2 = $properties['owner2_lease']->listings()->first();

        PropertyInquiry::create([
            'property_id' => $properties['self_listed_sale']->property_id,
            'listing_id' => $listing1?->listing_id,
            'name' => 'Bikash Rai',
            'phone' => '9812345670',
            'email' => 'bikash.rai@example.com',
            'message' => 'Is this property still available? I would like to schedule a visit.',
            'status' => 'new',
        ]);

        PropertyInquiry::create([
            'property_id' => $properties['owner2_lease']->property_id,
            'listing_id' => $listing2?->listing_id,
            'name' => 'Sunil Adhikari',
            'phone' => '9812345671',
            'email' => 'sunil.adhikari@example.com',
            'message' => 'Interested in leasing this commercial space for a retail shop.',
            'status' => 'contacted',
            'admin_note' => 'Called and shared floor plan; awaiting response.',
        ]);

        PropertyInquiry::create([
            'property_id' => $properties['owner3_investment']->property_id,
            'name' => 'Nisha Basnet',
            'phone' => '9812345672',
            'message' => 'What is the expected ROI on this investment plot?',
            'status' => 'closed',
            'admin_note' => 'Provided investment brochure; client not proceeding for now.',
        ]);
    }

    /**
     * @param  array<string, Client>  $clients
     */
    private function seedPowerOfAttorney(array $clients): void
    {
        $agentUser = $this->users['agent@apigharjagga.com'] ?? null;
        if (! $agentUser) {
            return;
        }

        PowerOfAttorney::create([
            'agent_user_id' => $agentUser->id,
            'owner_client_id' => $clients['owner_no_login']->client_id,
            'document_path' => 'demo/poa/approved-poa.pdf',
            'status' => 'approved',
            'verified_by_staff_id' => $this->staff['Document Officer / Legal Coordinator']->staff_id,
            'verified_at' => now()->subDays(20),
            'notes' => 'Verified against original notarized document.',
        ]);

        PowerOfAttorney::create([
            'agent_user_id' => $agentUser->id,
            'owner_client_id' => $clients['owner_three']->client_id,
            'document_path' => 'demo/poa/pending-poa.pdf',
            'status' => 'pending',
        ]);

        PowerOfAttorney::create([
            'agent_user_id' => $agentUser->id,
            'owner_client_id' => $clients['buyer_two']->client_id,
            'document_path' => 'demo/poa/rejected-poa.pdf',
            'status' => 'rejected',
            'verified_by_staff_id' => $this->staff['Document Officer / Legal Coordinator']->staff_id,
            'verified_at' => now()->subDays(15),
            'notes' => 'Document illegible; requested resubmission.',
        ]);
    }

    /**
     * @param  array<string, Client>  $clients
     * @param  array<string, Property>  $properties
     */
    private function seedFinanceExtras(array $clients, array $properties): void
    {
        $financeManager = $this->staff['Finance Manager'];

        // Invoices
        $inv1 = Invoice::create([
            'invoice_no' => 'INV-DEMO-0001',
            'client_id' => $clients['owner']->client_id,
            'property_id' => $properties['self_listed_sale']->property_id,
            'issue_date' => now()->subDays(30),
            'due_date' => now()->subDays(15),
            'status' => 'paid',
            'subtotal' => 15000,
            'tax_amount' => 0,
            'total_amount' => 15000,
            'created_by_staff_id' => $financeManager->staff_id,
        ]);
        InvoiceItem::create([
            'invoice_id' => $inv1->invoice_id,
            'description' => 'Digital marketing package — 30 days',
            'quantity' => 1,
            'unit_price' => 15000,
            'amount' => 15000,
        ]);

        $inv2 = Invoice::create([
            'invoice_no' => 'INV-DEMO-0002',
            'client_id' => $clients['owner_three']->client_id,
            'property_id' => $properties['owner3_investment']->property_id,
            'issue_date' => now()->subDays(5),
            'due_date' => now()->addDays(10),
            'status' => 'sent',
            'subtotal' => 8000,
            'tax_amount' => 0,
            'total_amount' => 8000,
            'created_by_staff_id' => $financeManager->staff_id,
        ]);
        InvoiceItem::create([
            'invoice_id' => $inv2->invoice_id,
            'description' => 'Field visit charge',
            'quantity' => 1,
            'unit_price' => 3000,
            'amount' => 3000,
        ]);
        InvoiceItem::create([
            'invoice_id' => $inv2->invoice_id,
            'description' => 'Valuation report preparation',
            'quantity' => 1,
            'unit_price' => 5000,
            'amount' => 5000,
        ]);

        Invoice::create([
            'invoice_no' => 'INV-DEMO-0003',
            'client_id' => $clients['owner_no_login']->client_id,
            'issue_date' => now()->subDays(3),
            'due_date' => now()->subDays(1),
            'status' => 'draft',
            'subtotal' => 5000,
            'tax_amount' => 0,
            'total_amount' => 5000,
            'created_by_staff_id' => $financeManager->staff_id,
        ]);

        // Budgets
        $year = (int) now()->format('Y');
        $accountCodes = ['5010' => 800000, '5020' => 60000, '5040' => 50000, '5060' => 20000];
        foreach ($accountCodes as $code => $allocated) {
            $account = FinanceAccount::where('account_code', $code)->first();
            if ($account) {
                Budget::create([
                    'account_id' => $account->account_id,
                    'fiscal_year' => $year,
                    'period_type' => 'annual',
                    'period_label' => (string) $year,
                    'allocated_amount' => $allocated,
                ]);
            }
        }

        // Payment vouchers — two draft (for you to test the approve action),
        // two already approved+posted (for report history).
        PaymentVoucher::create([
            'voucher_no' => 'PV-DEMO-0001',
            'voucher_date' => now(),
            'payee_name' => 'Kathmandu Metropolitan City (Office Rent)',
            'purpose' => 'Monthly office rent',
            'account_id' => FinanceAccount::where('account_code', '5020')->value('account_id'),
            'amount' => 60000,
            'mode_of_payment' => 'cash',
            'status' => 'draft',
            'created_by_staff_id' => $financeManager->staff_id,
        ]);

        PaymentVoucher::create([
            'voucher_no' => 'PV-DEMO-0002',
            'voucher_date' => now(),
            'payee_name' => 'Himalayan Digital Ads Pvt. Ltd.',
            'purpose' => 'Social media advertising campaign',
            'account_id' => FinanceAccount::where('account_code', '5040')->value('account_id'),
            'amount' => 25000,
            'mode_of_payment' => 'cheque',
            'cheque_no' => 'CHQ-20011',
            'bank_name' => 'Nabil Bank',
            'status' => 'draft',
            'created_by_staff_id' => $financeManager->staff_id,
        ]);

        $this->postVoucher('PV-DEMO-0003', now()->subDays(20), 'Office Supplies Store', 'Stationery and printing supplies', '5050', 8000, $financeManager);
        $this->postVoucher('PV-DEMO-0004', now()->subDays(12), 'Prabin Travels', 'Field visit fuel and travel reimbursement', '5060', 6500, $financeManager);
    }

    private function postVoucher(string $voucherNo, CarbonInterface $date, string $payee, string $purpose, string $expenseCode, float $amount, Staff $financeManager): void
    {
        $cash = FinanceAccount::where('account_code', FinanceAccount::CODE_CASH)->first();
        $expenseAccount = FinanceAccount::where('account_code', $expenseCode)->first();

        $voucher = PaymentVoucher::create([
            'voucher_no' => $voucherNo,
            'voucher_date' => $date,
            'payee_name' => $payee,
            'purpose' => $purpose,
            'account_id' => $expenseAccount?->account_id,
            'amount' => $amount,
            'mode_of_payment' => 'cash',
            'status' => 'paid',
            'approved_by_staff_id' => $financeManager->staff_id,
            'created_by_staff_id' => $financeManager->staff_id,
        ]);

        if ($cash && $expenseAccount) {
            FinanceTransaction::postBalanced(
                [
                    'transaction_date' => $date->toDateString(),
                    'reference_type' => 'payment_voucher',
                    'reference_id' => $voucher->voucher_id,
                    'description' => "Voucher {$voucher->voucher_no}: {$purpose}",
                    'created_by_staff_id' => $financeManager->staff_id,
                ],
                [
                    ['account_id' => $expenseAccount->account_id, 'debit' => $amount],
                    ['account_id' => $cash->account_id, 'credit' => $amount],
                ],
            );
        }
    }

    private function seedHr(): void
    {
        // Attendance — last 7 working days for every staff member.
        foreach ($this->staff as $staff) {
            for ($i = 7; $i >= 1; $i--) {
                $date = now()->subDays($i);
                if ($date->isWeekend()) {
                    continue;
                }
                Attendance::create([
                    'staff_id' => $staff->staff_id,
                    'attendance_date' => $date->toDateString(),
                    'status' => 'present',
                    'check_in' => $date->copy()->setTime(9, rand(0, 30)),
                    'check_out' => $date->copy()->setTime(17, rand(30, 59)),
                ]);
            }
        }

        // Leave requests
        LeaveRequest::create([
            'staff_id' => $this->staff['Site Engineer / Survey Officer']->staff_id,
            'leave_type_id' => 1,
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'total_days' => 3,
            'reason' => 'Family function',
            'status' => 'pending',
            'applied_at' => now()->subDay(),
        ]);
        LeaveRequest::create([
            'staff_id' => $this->staff['Customer Support Officer']->staff_id,
            'leave_type_id' => 1,
            'start_date' => now()->subDays(10)->toDateString(),
            'end_date' => now()->subDays(9)->toDateString(),
            'total_days' => 2,
            'reason' => 'Medical appointment',
            'status' => 'approved',
            'approved_by_staff_id' => $this->staff['General Manager']->staff_id,
            'applied_at' => now()->subDays(12),
        ]);
        LeaveRequest::create([
            'staff_id' => $this->staff['Receptionist / Front Desk Officer']->staff_id,
            'leave_type_id' => 1,
            'start_date' => now()->subDays(3)->toDateString(),
            'end_date' => now()->subDays(3)->toDateString(),
            'total_days' => 1,
            'reason' => 'Personal work',
            'status' => 'rejected',
            'approved_by_staff_id' => $this->staff['General Manager']->staff_id,
            'applied_at' => now()->subDays(4),
        ]);

        // Payroll — one finalized past month (posted to ledger), one draft
        // current month (so you can test the generate → finalize flow).
        $pastRun = PayrollRun::create([
            'period_month' => now()->subMonth()->format('Y-m'),
            'status' => 'finalized',
            'finalized_at' => now()->subMonth()->endOfMonth(),
            'created_by_staff_id' => $this->staff['Finance Manager']->staff_id,
        ]);

        $totalNetPay = 0;
        foreach ($this->staff as $staff) {
            $net = (float) $staff->basic_salary;
            $totalNetPay += $net;
            Payslip::create([
                'payroll_run_id' => $pastRun->payroll_run_id,
                'staff_id' => $staff->staff_id,
                'basic_salary' => $staff->basic_salary,
                'allowances' => 0,
                'deductions' => 0,
                'net_pay' => $net,
            ]);
        }

        $cash = FinanceAccount::where('account_code', FinanceAccount::CODE_CASH)->first();
        $salaries = FinanceAccount::where('account_code', '5010')->first();
        if ($cash && $salaries) {
            FinanceTransaction::postBalanced(
                [
                    'transaction_date' => now()->subMonth()->endOfMonth()->toDateString(),
                    'reference_type' => 'payroll_run',
                    'reference_id' => $pastRun->payroll_run_id,
                    'description' => 'Payroll for '.now()->subMonth()->format('F Y'),
                    'created_by_staff_id' => $this->staff['Finance Manager']->staff_id,
                ],
                [
                    ['account_id' => $salaries->account_id, 'debit' => $totalNetPay],
                    ['account_id' => $cash->account_id, 'credit' => $totalNetPay],
                ],
            );
        }

        PayrollRun::create([
            'period_month' => now()->format('Y-m'),
            'status' => 'draft',
            'created_by_staff_id' => $this->staff['Finance Manager']->staff_id,
        ]);

        // Performance reviews
        PerformanceReview::create([
            'staff_id' => $this->staff['Sales/Brokerage Representative']->staff_id,
            'reviewer_staff_id' => $this->staff['General Manager']->staff_id,
            'review_period' => now()->subQuarter()->format('Y \Q').ceil(now()->subQuarter()->month / 3),
            'rating' => 4,
            'strengths' => 'Consistently exceeds listing targets, strong client rapport.',
            'areas_for_improvement' => 'Could improve documentation turnaround time.',
            'review_date' => now()->subDays(20),
        ]);
        PerformanceReview::create([
            'staff_id' => $this->staff['Site Engineer / Survey Officer']->staff_id,
            'reviewer_staff_id' => $this->staff['Technical Manager']->staff_id,
            'review_period' => now()->subQuarter()->format('Y \Q').ceil(now()->subQuarter()->month / 3),
            'rating' => 5,
            'strengths' => 'Thorough site inspections, excellent technical reports.',
            'areas_for_improvement' => 'None noted this period.',
            'review_date' => now()->subDays(18),
        ]);

        // Staff documents & contracts
        StaffDocument::create([
            'staff_id' => $this->staff['Finance Manager']->staff_id,
            'document_name' => 'Citizenship Certificate',
            'file_path' => 'demo/staff/finance-manager-citizenship.pdf',
            'uploaded_at' => now()->subMonths(6),
        ]);
        StaffDocument::create([
            'staff_id' => $this->staff['Site Engineer / Survey Officer']->staff_id,
            'document_name' => 'Engineering Degree Certificate',
            'file_path' => 'demo/staff/engineer-degree.pdf',
            'uploaded_at' => now()->subMonths(4),
        ]);

        StaffContract::create([
            'staff_id' => $this->staff['General Manager']->staff_id,
            'contract_type' => 'permanent',
            'start_date' => $this->staff['General Manager']->date_of_joining,
            'salary' => $this->staff['General Manager']->basic_salary,
            'file_path' => 'demo/staff/gm-contract.pdf',
            'status' => 'active',
        ]);
        StaffContract::create([
            'staff_id' => $this->staff['Sales/Brokerage Representative']->staff_id,
            'contract_type' => 'probation',
            'start_date' => $this->staff['Sales/Brokerage Representative']->date_of_joining,
            'end_date' => Carbon::parse($this->staff['Sales/Brokerage Representative']->date_of_joining)->addMonths(6)->toDateString(),
            'salary' => $this->staff['Sales/Brokerage Representative']->basic_salary,
            'file_path' => 'demo/staff/sales-rep-contract.pdf',
            'status' => 'active',
        ]);
    }

    /**
     * @param  array<string, Client>  $clients
     * @param  array<string, Property>  $properties
     */
    private function seedProjects(array $clients, array $properties): void
    {
        $contractor1 = Contractor::create([
            'name' => 'Himalayan Builders Pvt. Ltd.',
            'contact_person' => 'Mohan Shrestha',
            'mobile_no' => '9851000001',
            'email' => 'contact@himalayanbuilders.com.np',
            'specialization' => 'Civil construction',
            'is_active' => true,
        ]);
        $contractor2 = Contractor::create([
            'name' => 'Everest Electrical Works',
            'contact_person' => 'Bikram Basnet',
            'mobile_no' => '9851000002',
            'email' => 'info@everestelectrical.com.np',
            'specialization' => 'Electrical works',
            'is_active' => true,
        ]);
        Contractor::create([
            'name' => 'Sagarmatha Plumbing Services',
            'contact_person' => 'Yubaraj Karki',
            'mobile_no' => '9851000003',
            'specialization' => 'Plumbing',
            'is_active' => true,
        ]);

        $project1 = Project::create([
            'project_code' => 'PRJ-DEMO-0001',
            'project_name' => 'Baluwatar Residence Renovation',
            'client_id' => $clients['owner']->client_id,
            'property_id' => $properties['self_listed_sale']->property_id,
            'assigned_engineer_staff_id' => $this->staff['Site Engineer / Survey Officer']->staff_id,
            'status' => 'in_progress',
            'start_date' => now()->subMonths(2)->toDateString(),
            'expected_end_date' => now()->addMonth()->toDateString(),
            'description' => 'Full interior renovation and structural reinforcement.',
            'created_by_staff_id' => $this->staff['Technical Manager']->staff_id,
        ]);

        ProjectMilestone::create(['project_id' => $project1->project_id, 'title' => 'Site preparation & demolition', 'sequence' => 1, 'due_date' => now()->subMonth()->toDateString(), 'completed_date' => now()->subMonth()->toDateString(), 'status' => 'completed']);
        ProjectMilestone::create(['project_id' => $project1->project_id, 'title' => 'Structural reinforcement', 'sequence' => 2, 'due_date' => now()->toDateString(), 'status' => 'pending']);
        ProjectMilestone::create(['project_id' => $project1->project_id, 'title' => 'Interior finishing', 'sequence' => 3, 'due_date' => now()->addMonth()->toDateString(), 'status' => 'pending']);

        ProjectSiteVisit::create(['project_id' => $project1->project_id, 'visit_date' => now()->subWeeks(2), 'visited_by_staff_id' => $this->staff['Site Engineer / Survey Officer']->staff_id, 'notes' => 'Demolition on track; no structural surprises found.']);
        ProjectSiteVisit::create(['project_id' => $project1->project_id, 'visit_date' => now()->subDays(3), 'visited_by_staff_id' => $this->staff['Technical Manager']->staff_id, 'notes' => 'Reviewed reinforcement plan with contractor.']);

        ProjectProgressLog::create(['project_id' => $project1->project_id, 'log_date' => now()->subMonth(), 'percent_complete' => 20, 'description' => 'Demolition complete.', 'logged_by_staff_id' => $this->staff['Site Engineer / Survey Officer']->staff_id]);
        ProjectProgressLog::create(['project_id' => $project1->project_id, 'log_date' => now()->subDays(3), 'percent_complete' => 45, 'description' => 'Reinforcement 45% complete.', 'logged_by_staff_id' => $this->staff['Site Engineer / Survey Officer']->staff_id]);

        BoqItem::create(['project_id' => $project1->project_id, 'item_description' => 'Cement (bags)', 'unit' => 'bag', 'quantity' => 500, 'rate' => 950, 'amount' => 475000]);
        BoqItem::create(['project_id' => $project1->project_id, 'item_description' => 'Steel rebar (12mm)', 'unit' => 'kg', 'quantity' => 2000, 'rate' => 130, 'amount' => 260000]);
        BoqItem::create(['project_id' => $project1->project_id, 'item_description' => 'Labour charges', 'unit' => 'lump sum', 'quantity' => 1, 'rate' => 350000, 'amount' => 350000]);

        ProjectContractor::create(['project_id' => $project1->project_id, 'contractor_id' => $contractor1->contractor_id, 'work_scope' => 'Civil construction and structural work', 'contract_amount' => 900000, 'start_date' => now()->subMonths(2)->toDateString(), 'status' => 'active']);
        ProjectContractor::create(['project_id' => $project1->project_id, 'contractor_id' => $contractor2->contractor_id, 'work_scope' => 'Electrical rewiring', 'contract_amount' => 150000, 'start_date' => now()->subMonth()->toDateString(), 'status' => 'active']);

        MaterialRecord::create(['project_id' => $project1->project_id, 'material_name' => 'Cement', 'quantity' => 300, 'unit' => 'bag', 'unit_cost' => 950, 'total_cost' => 285000, 'supplier' => 'Kathmandu Cement Suppliers', 'received_date' => now()->subMonth(), 'recorded_by_staff_id' => $this->staff['Site Engineer / Survey Officer']->staff_id]);
        MaterialRecord::create(['project_id' => $project1->project_id, 'material_name' => 'Steel rebar', 'quantity' => 1200, 'unit' => 'kg', 'unit_cost' => 130, 'total_cost' => 156000, 'supplier' => 'Himal Iron & Steel', 'received_date' => now()->subDays(20), 'recorded_by_staff_id' => $this->staff['Site Engineer / Survey Officer']->staff_id]);

        ProjectInspectionReport::create(['project_id' => $project1->project_id, 'inspection_date' => now()->subDays(5), 'inspected_by_staff_id' => $this->staff['Technical Manager']->staff_id, 'findings' => 'Reinforcement work meets structural drawings; minor rework needed on the east wall.', 'result' => 'needs_correction']);

        $this->postVoucher('PV-DEMO-0005', now()->subDays(18), 'Himalayan Builders Pvt. Ltd.', 'Advance payment — civil construction', '5010', 300000, $this->staff['Finance Manager']);
        PaymentVoucher::where('voucher_no', 'PV-DEMO-0005')->update(['project_id' => $project1->project_id]);

        Project::create([
            'project_code' => 'PRJ-DEMO-0002',
            'project_name' => 'Godawari Land Boundary Wall',
            'client_id' => $clients['owner_three']->client_id,
            'property_id' => $properties['owner3_investment']->property_id,
            'assigned_engineer_staff_id' => $this->staff['Site Engineer / Survey Officer']->staff_id,
            'status' => 'completed',
            'start_date' => now()->subMonths(4)->toDateString(),
            'expected_end_date' => now()->subMonths(2)->toDateString(),
            'actual_end_date' => now()->subMonths(2)->toDateString(),
            'description' => 'Boundary wall construction for investment plot.',
            'created_by_staff_id' => $this->staff['Technical Manager']->staff_id,
        ]);
    }

    private function seedCms(): void
    {
        $teamMembers = [
            ['Bishnu Prasad Adhikari', 'Managing Director', 'Executive', 1],
            ['Sunita Rana', 'General Manager', 'Operations', 2],
            ['Rajesh Basnet', 'Finance Manager', 'Finance', 3],
            ['Priya Maharjan', 'Marketing Manager', 'Marketing', 4],
            ['Suresh Tamang', 'Technical Manager', 'Engineering', 5],
        ];
        foreach ($teamMembers as [$name, $designation, $department, $order]) {
            TeamMember::create([
                'name' => $name,
                'designation' => $designation,
                'department' => $department,
                'bio' => "{$name} brings years of real estate industry experience to API GharJagga.",
                'display_order' => $order,
                'is_active' => true,
            ]);
        }

        $testimonials = [
            ['Aarav Shrestha', 'Property Seller', 5, 'API GharJagga made selling my house incredibly smooth. Professional team throughout.'],
            ['Sushma Gurung', 'Property Buyer', 5, 'Found my dream apartment within two weeks. Highly recommended!'],
            ['Deepak Man Shakya', 'Investor', 4, 'Great investment advice and transparent valuation process.'],
            ['Anjali Tamang', 'Tenant', 4, 'Easy rental process and responsive customer support.'],
        ];
        foreach ($testimonials as $i => [$name, $role, $rating, $message]) {
            Testimonial::create([
                'client_name' => $name,
                'client_role' => $role,
                'rating' => $rating,
                'message' => $message,
                'is_featured' => $i < 2,
                'is_active' => true,
                'display_order' => $i + 1,
            ]);
        }

        $job1 = JobOpening::create([
            'title' => 'Site Engineer',
            'department' => 'Engineering',
            'location' => 'Kathmandu',
            'employment_type' => 'full_time',
            'description' => 'Conduct site inspections and manage construction project quality control.',
            'requirements' => "Bachelor's in Civil Engineering, 2+ years experience.",
            'posted_date' => now()->subDays(15)->toDateString(),
            'closing_date' => now()->addDays(15)->toDateString(),
            'is_active' => true,
        ]);
        $job2 = JobOpening::create([
            'title' => 'Sales & Brokerage Representative',
            'department' => 'Sales',
            'location' => 'Lalitpur',
            'employment_type' => 'full_time',
            'description' => 'Manage client relationships and facilitate property sales.',
            'requirements' => "Bachelor's degree, excellent communication skills.",
            'posted_date' => now()->subDays(10)->toDateString(),
            'closing_date' => now()->addDays(20)->toDateString(),
            'is_active' => true,
        ]);

        JobApplication::create([
            'job_id' => $job1->job_id,
            'applicant_name' => 'Rabin Khatri',
            'email' => 'rabin.khatri@example.com',
            'phone' => '9800011122',
            'resume_path' => 'demo/careers/rabin-khatri-resume.pdf',
            'status' => 'shortlisted',
            'applied_at' => now()->subDays(8),
        ]);
        JobApplication::create([
            'job_id' => $job1->job_id,
            'applicant_name' => 'Sarita Poudel',
            'email' => 'sarita.poudel@example.com',
            'phone' => '9800011123',
            'resume_path' => 'demo/careers/sarita-poudel-resume.pdf',
            'status' => 'received',
            'applied_at' => now()->subDays(3),
        ]);
        JobApplication::create([
            'job_id' => $job2->job_id,
            'applicant_name' => 'Manoj Shrestha',
            'email' => 'manoj.shrestha@example.com',
            'phone' => '9800011124',
            'resume_path' => 'demo/careers/manoj-shrestha-resume.pdf',
            'status' => 'reviewed',
            'applied_at' => now()->subDays(5),
        ]);

        $blogPosts = [
            ['Understanding Property Valuation in Nepal', 'A guide to how market value, forced-sale value, and government valuation differ.', 'Market Insights'],
            ['5 Tips for First-Time Home Buyers', 'What to check before signing a sale-purchase agreement.', 'Buying Guide'],
            ['Why KYC Verification Matters for Property Transactions', 'How digital KYC speeds up safe, fraud-free deals.', 'Company News'],
        ];
        foreach ($blogPosts as $i => [$title, $excerpt, $category]) {
            BlogPost::create([
                'title' => $title,
                'slug' => Str::slug($title),
                'excerpt' => $excerpt,
                'content' => "<p>{$excerpt}</p><p>This is demo content for the {$title} article, covering practical guidance for API GharJagga clients.</p>",
                'category' => $category,
                'author_staff_id' => $this->staff['Marketing Manager']->staff_id,
                'is_published' => $i < 2,
                'published_at' => $i < 2 ? now()->subDays(20 - $i * 5) : null,
            ]);
        }

        $album1 = GalleryAlbum::create(['title' => 'Office Launch Event', 'description' => 'Photos from our new Kathmandu office launch.', 'is_active' => true]);
        $album2 = GalleryAlbum::create(['title' => 'Completed Projects', 'description' => 'Showcase of our recent construction and renovation work.', 'is_active' => true]);

        GalleryImage::create(['album_id' => $album1->album_id, 'image_path' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=1200&q=80', 'caption' => 'Ribbon cutting ceremony', 'display_order' => 1]);
        GalleryImage::create(['album_id' => $album1->album_id, 'image_path' => 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=1200&q=80', 'caption' => 'Team photo', 'display_order' => 2]);
        GalleryImage::create(['album_id' => $album2->album_id, 'image_path' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80', 'caption' => 'Baluwatar renovation — before', 'display_order' => 1]);

        SiteDocument::create(['title' => 'Company Brochure 2026', 'category' => 'Marketing', 'file_path' => 'demo/documents/company-brochure-2026.pdf', 'is_public' => true, 'uploaded_at' => now()->subMonths(2)]);
        SiteDocument::create(['title' => 'Property Listing Terms & Conditions', 'category' => 'Legal', 'file_path' => 'demo/documents/listing-terms.pdf', 'is_public' => true, 'uploaded_at' => now()->subMonths(3)]);
        SiteDocument::create(['title' => 'Internal Valuation Guidelines', 'category' => 'Internal', 'file_path' => 'demo/documents/internal-valuation-guidelines.pdf', 'is_public' => false, 'uploaded_at' => now()->subMonths(1)]);

        ContactMessage::create(['name' => 'Kritika Basnet', 'email' => 'kritika.basnet@example.com', 'phone' => '9800022233', 'subject' => 'General inquiry', 'message' => 'Do you handle properties outside Kathmandu Valley?', 'status' => 'new']);
        ContactMessage::create(['name' => 'Ramesh Karki', 'email' => 'ramesh.karki@example.com', 'phone' => '9800022234', 'subject' => 'Partnership opportunity', 'message' => "I'd like to discuss a contractor partnership for your construction projects.", 'status' => 'contacted', 'admin_note' => 'Forwarded to Technical Manager.']);
        ContactMessage::create(['name' => 'Sabnam Rai', 'email' => 'sabnam.rai@example.com', 'subject' => 'Careers question', 'message' => 'Are you hiring remote marketing interns?', 'status' => 'closed', 'admin_note' => 'Replied — no remote positions currently.']);
    }
}
