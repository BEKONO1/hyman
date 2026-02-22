<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WalletHistory;
use App\Models\Wallet;
use App\Http\Resources\API\WalletHistoryResource;
use App\Http\Resources\API\WalletResource;
use App\Traits\NotificationTrait;
use Carbon\Carbon;
use App\Models\WithdrawMoney;
use App\Models\PaymentGateway;
use App\Models\User;
use Validator;
use App\Models\Bank;

class WalletController extends Controller
{
    use NotificationTrait;
    public function getHistory(Request $request)
    {
        $user_id = $request->user_id ?? auth()->user()->id;

        $wallet_history = WalletHistory::with('providers')->where('user_id', $user_id);
        $per_page = config('constant.PER_PAGE_LIMIT');

        $orderBy = $request->orderby ? $request->orderby : 'asc';

        if ($request->has('per_page') && !empty($request->per_page)) {
            if (is_numeric($request->per_page)) {
                $per_page = $request->per_page;
            }
            if ($request->per_page === 'all') {
                $per_page = $wallet_history->count();
            }
        }
        
        $wallet_history = $wallet_history->orderBy('id', $orderBy)->paginate($per_page);
        $items = WalletHistoryResource::collection($wallet_history);
        $wallet_balance = Wallet::where('user_id', $user_id)->value('amount');
        $response = [
            'pagination' => [
                'total_items' => $items->total(),
                'per_page' => $items->perPage(),
                'currentPage' => $items->currentPage(),
                'totalPages' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
                'next_page' => $items->nextPageUrl(),
                'previous_page' => $items->previousPageUrl(),
            ],
            'data' => $items,
            'available_balance' => $wallet_balance,
        ];

        return comman_custom_response($response);
    }

    public function walletTopup(Request $request)
    {
        $request->validate([
            'amount' => 'required',

        ]);

        $user_id = $request->user_id ?? auth()->user()->id;

        $wallet = Wallet::where('user_id', $user_id)->first();
    
        if (!$wallet) {
            $user = User::where('id', $user_id)->first();

            if ($user && $user->user_type == 'user' || $user->user_type = "provider") {
                $wallet = Wallet::create([
                    'title' => $user->display_name,
                    'user_id' => $user->id,
                    'amount' => 0,
                ]);
            } else {
                return comman_custom_response(['error' => 'User not found or invalid user type']);
            }
        }
        
        $wallet->amount += $request->amount;
        
        $wallet->save();

        $activity_data = [
            'activity_type' => 'wallet_top_up',
            'wallet' => $wallet,
            'top_up_amount' => $request->amount,
            'transaction_type' => $request->transaction_type,
            'transaction_id' => $request->transaction_id,
        ];

        $this->sendNotification($activity_data);

        $response = [
            'message' => trans('messages.wallet_top_up', ['amount' => getPriceFormat($wallet->amount)]),
            'data' => $wallet,
        ];

        return comman_custom_response($response);
    }


    public function getwalletlist(Request $request)
    {
        $wallet = Wallet::query();

        if ($request->has('status') && !empty($request->status)) {

            $wallet = $wallet->where('status', $status);
        }

        $per_page = config('constant.PER_PAGE_LIMIT');

        if ($request->has('per_page') && !empty($request->per_page)) {
            if (is_numeric($request->per_page)) {
                $per_page = $request->per_page;
            }
            if ($request->per_page === 'all') {
                $per_page = $wallet->count();
            }
        }

        $wallet = $wallet->orderBy('updated_at', 'desc')->paginate($per_page);
        $items = WalletResource::collection($wallet);

        $response = [
            'pagination' => [
                'total_items' => $items->total(),
                'per_page' => $items->perPage(),
                'currentPage' => $items->currentPage(),
                'totalPages' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
                'next_page' => $items->nextPageUrl(),
                'previous_page' => $items->previousPageUrl(),
            ],
            'data' => $items,
        ];

        return comman_custom_response($response);
    }
    public function store(Request $request)
    {

        if (demoUserPermission()) {
            $message = __('messages.demo_permission_denied');
            return comman_message_response($message);
        }
        $data = $request->all();

        $wallet = Wallet::where('user_id', $data['user_id'])->first();
        if ($wallet && !$data['id']) {
            $message = __('messages.already_provider_wallet');
            return comman_message_response($message, 406);
        }
        if ($wallet !== null) {
            $data['amount'] = $wallet->amount + $request->amount;
        }
        $result = Wallet::updateOrCreate(['id' => $data['id']], $data);


        $message = trans('messages.update_form', ['form' => trans('messages.wallet')]);
        if ($result->wasRecentlyCreated) {
            $activity_data = [
                'activity_type' => 'add_wallet',
                'wallet' => $result,
            ];
            $this->sendNotification($activity_data);

            $message = trans('messages.save_form', ['form' => trans('messages.wallet')]);
        } else {
            if ($wallet->amount  != $data['amount']) {
                $activity_data = [
                    'activity_type' => 'update_wallet',
                    'wallet' => $result,
                    'added_amount' => $request->amount
                ];
                $this->sendNotification($activity_data);
            }
        }

        return comman_message_response($message);
    }

    public function withdrawMoney(Request $request){


        $data = $request->except('_token');

        $payout_status='';
        $status = '';

        $payment_gateway= $data['payment_gateway'];

        $user_id=$data['user_id'];
        
        // Get bank details if payment method is bank
        $bank_name = '';
        if($data['payment_method'] === 'bank') {
            $bank = Bank::find($data['bank']);
            $bank_name = $bank ? $bank->bank_name : '';
        }

        $wallet = Wallet::where('user_id', $user_id)->first();

        if (!$wallet) {
            return comman_message_response('Wallet not found for user.');
        } 
        if ($wallet->amount < $request->amount) {
            return comman_message_response('Insufficient balance to withdraw.');
        }

        if($data['payment_method'] === 'bank'){

            switch($payment_gateway){

                case 'razorpayx':
                    $razorPayX = PaymentGateway::where('type','razorPayX')->where('status',1)->first();
                    if($razorPayX == null){
                        return comman_message_response(__('messages.transfer_admin_contact') ,406);

                    }
                    
                    $response=providerpayout_rezopayX($data);

                    if($response==''){
                        $data['bank_id'] = $data['bank'];
                        $data['payment_type'] = $payment_gateway;
                        $data['datetime'] = Carbon::now();
                        $data['status'] = 'failed';
                        $result = WithdrawMoney::create($data);
                        return comman_message_response(trans('messages.rezorpayx_details'));

                    }

                    $payout_details = json_decode($response,True);
                    $payout = $payout_details;

                    if (isset($payout_details['status']) && $payout_details['status'] == 'processing') {
                        $data['status'] = 'paid';
                    }
                    
                    if($error=$payout['error']['description']  ==''){

                        $payout_id=$payout['id'] ;
                        $data['paid_date']=Carbon::now();

                    }
                    else {
                        $razorpay_message=$payout['error']['description'];
                        if($payout['error']['code'] == 'BAD_REQUEST_ERROR'){
                            return comman_message_response(trans('messages.razorpay_message',['razorpay_message' => $razorpay_message]) ,406);
                        }
                        $data['bank_id'] = $data['bank'];
                        $data['payment_type'] = $payment_gateway;
                        $data['datetime'] = Carbon::now();
                        $data['status'] = 'failed';
                        $result = WithdrawMoney::create($data);

                        return comman_message_response(trans('messages.razorpay_message',['razorpay_message' => $razorpay_message]));

                    }
               break;

               case 'stripe':

                    $response=providerpayout_stripe($data);

                    if($response==''){

                        return comman_message_response(trans('messages.stripe_details'));

                    }
                    else{

                        $status = $response->status;

                        if($status==400){

                            $error_message = $response->code;

                            $data['bank_id'] = $data['bank'];
                            $data['payment_type'] = $payment_gateway;
                            $data['datetime'] = Carbon::now();
                            $data['status'] = 'failed';
                            $result = WithdrawMoney::create($data);

                            return comman_message_response(trans('messages.stripe_message',['stripe_message' => $error_message]));

                        }
                        else{
                            $payout_id=$response['id'];

                            $status='';

                            if($payout_id!=''){

                                $status="paid";
                            }

                            $data['bank_id']=$data['bank'];
                            $data['status']=$status;
                            $data['paid_date']=Carbon::now();

                        }
                    }
                break;

            }
         }
         if($data['payment_method'] === 'bank'){
            $data['bank_id'] = $data['bank'];
         }else{
             $data['bank_id'] = 0;
         }
        
        $data['payment_type'] = $payment_gateway;
        $data['datetime'] = Carbon::now();
        $result = WithdrawMoney::create($data);
        $wallet->amount -= $data['amount'];
        $wallet->save();
        $activity_data = [
            'id' => $result->id,
            'type' => 'wallet',
            'wallet' => $wallet,
            'activity_type' => 'withdraw_money',
            'user_id' => $data['user_id'],
            'amount' => $data['amount'],
        ];
        $this->sendNotification($activity_data);

        if ( request()->is('api*')){
            $message = __('messages.money_transfer');
            return comman_message_response($message);
          }
    }
}
