<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\WalletHistory;

class AlterWalletHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $wallet_histories = WalletHistory::all();

        foreach ($wallet_histories as $history) {
            if ($history->activity_data) {
                $activity_data = json_decode($history->activity_data, true);
                
                if (is_array($activity_data)) {
        
                    $creditActivities = ['add_wallet', 'update_wallet', 'wallet_top_up', 'wallet_refund'];
                    $debitActivities = ['wallet_payout_transfer','withdraw_money'];
        
                    if (!isset($activity_data['credit_debit_amount'])) {
                        $activity_data['credit_debit_amount'] = $activity_data['amount'];
                    }
        
                    if (!isset($activity_data['transaction_type'])) {
                        if (in_array($history->activity_type, $creditActivities)) {
                            $activity_data['transaction_type'] = 'Credit';
                        } elseif (in_array($history->activity_type, $debitActivities)) {
                            $activity_data['transaction_type'] = 'Debit';
                        }
                    }
        
                    $history->activity_data = json_encode($activity_data);
                    $history->save();
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
