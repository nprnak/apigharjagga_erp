<x-filament-panels::page>
    <style>
        .custom-dashboard-wrapper {
            width: 100%;
            max-width: 100%;
        }

        .dashboard-grid-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            width: 100%;
        }

        @media (min-width: 1024px) {
            .dashboard-grid-container {
                grid-template-columns: 2fr 1fr;
            }
        }

        .dashboard-col-main {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            min-width: 0;
        }

        .dashboard-col-sidebar {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            min-width: 0;
        }

        .dashboard-stats-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        @media (min-width: 640px) {
            .dashboard-stats-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .dashboard-bottom-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 768px) {
            .dashboard-bottom-row {
                grid-template-columns: 1fr 1fr;
            }
        }

        .dashboard-quick-actions-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .dash-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);
            transition: all 0.2s ease-in-out;
        }

        .dash-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.06);
        }
    </style>

    <div class="custom-dashboard-wrapper">
        <div class="dashboard-grid-container">
            <!-- Left Main Content (2 Columns Wide) -->
            <div class="dashboard-col-main">
                <!-- 1. Welcome Banner -->
                @include('filament.user.widgets.welcome-banner')

                <!-- 2. 4 Stat Cards in 2x2 Grid -->
                @include('filament.user.widgets.stat-cards-grid')

                <!-- 3. Bottom Row: Chart & Quick Actions -->
                <div class="dashboard-bottom-row">
                    <div>
                        @livewire(\App\Filament\User\Widgets\ListingsStatusChart::class)
                    </div>
                    <div>
                        @include('filament.user.widgets.quick-actions')
                    </div>
                </div>
            </div>

            <!-- Right Sidebar Content (1 Column Wide) -->
            <div class="dashboard-col-sidebar">
                <!-- 4. User Profile Card -->
                @include('filament.user.widgets.user-profile-card')

                <!-- 5. Interactive KYC Stepper -->
                @include('filament.user.widgets.kyc-progress-stepper')

                <!-- 6. Quick Support Card -->
                @include('filament.user.widgets.quick-support-card')
            </div>
        </div>
    </div>
</x-filament-panels::page>
