<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');

        if (env('IS_DUMMY_DATA') == false) {
            // \App\Models\User::factory(10)->create();
            $this->call([
                AppSettingsTableSeeder::class,
                RoleTableSeeder::class,
                UsersTableSeeder::class,
                // ModelHasRolesTableSeeder::class,
                PermissionTableSeeder::class,
                RoleHasPermissionsTableSeeder::class,
                ModelHasPermissionsTableSeeder::class,
                CountriesTableSeeder::class,
                StatesTableSeeder::class,
                CitiesTableSeeder::class,
                BookingStatusesTableSeeder::class,
                SettingsTableSeeder::class,
                ProviderTypesTableSeeder::class,
                HandymanTypesTableSeeder::class,
                StaticDataSeeder::class,
                NotificationTemplateSeeder::class,
                MailTemplateSeeder::class,
                PaymentGatewaysTableSeeder::class,
                AppDownloadsTableSeeder::class,
                SettingsTableSeeder::class,

            ]);
        } else {

            $this->call([
                AppSettingsTableSeeder::class,
                RoleTableSeeder::class,
                ServiceZonesTableSeeder::class,
                UsersTableSeeder::class,
                ModelHasRolesTableSeeder::class,
                PermissionTableSeeder::class,
                RoleHasPermissionsTableSeeder::class,
                ModelHasPermissionsTableSeeder::class,
                CountriesTableSeeder::class,
                StatesTableSeeder::class,
                CitiesTableSeeder::class,
                BookingStatusesTableSeeder::class,
                SettingsTableSeeder::class,
                ProviderTypesTableSeeder::class,
                HandymanTypesTableSeeder::class,
                PlansTableDataSeeder::class,
                StaticDataSeeder::class,
                CategoriesTableSeeder::class,
                SubCategoriesTableSeeder::class,
                ServicesTableSeeder::class,
                ServicePackagesTableSeeder::class,
                PackageServiceMappingsTableSeeder::class,
                SlidersTableSeeder::class,
                PostRequestStatusesTableSeeder::class,
                NotificationTemplateSeeder::class,
                MailTemplateSeeder::class,
                BookingsTableSeeder::class,
                BookingRatingsTableSeeder::class,
                BookingActivitiesTableSeeder::class,
                BookingCouponMappingsTableSeeder::class,
                BookingExtraChargesTableSeeder::class,
                BookingHandymanMappingsTableSeeder::class,
                BookingPackageMappingsTableSeeder::class,
                HandymanRatingsTableSeeder::class,
                PostJobRequestsTableSeeder::class,
                PostJobServiceMappingsTableSeeder::class,
                PostJobBidsTableSeeder::class,
                TaxesTableSeeder::class,
                PaymentsTableSeeder::class,
                BanksTableSeeder::class,
                ProviderPayoutsTableSeeder::class,
                DocumentsTableSeeder::class,
                ProviderDocumentsTableSeeder::class,
                ProviderTaxesTableSeeder::class,
                ProviderSlotMappingsTableSeeder::class,
                PlanLimitsTableSeeder::class,
                PaymentGatewaysTableSeeder::class,
                CouponsTableSeeder::class,
                CouponServiceMappingsTableSeeder::class,
                AppDownloadsTableSeeder::class,
                WalletsTableSeeder::class,
                WalletHistoriesTableSeeder::class,
                FrontendSettingTableSeeder::class,
                ServiceFaqsTableSeeder::class,
                ServiceAddonsTableSeeder::class,
                ServiceProofsTableSeeder::class,
                ProviderSubscriptionsTableSeeder::class,
                PaymentHistoriesTableSeeder::class,
                UserFavouriteServicesTableSeeder::class,
                SettingsTableSeeder::class,
                UserFavouriteProvidersTableSeeder::class,
                PromotionalBannerSeeder::class,
            ]);
            $this->call(BlogsTableSeeder::class);
            $this->call(BookingHandymanMappingsTableSeeder::class);
        }

        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('storage:link');
    }
}
