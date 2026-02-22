<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Booking;
use App\Models\ProviderPayout;
use App\Models\Wallet;
use App\Models\BookingHandymanMapping;
use App\Models\HandymanPayout;
use App\Models\HandymanType;
use App\Traits\EarningTrait;
use App\Models\CommissionEarning;

class AlterPendingProviderEarning extends Migration
{
    use EarningTrait;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_address_mappings', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('booking_extra_charges', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('booking_package_mappings', function (Blueprint $table) {
            $table->softDeletes();
        });
        // Provider wallet
        $providers = User::with('providertype', 'wallet')->where('user_type','provider')->get();

        foreach ($providers as $provider) {
            $provider_commission = optional($provider->providertype)->commission;
            $provider_type = optional($provider->providertype)->type;

            $bookings = Booking::where('provider_id', $provider->id)
            ->where('status', 'completed')
            ->whereNotNull('payment_id')
            ->get();

            $booking_data = get_provider_commission($bookings);

            $providerEarning = ProviderPayout::where('provider_id',$provider->id)->sum('amount') ?? 0;

            $provider_earning = calculate_commission($booking_data['total_amount'],$provider_commission,$provider_type,'provider', $providerEarning,$bookings->count());

            $wallet = Wallet::where('user_id', $provider->id)->first();
            if ($wallet) {
                $wallet->amount += $provider_earning['number_format'];
                $wallet->save();
            }
             
            if($provider_earning['number_format'] > 0){
                $providerpayout = new ProviderPayout;
                $providerpayout->provider_id = $provider->id;
                $providerpayout->payment_method = 'wallet';
                $providerpayout->amount = $provider_earning['number_format'];
                $providerpayout->paid_date = now();
                $providerpayout->save();
            }

            foreach($bookings as $booking){
                $this->addBookingCommission($booking);

                $commission_earnings = CommissionEarning::where('booking_id', $booking->id)->get();
                
                foreach($commission_earnings as $commission_earning){
                    if($commission_earning){
                        $commission_earning->commission_status = 'paid';
                        $commission_earning->save();
                    }
                }
            }


            // Handyman wallet
            $bookings = Booking::with('handymanAdded')->has('handymanAdded')->where('provider_id',$provider->id)->whereNotNull('payment_id')->get();
            $provider_earning = ProviderPayout::where('provider_id',$provider->id)->sum('amount') ?? 0;

            $handyman = collect($bookings)->map(function ($handyman) {
            return [$handyman->handymanAdded->pluck('handyman_id')];
            })->toArray();

            $userIds = [];
            foreach ($handyman  as $item) {
                $userId = $item[0]->get(0);
                $userIds[] = $userId;

            }

            $user = User::whereIn('id',array_values($userIds))->where('user_type', 'handyman')->get();

            foreach ($user as $key => $value) {
        
                $handymantype_id  = !empty($value->handymantype_id) ? $value->handymantype_id : 1;

                $handyman_type = HandymanType::withTrashed()->where('id',$handymantype_id)->first();

                $commission =  $handyman_type->commission;

                $commission_type = $handyman_type->type;

                $handyman_bookings = BookingHandymanMapping::with('bookings')->where('handyman_id',$value->id)->whereHas('bookings',function ($q){
                    $q->whereNotNull('payment_id');
                })->get();

                $totalEarning = HandymanPayout::where('handyman_id',$value->id)->sum('amount') ?? 0;

                $all_booking_total = $handyman_bookings->map(function ($booking) {
                    return optional($booking->bookings)->total_amount;
                })->toArray();

                $total = array_reduce($all_booking_total, function ($value1, $value2) {
                    return $value1 + $value2;
                }, 0);

                $earning =   ($commission * count($handyman_bookings));

                if($commission_type === 'percent'){
                    $earning =  ($total) * $commission / 100;
                }

                $final_amount = $earning - $totalEarning;

                if(floor($final_amount) <= 0){
                    $final_amount = 0;
                }

                $wallet = new Wallet;
                $wallet->title = $value->first_name.' '.$value->last_name.' handyman wallet';
                $wallet->user_id = $value->id;
                $wallet->amount = $final_amount;
                $wallet->save();

                if(floor($final_amount) > 0){
                    $handymanpayout = new HandymanPayout;
                    $handymanpayout->handyman_id = $value->id;
                    $handymanpayout->payment_method = 'wallet';
                    $handymanpayout->amount = $final_amount;
                    $handymanpayout->paid_date = now();
                    $handymanpayout->save();
                }
            }
            
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
