<?php

namespace App\Traits;

use App\Models\User;
use App\Models\ProviderPayout;
use App\Models\CommissionEarning;


trait EarningTrait {

    public function get_provider_commission($bookings,$provider){
      

        $total_amount=$bookings->sum('total_amount');

        $provider_earning=$this->getProviderCommission($bookings);

        $provider_paid_earning = ProviderPayout::where('provider_id',$provider->id)->sum('amount') ?? 0;

        $handyman_earnings=$this->getHandymanCommission($bookings);

        $data=[
             'total_earning'=>$total_amount,
             'provider_total_earning'=> $provider_earning,
             'provider_paid_earning'=>  $provider_paid_earning,
             'provider_due_earning'=>$provider_earning - $provider_paid_earning,
             'admin_earning'=>$total_amount-$handyman_earnings-$provider_earning,
             
        ];

        return $data;
    }

    public function getProviderCommission($bookings)
    {
        $providerEarning = 0;
    
        foreach ($bookings as $booking) {
            $commissionData = json_decode($booking->commission_data, false);
    
            if ($commissionData) {
                if ($commissionData->type == 'percent') {
                    $providerEarning += ($booking->total_amount * $commissionData->commission) / 100;
                } else {
                    $providerEarning += $commissionData->commission;
                }
            }
        }
    
        return $providerEarning;
    }


    public function getHandymanCommission($bookings)
    {
        $handymanEarning = 0;
    
        foreach ($bookings as $booking) {

            $providerId = $booking->provider_id;

            $handyman = $booking->handymanAdded()->where('handyman_id', '!=', $providerId)->get();
  
                foreach ($handyman as $handyman) {

                    $commissionData = json_decode($handyman->commission_data, false);

                    if( $commissionData){

                        if ($commissionData->type == 'percent') {

                            $handymanEarning += ($booking->total_amount * $commissionData->commission) / 100;

                        }else{

                            $handymanEarning += $commissionData->commission;

                        }  

                    }
                }
           }

        return $handymanEarning;
    }


    public function getProviderBookingCommission($booking, $payment,$handyman_earning)
    {
        $provider_commission_data = [];
        $providerEarning = 0;
    
        $provider = User::where('id', $booking['provider_id'])->with('providertype')->first();
        $provider_commission = $provider->providertype ? json_encode($provider->providertype) : null;
        $commissionData = json_decode($provider_commission, false);
    
        if ($commissionData) {
            if ($commissionData->type === 'percent') {
                $providerEarning += ($booking->final_sub_total * $commissionData->commission) / 100;
            } else {
                $providerEarning += $commissionData->commission;
            }
        }
        if($handyman_earning > 0){
            $providerEarning = $providerEarning - $handyman_earning;
        }
        $payment_status = 'pending';
        if($payment && $payment->payment_status === 'paid'){ 
            $payment_status = 'unpaid';
        }
    
        $provider_commission_data = [
            'employee_id'       => $booking->provider_id,
            'booking_id'        => $booking->id,
            'user_type'         => 'provider',
            'commission_amount' => $providerEarning,
            'commission_status' => $payment_status,
            'commissions'       => $provider_commission
        ];
    
        return $provider_commission_data;
    }
    

    public function getHandymanBookingCommission($booking, $payment,$provider_earning)
    {
        $handyman_commission_data = [];
        $handymanEarning = 0;
    
        $providerId = $booking->provider_id;
        $handymen = $booking->handymanAdded()->where('handyman_id', '!=', $providerId)->get();

    
        foreach ($handymen as $handyman) {
            $handymanData = User::where('id', $handyman->handyman_id)->with('handymantype')->first();

            $handyman_commission = $handymanData->handymantype ? json_encode($handymanData->handymantype) : null;
            $commissionData = json_decode($handyman_commission, false);
    
            if ($commissionData) {
                if ($commissionData->type === 'percent') {
                    $handymanEarning = 0;
                    if($provider_earning > 0){
                        $handymanEarning += ($provider_earning * $commissionData->commission) / 100;
                    }
                    
                } else {
                    $handymanEarning += $commissionData->commission;
                }
            }

            $payment_status = 'pending';
            if($payment && $payment->payment_status === 'paid'){ 
                $payment_status = 'unpaid';
            }
    
            $handyman_commission_data[] = [
                'employee_id'       => $handyman->handyman_id,
                'booking_id'        => $booking->id,
                'user_type'         => 'handyman',
                'commission_amount' => $handymanEarning,
                'commission_status' => $payment_status,
                'commissions'       => $handyman_commission
            ];

    
        }
    
        return $handyman_commission_data;
    }
    

    public function addBookingCommission($bookingdata)
    {
        $payment = $bookingdata->payment;
        $provider_earning = 0;
        $handyman_earning = 0;

        if ($bookingdata->provider_id) {
            $provider_commission_data = $this->getProviderBookingCommission($bookingdata, $payment,$handyman_earning);
            $provider_earning = $this->saveCommission($provider_commission_data);
        }

        if ($bookingdata->handymanAdded) {
            $handyman_commission_data = $this->getHandymanBookingCommission($bookingdata, $payment,$provider_earning);
            foreach ($handyman_commission_data as $commission_data) {
                $handyman_earning += $this->saveCommission($commission_data);
            }
            if($handyman_earning > 0){
                $provider_commission_data = $this->getProviderBookingCommission($bookingdata, $payment,$handyman_earning);
                $provider_earning = $this->saveCommission($provider_commission_data);
            }
        }

        $payment_status = $payment && $payment->payment_status == 'paid' ? 'unpaid' : 'pending';

        $admin_earning = $bookingdata->final_sub_total - $provider_earning - $handyman_earning;
        $admin_commission_data = [
            'employee_id'       => User::where('user_type', 'admin')->value('id'),
            'booking_id'        => $bookingdata->id,
            'user_type'         => 'admin',
            'commission_amount' => $admin_earning,
            'commission_status' => $payment_status,
            'commissions'       => null
        ];
        $this->saveCommission($admin_commission_data);
    }

    // protected function saveCommission($commission_data)
    // {
    //     $commission = new CommissionEarning;
    //     $commission->fill($commission_data);
    //     $commission->save();
    //     return $commission->commission_amount;
    // }

    protected function saveCommission($commission_data)
    {
        // Check if a commission already exists for the booking and user type
        $commission = CommissionEarning::where('booking_id', $commission_data['booking_id'])
                                    ->where('user_type', $commission_data['user_type'])
                                    ->where('employee_id', $commission_data['employee_id'])
                                    ->first();

        // If commission exists, update it; otherwise, create a new one
        if ($commission) {
            $commission->fill($commission_data);  // Update the existing commission data
        } else {
            $commission = new CommissionEarning;
            $commission->fill($commission_data);  // Create a new commission
        }

        // Save or update the commission record
        $commission->save();

        // Return the commission amount for further processing
        return $commission->commission_amount;
    }


}



    

?>