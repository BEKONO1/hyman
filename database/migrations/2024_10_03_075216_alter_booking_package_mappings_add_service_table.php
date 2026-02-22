<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\BookingPackageMapping;

class AlterBookingPackageMappingsAddServiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_package_mappings', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_package_mappings', 'services')) {
                $table->longText('services')->nullable();
            }
        });

        $bookingPackages = BookingPackageMapping::with('package.packageServices.service')->get();

        // Iterate over each booking package mapping
        foreach ($bookingPackages as $bookingPackage) {
            $services = [];

            // Get the services and prices for the package
            if ($bookingPackage->package && $bookingPackage->package->packageServices) {
                foreach ($bookingPackage->package->packageServices as $packageService) {
                    $services[] = [
                        'service_id' => optional($packageService->service)->id,
                        'price' => optional($packageService->service)->price,
                    ];
                }
            }
            // Update the 'services' column with the JSON data
            $bookingPackage->update([
                'services' => json_encode($services), // Store JSON data
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_package_mappings', function (Blueprint $table) {
            $table->dropColumn('services');
        });
    }
}
