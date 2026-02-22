<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PaymentHistory;
use App\Models\Payment;
use App\Models\Setting;
use Facade\Ignition\DumpRecorder\Dump;
use Yajra\DataTables\DataTables;
use App\Models\Booking;
use App\Models\CommissionEarning;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = [
            'payment_status' => $request->payment_status,
        ];
        $pageTitle = __('messages.payments' );
        $assets = ['datatable'];

        return view('payment.index', compact('pageTitle','assets','filter'));
    }

    public function cashIndex($id)
    {
        $pageTitle = __('messages.list_form_title',['form' => __('messages.cash_history')] );
        $auth_user = authSession();
        $assets = ['datatable'];
        return view('paymenthistory.index', compact('pageTitle','assets','auth_user','id'));
    }

     public function paymenthistory_index_data(DataTables $datatable,$id){
        $query = PaymentHistory::where('payment_id',$id);

        if (auth()->user()->hasAnyRole(['admin'])) {
            $query->newquery();
        }

        return $datatable  ->eloquent($query)
        // ->editColumn('booking_id', function($payment) {
        //     return ($payment->booking_id != null && isset($payment->booking->service)) ? $payment->booking->service->name :'-';
        // })
        // ->filterColumn('booking_id',function($query,$keyword){
        //     $query->whereHas('booking.service',function ($q) use($keyword){
        //         $q->where('name','like','%'.$keyword.'%');
        //     });
        // })
        // ->editColumn('customer_id', function($payment) {
        //     return ($payment->booking != null && isset($payment->booking->customer)) ? $payment->booking->customer->display_name : '-';
        // })
        // ->filterColumn('customer_id', function ($query, $keyword) {
        //     $query->whereHas('booking', function ($q) use ($keyword) {
        //         $q->whereHas('customer', function ($c) use ($keyword) {
        //             $c->where('display_name', 'like', '%' . $keyword . '%');
        //         });
        //     });
        // })
        
        ->editColumn('sender_id', function($payment) {
            return ($payment->sender != null && isset($payment->sender)) ? $payment->sender->display_name : '-';
        })
        ->filterColumn('sender_id', function ($query, $keyword) {
            $query->whereHas('sender', function ($q) use ($keyword) {
                    $q->where('display_name', 'like', '%' . $keyword . '%');
            });
        })
        ->editColumn('receiver_id', function($payment) {
            return ($payment->receiver != null && isset($payment->receiver)) ? $payment->receiver->display_name : '-';
        })
        ->filterColumn('receiver_id', function ($query, $keyword) {
            $query->whereHas('receiver', function ($q) use ($keyword) {
                    $q->where('display_name', 'like', '%' . $keyword . '%');
            });
        })
        ->editColumn('datetime' , function ($query){
            $sitesetup = Setting::where('type','site-setup')->where('key', 'site-setup')->first();
            $datetime = json_decode($sitesetup->value);
            $date = date("$datetime->date_format $datetime->time_format", strtotime($query->datetime));
            return $date;
        })
        ->addIndexColumn()
        ->toJson();
     }

    public function cashDatatable(Request $request){
        $filter = [
            'payment_status' => $request->payment_status,
        ];
        $pageTitle = __('messages.cash_payments' );
        $assets = ['datatable'];
        return view('payment.cash', compact('pageTitle','assets','filter'));
    }

    public function cash_index_data(DataTables $datatable,Request $request)
    {
        $query = Payment::query()->myPayment();
        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('payment_status', $filter['column_status']);
            }
        }
        if (auth()->user()->hasAnyRole(['admin'])) {
            $query= $query->where('payment_type','cash')->newQuery();
        }

        return $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" onclick="dataTableRowCheck('.$row->id.')">';
            })
            ->editColumn('id', function($payment) {
                if(isset($payment->booking) && $payment->booking !== null){
                    return '<a class="btn-link btn-link-hover" href='.route('booking.show', $payment->booking->id).'> #'.$payment->booking->id.'</a>';
                }
            })
            ->editColumn('booking_id', function($payment) {
                if(!empty($payment->booking->bookingPackage)){
                    $service_name = optional(optional($payment->booking)->bookingPackage)->name." (".__('messages.service_package').")";
                }
                else{
                    $service_name = optional(optional($payment->booking)->service)->name." (".__('messages.service').")";
                }
     
                return ($payment->customer_id != null &&isset($payment->booking->service)) ? $service_name :'-';
            })
            ->filterColumn('booking_id',function($query,$keyword){
                $query->whereHas('booking.service',function ($q) use($keyword){
                    $q->where('name','like','%'.$keyword.'%');
                });
            })
            ->orderColumn('booking_id', function ($query, $order) {
                $query->join('bookings', 'bookings.id', '=', 'payments.booking_id')  
                      ->join('services', 'services.id', '=', 'bookings.service_id')  
                      ->orderBy('services.name', $order);  
            })
            ->editColumn('customer_id', function ($payment) {
                return view('payment.user', compact('payment'));
            })
            ->filterColumn('customer_id',function($query,$keyword){
                $query->whereHas('customer',function ($q) use($keyword){
                    $q->where('display_name','like','%'.$keyword.'%');
                });
            })
            ->editColumn('datetime' , function ($query){
                $sitesetup = Setting::where('type','site-setup')->where('key', 'site-setup')->first();
                $datetime = json_decode($sitesetup->value);
                $date = date("$datetime->date_format $datetime->time_format", strtotime($query->datetime));
                return $date;
            })
            ->editColumn('total_amount', function($payment) {
                return getPriceFormat($payment->total_amount);
            })
            ->editColumn('history', function($payment) {
                $action = '<a class="btn-link btn-link-hover" href="'.route('cash.index', $payment->id).'">'.__('messages.view').'</a>';
                return $action;
            })
            ->editColumn('status', function($query) {
                $payment = $query->payment_status;
                if($payment !== null){
                    $payment_status = '<span class="text-center bg badge-primary">'.str_replace('_'," ",ucfirst($payment)).'</span>';
                }else{
                    $payment_status = '<span class="text-center d-block">-</span>';
                }
                return $payment_status;
            })

            ->editColumn('action', function($payment) {
                $action=null;
               if(auth()->user()->hasRole(['admin']) || auth()->user()->hasRole(['demo_admin']) ){
                // $action = set_admin_approved_cash($payment->id). ' ' .view('payment.cashaction',compact('payment'))->render();
                $action = view('payment.cashaction',compact('payment'))->render();
               }
              
                return $action;

            })
            ->addIndexColumn()->rawColumns(['check','history','action','id','status'])
            ->toJson();
    }



    public function index_data(DataTables $datatable,Request $request)
    {
        $query = Payment::query()->myPayment(); 
        // if (!$request->order) { 
        //     $query->orderBy('created_at', 'DESC');
        // } 
        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('payment_status', $filter['column_status']);
            }
        }
        if (auth()->user()->hasAnyRole(['admin'])) {
            $query->newQuery();
        }

        return $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" onclick="dataTableRowCheck('.$row->id.')">';
            })
        ->editColumn('id', function($query) {

            return "<a class='btn-link btn-link-hover' href=" .route('booking.show', $query->booking->id).">#".$query->booking->id."</a>"; 
        })
        ->orderColumn('id', function($query, $order) {
            $query->orderBy('payments.booking_id', $order);  
        })
        ->editColumn('booking_id', function($query) {
            if(!empty($query->booking->bookingPackage)){
                $service_name = optional(optional($query->booking)->bookingPackage)->name." (".__('messages.service_package').")";
            }
            else{
                $service_name = optional(optional($query->booking)->service)->name." (".__('messages.service').")";
            }
 
            return ($query->customer_id != null &&isset($query->booking->service)) ? $service_name :'-';
        })
        ->filterColumn('booking_id',function($query,$keyword){
            $query->whereHas('booking.service',function ($q) use($keyword){
                $q->where('name','like','%'.$keyword.'%');
            });
        })
        ->orderColumn('booking_id', function ($query, $order) {
            $query->join('bookings', 'bookings.id', '=', 'payments.booking_id')  
                  ->join('services', 'services.id', '=', 'bookings.service_id')  
                  ->orderBy('services.name', $order);  
        })
        ->editColumn('customer_id', function ($payment) {
            return view('payment.user', compact('payment'));
        })
        ->filterColumn('customer_id',function($query,$keyword){
            $query->whereHas('customer',function ($q) use($keyword){
                $q->where('display_name','like','%'.$keyword.'%');
            });
        })
        ->orderColumn('customer_id', function ($query, $order) {
            $query->select('payments.*')
                  ->join('users as customers', 'customers.id', '=', 'payments.customer_id')
                  ->orderBy('customers.display_name', $order);   
        })
        ->editColumn('datetime' , function ($query){
            $sitesetup = Setting::where('type','site-setup')->where('key', 'site-setup')->first();
            $datetime = json_decode($sitesetup->value);
            $date = date("$datetime->date_format $datetime->time_format", strtotime($query->datetime));
            return $date;
        })
        ->editColumn('payment_status', function($query) {
            $payment = $query->payment_status;
            if($payment !== null){
                $payment_status = '<span class="text-center text-white badge bg-primary">'.str_replace('_'," ",ucfirst($payment)).'</span>';
            }else{
                $payment_status = '<span class="text-center d-block">-</span>';
            }
            return $payment_status;
        })


        ->editColumn('total_amount', function($query) {
            return getPriceFormat($query->total_amount);
        })
        ->addColumn('action', function($payment){
            return view('payment.action',compact('payment'))->render();
        })
        ->addIndexColumn()
        ->rawColumns(['action','check','payment_status','id'])
            ->toJson();
    }

    /* bulck action method */
    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = 'Bulk Action Updated';
        switch ($actionType) {

            case 'delete':
                Payment::whereIn('id', $ids)->delete();
                $message = 'Bulk Payment Deleted';
                break;

            default:
                return response()->json(['status' => false, 'message' => 'Action Invalid']);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(demoUserPermission()){
            return  redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $document = Payment::find($id);
        $msg= __('messages.msg_fail_to_delete',['name' => __('messages.payment')] );

        if( $document!='') {

            $document->delete();
            $msg= __('messages.msg_deleted',['name' => __('messages.payment')] );
        }
        if(request()->is('api/*')) {
            return comman_message_response($msg);
		}
        return comman_custom_response(['message'=> $msg, 'status' => true]);
    }

    public function cashApprove($id){

        $sitesetup = Setting::where('type','site-setup')->where('key', 'site-setup')->first();
        $admin = json_decode($sitesetup->value);
        $paymentdata = Payment::where('id',$id)->first();
        $parent_payment_history = PaymentHistory::where('status','pending_by_admin')
        ->where('payment_id',$id)->first();

        $payment_history = [
            'payment_id' => $id,
            'booking_id' => $paymentdata->booking_id,
            'action' => config('constant.PAYMENT_HISTORY_ACTION.ADMIN_APPROVED_CASH'),
            'type' => $parent_payment_history->type,
            'sender_id' => $parent_payment_history->sender_id,
            'receiver_id' => admin_id(),
            'total_amount' => $paymentdata->total_amount,
            'text' =>  __('messages.cash_approved',['amount' => getPriceFormat((float)$paymentdata->total_amount),'name' => get_user_name(admin_id())]),
            'status' => config('constant.PAYMENT_HISTORY_STATUS.APPROVED_ADMIN'),
            'parent_id' => $parent_payment_history->parent_id
        ];

     
        date_default_timezone_set( $admin->time_zone ?? 'UTC');
        $payment_history['datetime'] = date('Y-m-d H:i:s');

        if(!empty($paymentdata->txn_id)){
            $payment_history['txn_id'] =$paymentdata->txn_id;
        }
        if(!empty($paymentdata->other_transaction_detail)){
            $payment_history['other_transaction_detail'] =$paymentdata->other_transaction_detail;
        }
        $res = PaymentHistory::create($payment_history);
        // $parent_record = PaymentHistory::where('parent_id',$parent_payment_history->parent_id)->first();

        // $payment_record_data= PaymentHistory::where('payment_id',$id)->update(['status'=>'approved_by_admin']);

        // $parent_record->status = 'approved_by_admin';
        // $parent_record->update();

        $booking = Booking::where('id', $payment_history['booking_id'])->first();

        if($booking->status == 'completed'){

            CommissionEarning::where('booking_id',$booking->id)->update(['commission_status'=>'unpaid']);
        }

        $paymentdata->payment_status = 'paid';
        $paymentdata->update();

        $msg = __('messages.approve_successfully');
        return redirect()->back()->withSuccess($msg);
    }

}
