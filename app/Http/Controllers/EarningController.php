<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use App\Models\ProviderPayout;
use App\Models\BookingHandymanMapping;
use App\Models\HandymanPayout;
use App\Models\HandymanType;
use Illuminate\Support\Arr;
use Yajra\DataTables\DataTables;
use App\Models\CommissionEarning;
class EarningController extends Controller
{
    public function index(){
        $pageTitle =  __('messages.earnings');
        return view('earning.index',compact('pageTitle'));
    }
    public function setEarningData(Request $request){
        $query = User::select('users.*')
                ->with('commission_earning')
                ->whereHas('commission_earning', function ($q) {
                    $q->whereIn('commission_status', ['paid', 'unpaid'])
                    ->where('user_type', 'provider');
                })->orderBy('updated_at', 'desc');

        $providers = $query->get();

        if($request->ajax()) {
            return Datatables::of($query)
            ->addIndexColumn()

            ->addColumn('provider_name', function($row){
                $user_id = $row->id;
                $user_name = $row->display_name;
                $user_image = getSingleMedia(optional($row),'profile_image', null);
                $email = $row->email;
                return view('earning.user', compact('row','user_id','user_name','email','user_image'));
            })
            ->orderColumn('provider_name', function ($query, $order) {
                $query->join('commission_earnings', 'commission_earnings.employee_id', '=', 'users.id')
                      ->whereIn('commission_earnings.commission_status', ['unpaid','paid'])
                      ->groupBy('users.id') // Group by user ID to avoid duplicates
                      ->orderBy('users.display_name', $order);
            })

            ->filterColumn('provider_name', function ($query, $keyword) {
                $query->where(function($q) use($keyword) {
                    $q->where('display_name', 'like', '%'.$keyword.'%')
                      ->orWhere('first_name', 'like', '%'.$keyword.'%')
                      ->orWhere('last_name', 'like', '%'.$keyword.'%')
                      ->orWhere('email', 'like', '%'.$keyword.'%');
                });
            })
                       

            ->addColumn('action', function($row) {
                $btn = '-';
                $provider_id = $row->id;

                $commissionData = $row->commission_earning()
                    ->whereHas('getbooking', function ($query) {
                        $query->where('status', 'completed');
                    })
                    ->where('commission_status', 'unpaid')
                    ->where('user_type', 'provider');



                // $totalBookings = $commissionData->distinct('booking_id')->count();


                $row['commission'] = $commissionData->get();
                // $row['total_bookings'] = $totalBookings;
                $ProviderEarning = 0;
                if($row['commission']->count() > 0){
                    foreach ($row['commission'] as $commission) {
                        if ($commission != null) {
                            // Fetch commission data for related bookings including handyman
                            $commission_data = CommissionEarning::where('booking_id', $commission->booking_id)
                                ->whereIn('user_type', ['provider', 'handyman'])
                                ->where('commission_status', 'unpaid')
                                ->get();

                            if ($commission_data) {
                                foreach ($commission_data as $data) {
                                    if (isset($data->commission_amount)) {
                                        $ProviderEarning += $data->commission_amount;
                                    }
                                }
                            }
                        }
                    }
                }

                $row['total_pay'] = $ProviderEarning;

                if($commissionData->count() > 0){
                    $btn = "<a href=". route('providerpayout.create',$provider_id) ."><i class='fas fa-money-bill-alt earning-icon'></i></a>";
                }

                return $btn;

            })
            // ->editColumn('commission', function ($row) {

            //     foreach($row['commission'] as $commission){
            //         $commissions = json_decode($commission->commissions);

            //         $commission = $commissions ? getPriceFormat($commissions->commission) : 0;
            //         if($commissions != null && $commissions->type == "percent"){
            //             $commission = $commissions->commission.''.'%';
            //         }
            //         return $commission ?? 0;
            //     }
            //     // return "<b><span  data-assign-module='".$row->id."' data-assign-commission-type='provider_commission' data-assign-target='#view_commission_list' data-assign-event='assign_commssions' class='btn text-primary p-0 fs-5' data-bs-toggle='tooltip' title='View'> <i class='ph ph-eye align-middle'></i></span>";

            //  })
            ->editColumn('total_bookings', function ($row) {
                $commissionData = $row->commission_earning()
                    ->whereHas('getbooking', function ($query) {
                        $query->where('status', 'completed');
                    })
                    ->whereIn('commission_status', ['unpaid','paid'])
                    ->where('user_type', 'provider');

                $totalBookings = $commissionData->distinct('booking_id')->count();
                $row['total_bookings'] = $totalBookings;
                if($row->total_bookings > 0){
                    return "<b><a href='" . route('booking.index', ['provider_id' => $row->id]) . "' data-assign-module='".$row->id."'  class='text-primary text-nowrap px-1' data-bs-toggle='tooltip' title='View Provider Bookings'>".$row->total_bookings."</a> </b>";
                }else{

                    return "<b><span  data-assign-module='".$row->id."'  class='text-primary text-nowrap px-1' data-bs-toggle='tooltip' title='View Provider Bookings'>0</span>";
                }

            })
            ->editColumn('total_earning', function ($row) {
                $commissionData = $row->commission_earning()
                ->whereHas('getbooking', function ($query) {
                    $query->where('status', 'completed');
                })
                ->whereIn('commission_status', ['unpaid','paid'])
                ->where('user_type', 'provider');

                $commissions = $commissionData->get();
                $totalServiceAmount = 0;
                foreach($commissions as $commission){
                    if($commission != null){
                        $bookingData = Booking::where('id', $commission->booking_id)->first();
                        if($bookingData != null){
                            $totalServiceAmount += $bookingData->final_sub_total;
                        }

                    }
                }
                $row['totalServiceAmount'] = $totalServiceAmount;

                return $totalServiceAmount ? getPriceFormat($totalServiceAmount) : getPriceFormat(0);
            })
            ->editColumn('admin_earning', function ($row) {
                $commissionData = $row->commission_earning()
                ->whereHas('getbooking', function ($query) {
                    $query->where('status', 'completed');
                })
                ->whereIn('commission_status', ['unpaid','paid'])
                ->where('user_type', 'provider');

                $commissions = $commissionData->get();
                $totalAdminEarning = 0;
                foreach($commissions as $commission){
                    if($commission != null){
                        $commission_data = CommissionEarning::where('booking_id', $commission->booking_id)->where('user_type', 'admin')->whereIn('commission_status', ['unpaid','paid'])->first();
                        if($commission_data != null){
                            $totalAdminEarning += $commission_data->commission_amount;
                        }

                    }
                }

                return $totalAdminEarning ? getPriceFormat($totalAdminEarning) : getPriceFormat(0);
            })
            ->editColumn('provider_earning', function ($row) {
                return $row->total_pay ? getPriceFormat($row->total_pay) : getPriceFormat(0);
            })
            ->editColumn('handyman_total_earning', function($row){

                $handyman_total_earning = 0;
                $commissions = $row->commission_earning()
                ->whereHas('getbooking', function ($query) {
                    $query->where('status', 'completed');
                })
                ->whereIn('commission_status', ['paid'])
                ->where('user_type', 'provider')->get();
                foreach($commissions as $commission){
                    if($commission != null){
                    $commission_data = CommissionEarning::where('booking_id', $commission->booking_id)->where('user_type', 'handyman')->where('commission_status', 'paid')->first();
                    if($commission_data){
                        $handyman_total_earning += $commission_data->commission_amount;
                    }
                    }
                }

                return $handyman_total_earning ? getPriceFormat( $handyman_total_earning) : getPriceFormat(0);
            })

            ->editColumn('provider_paid_earning', function($row){
                $commissionData = ProviderPayout::where('provider_id',$row->id)
                ->sum('amount');
                $provider_paid_earning = $commissionData ?? 0;
                // return getPriceFormat( $provider_paid_earning);
                return "<b><a href='" . route('providerpayout.show',$row->id) . "' data-assign-module='".$row->id."'  class='text-primary text-nowrap px-1' data-bs-toggle='tooltip' title='".__('messages.view_provider_payout')."'>".getPriceFormat( $provider_paid_earning)."</a> </b>";
            })



            ->rawColumns(['provider_name','action','total_bookings','commission','total_earning','provider_total_earning','provider_paid_earning','handyman_total_earning'])
            ->make(true);
        }
        if($request->is('api/*')) {

            $earningData = [];
            foreach($providers as $provider){
                $commissionData = $provider->commission_earning()
                    ->whereHas('getbooking', function ($query) {
                        $query->where('status', 'completed');
                    })
                    ->whereIn('commission_status', ['unpaid','paid'])
                    ->where('user_type', 'provider');

                //$providerEarning = $commissionData->sum('commission_amount');
                $totalBookings = $commissionData->distinct('booking_id')->count();
                $commissions = $commissionData->get();

                $totalEarning = 0;
                $adminEarning = 0;
                $totalTax = 0;
                foreach($commissions as $commission){
                    if ($commission != null) {
                        $bookingData = Booking::where('id', $commission->booking_id)->first();
                        $totalTax += $bookingData->final_total_tax;

                        // $totalEarning += $bookingData->total_amount;
                        if($bookingData != null){
                            $totalEarning += $bookingData->final_sub_total;
                        }

                        $commission_data = CommissionEarning::where('booking_id', $commission->booking_id)->where('user_type', 'admin')->whereIn('commission_status', ['unpaid','paid'])->first();
                        $adminEarning += $commission_data->commission_amount;




                    }
                }
                $providerCommissionData = $provider->commission_earning()
                    ->whereHas('getbooking', function ($query) {
                        $query->where('status', 'completed');
                    })
                    ->where('commission_status', 'unpaid')
                    ->where('user_type', 'provider')->get();
                    $providerDueAmount = 0;
                if($providerCommissionData->count() > 0){
                    foreach ($providerCommissionData as $commission) {
                        if ($commission != null) {
                            // Fetch commission data for related bookings including handyman
                            $provider_commission_data = CommissionEarning::where('booking_id', $commission->booking_id)
                            ->whereIn('user_type', ['provider', 'handyman'])
                            ->where('commission_status', 'unpaid')
                            ->get();

                                    if ($provider_commission_data) {
                                        foreach ($provider_commission_data as $data) {
                                            if (isset($data->commission_amount)) {
                                                $providerDueAmount += $data->commission_amount;
                                            }
                                        }
                                    }
                        }
                    }
                }

                $handymancommissionData = $provider->commission_earning()
                    ->whereHas('getbooking', function ($query) {
                        $query->where('status', 'completed');
                    })
                    ->whereIn('commission_status', ['paid'])
                    ->where('user_type', 'provider')->get();
                $handyman_total_earning = 0;
                if($handymancommissionData){
                    foreach($handymancommissionData as $commission){
                        if($commission != null){
                        $commission_data = CommissionEarning::where('booking_id', $commission->booking_id)->where('user_type', 'handyman')->where('commission_status', 'paid')->first();
                        if($commission_data){
                            $handyman_total_earning += $commission_data->commission_amount;
                        }
                        }
                    }
                }

                $provider_paid_earning = ProviderPayout::where('provider_id',$provider->id)->sum('amount');
                $provider_paid_earning = $provider_paid_earning ?? 0;
                $totalEarning = $totalEarning ? $totalEarning : 0;

                $adminEarning = $adminEarning ? $adminEarning : 0;

                $earningData[] = [
                    'provider_id' => $provider->id,
                    'provider_name' => $provider->display_name,
                    'provider_image' => getSingleMedia(optional($provider),'profile_image', null),
                    'email' => $provider->email,
                    'commission' => optional($provider->providertype)->commission,
                    'commission_type' => optional($provider->providertype)->type,
                    'total_bookings' => $totalBookings,
                    'total_earning' =>$totalEarning,
                    'taxes' => $totalTax,
                    'taxes_formate' =>  getPriceFormat($totalTax, 2),
                    'admin_earning' => $adminEarning,
                    'provider_paid_earning' => $provider_paid_earning,
                    'provider_paid_earning_formate' => $provider_paid_earning,
                    'provider_due_amount' => $providerDueAmount,
                    'handyman_total_amount' => $handyman_total_earning,
                ];
            }
            return comman_custom_response($earningData);
		}
    }





    public function handymanEarning(){
        $pageTitle =  __('messages.earning');
        return view('earning.handyman',compact('pageTitle'));
    }
    public function handymanEarningData(Request $request){
        $auth_user = authSession();

        $query = User::select('users.*')
                ->with('commission_earning')
                ->whereHas('commission_earning', function ($q) {
                    $q->whereIn('commission_status', ['paid', 'unpaid'])
                    ->where('user_type', 'handyman');
                })->orderBy('updated_at', 'desc');

        if (auth()->user()->hasAnyRole(['admin'])) {
            $query->withTrashed();
        }
        if (auth()->user()->hasAnyRole(['provider'])) {
            $query->where('provider_id', auth()->user()->id);
        }

        if ($request->ajax()) {
            return Datatables::of($query)
            ->addIndexColumn()

            ->addColumn('handyman_name', function($row){
                $user_id = $row->id;
                $user_name = $row->display_name;
                $user_image = getSingleMedia(optional($row),'profile_image', null);
                $email = $row->email;
                return view('earning.user', compact('row','user_id','user_name','email','user_image'));
            })

            ->addColumn('action', function($row){
                $btn = '-';
                $handyman_id = $row->id;

                $commissionData = $row->commission_earning()
                    ->whereHas('getbooking', function ($query) {
                        $query->where('status', 'completed');
                    })
                    ->where('commission_status', 'unpaid')
                    ->where('user_type', 'handyman');

                $commissionAmount = $commissionData->sum('commission_amount');
                // $totalBookings = $commissionData->distinct('booking_id')->count();

                $row['total_pay'] = $commissionAmount;
                $row['commission'] = $commissionData->get();
                // $row['total_bookings'] = $totalBookings;

                if($commissionData->count() > 0){
                    $btn = "<a href=". route('handymanpayout.create',$handyman_id) ."><i class='fas fa-money-bill-alt earning-icon'></i></a>";
                }

                return $btn;
            })
            // ->editColumn('commission', function ($row) {

            //     foreach($row['commission'] as $commission){
            //         if($commission != null){
            //             $commissions = json_decode($commission->commissions);
            //             if($commissions != null){
            //                 $commission = $commissions ? getPriceFormat($commissions->commission) : 0;
            //                 if($commissions->type == "percent"){
            //                     $commission = $commissions->commission.''.'%';
            //                 }
            //                 return $commission ?? 0;
            //             }
            //         }
            //     }
            // })
            ->editColumn('total_bookings', function ($row) {
                $commissionData = $row->commission_earning()
                ->whereHas('getbooking', function ($query) {
                    $query->where('status', 'completed');
                })
                ->whereIn('commission_status', ['unpaid','paid'])
                ->where('user_type', 'handyman');

            $totalBookings = $commissionData->distinct('booking_id')->count();
            $row['total_bookings'] = $totalBookings;
                if($row->total_bookings > 0){
                    return "<b><a href='" . route('booking.index', ['handyman_id' => $row->id]) . "' data-assign-module='".$row->id."'  class='text-primary text-nowrap px-1' data-bs-toggle='tooltip' title='View Handyman Bookings'>".$row->total_bookings."</a> </b>";
                }else{

                    return "<b><span  data-assign-module='".$row->id."'  class='text-primary text-nowrap px-1' data-bs-toggle='tooltip' title='View Handyman Bookings'>0</span>";
                }

            })
            ->editColumn('total_earning', function ($row) {
                $commissionData = $row->commission_earning()
                ->whereHas('getbooking', function ($query) {
                    $query->where('status', 'completed');
                })
                ->whereIn('commission_status', ['unpaid','paid'])
                ->where('user_type', 'handyman');

                $commissions = $commissionData->get();
                $totalServiceAmount = 0;
                foreach($commissions as $commission){
                    $bookingData = Booking::where('id', $commission->booking_id)->first();

                    $totalServiceAmount += $bookingData->final_sub_total;
                }
                $row['totalServiceAmount'] = $totalServiceAmount;

                return $totalServiceAmount ? getPriceFormat($totalServiceAmount) : getPriceFormat(0);
            })
            ->editColumn('admin_earning', function ($row) {
                $commissionData = $row->commission_earning()
                ->whereHas('getbooking', function ($query) {
                    $query->where('status', 'completed');
                })
                ->whereIn('commission_status', ['unpaid','paid'])
                ->where('user_type', 'handyman');

                $commissions = $commissionData->get();
                $totalAdminEarning = 0;
                foreach($commissions as $commission){
                    $commission_data = CommissionEarning::where('booking_id', $commission->booking_id)->where('user_type', 'admin')->whereIn('commission_status', ['unpaid','paid'])->first();
                    if($commission_data !== null){
                        $totalAdminEarning += $commission_data->commission_amount;
                    }

                }

                return $totalAdminEarning ? getPriceFormat($totalAdminEarning) : getPriceFormat(0);
            })
            ->editColumn('handyman_earning', function ($row) {
                return $row->total_pay ? getPriceFormat($row->total_pay) : getPriceFormat(0);
            })

            ->editColumn('handyman_paid_earning', function ($row) {
                $commissionData = HandymanPayout::where('handyman_id',$row->id)
                ->sum('amount');
                $handyman_paid_earning = $commissionData ?? 0;
                // return getPriceFormat( $handyman_paid_earning);
                return "<b><a href='" . route('handymanpayout.show',$row->id) . "' data-assign-module='".$row->id."'  class='text-primary text-nowrap px-1' data-bs-toggle='tooltip' title='" . __('messages.view_handyman_payout') . "'>".getPriceFormat( $handyman_paid_earning)."</a> </b>";

            })
            ->editColumn('provider_earning', function ($row) {
                $commissionData = $row->commission_earning()
                ->whereHas('getbooking', function ($query) {
                    $query->where('status', 'completed');
                })
                ->whereIn('commission_status', ['unpaid','paid'])
                ->where('user_type', 'handyman');

                $commissions = $commissionData->get();
                $provider_earning = 0;
                foreach($commissions as $commission){
                    if($commission != null){
                    $commission_data = CommissionEarning::where('booking_id', $commission->booking_id)->where('user_type', 'provider')->whereIn('commission_status', ['unpaid','paid'])->first();
                    if($commission_data){
                        $provider_earning += $commission_data->commission_amount;
                    }
                    }
                }




                return $provider_earning ? getPriceFormat( $provider_earning) : getPriceFormat(0);
            })
            ->rawColumns(['action','total_earning','total_bookings','provider_earning','handyman_paid_earning'])
            ->make(true);
        }


        if($request->is('api/*')) {

            if (auth()->user()->hasAnyRole(['provider'])) {
               $handymen_list =$query->where('provider_id', auth()->user()->id)->get();
            }

            $handymanearningData = [];

            foreach($handymen_list as $handyman){
                $commissionData = $handyman->commission_earning()
                    ->whereHas('getbooking', function ($query) {
                        $query->where('status', 'completed');
                    })
                    ->whereIn('commission_status', ['unpaid','paid'])
                    ->where('user_type', 'handyman');

                //$providerEarning = $commissionData->sum('commission_amount');
                $totalBookings = $commissionData->distinct('booking_id')->count();
                $commissions = $commissionData->get();

                $totalEarning = 0;
                $adminEarning = 0;
                $totalTax = 0;
                foreach($commissions as $commission){
                    if ($commission != null) {
                        $bookingData = Booking::where('id', $commission->booking_id)->first();
                        $totalTax += $bookingData->final_total_tax;

                        // $totalEarning += $bookingData->total_amount;
                        if($bookingData != null){
                            $totalEarning += $bookingData->final_sub_total;
                        }

                        $commission_data = CommissionEarning::where('booking_id', $commission->booking_id)->where('user_type', 'admin')->whereIn('commission_status', ['unpaid','paid'])->first();
                        $adminEarning += $commission_data->commission_amount;



                    }
                }

                $handymanCommissionData = $handyman->commission_earning()
                    ->whereHas('getbooking', function ($query) {
                        $query->where('status', 'completed');
                    })
                    ->where('commission_status', 'unpaid')
                    ->where('user_type', 'handyman')->get();
                    $handymanDueAmount = 0;
                if($handymanCommissionData->count() > 0){
                    foreach ($handymanCommissionData as $commission) {
                        if ($commission != null) {
                            // Fetch commission data for related bookings including handyman
                            $handyman_commission_data = CommissionEarning::where('booking_id', $commission->booking_id)
                            ->whereIn('user_type', ['handyman'])
                            ->where('commission_status', 'unpaid')
                            ->get();

                                    if ($handyman_commission_data) {
                                        foreach ($handyman_commission_data as $data) {
                                            if (isset($data->commission_amount)) {
                                                $handymanDueAmount += $data->commission_amount;
                                            }
                                        }
                                    }
                         }
                    }
                }

                $providercommissionData = $handyman->commission_earning()
                    ->whereHas('getbooking', function ($query) {
                        $query->where('status', 'completed');
                    })
                    ->whereIn('commission_status', ['paid','unpaid'])
                    ->where('user_type', 'handyman')->get();
                $provider_total_earning = 0;
                if($providercommissionData){
                    foreach($providercommissionData as $commission){
                        if($commission != null){
                        $commission_data = CommissionEarning::where('booking_id', $commission->booking_id)->where('user_type', 'provider')->whereIn('commission_status', ['paid','unpaid'])->first();
                        if($commission_data){
                            $provider_total_earning += $commission_data->commission_amount;
                        }
                        }
                    }
                }

                $handyman_paid_earning = HandymanPayout::where('handyman_id',$handyman->id)->sum('amount');
                $handyman_paid_earning = $handyman_paid_earning ?? 0;
                $totalEarning = $totalEarning ? $totalEarning : 0;

                $adminEarning = $adminEarning ? $adminEarning : 0;

                $handymanearningData[] = [
                    'handyman_id' => $handyman->id,
                    'handyman_name' => $handyman->display_name,
                    'handyman_image' => getSingleMedia(optional($handyman),'profile_image', null),
                    'email' => $handyman->email,
                    'commission' => optional($handyman->providertype)->commission,
                    'commission_type' => optional($handyman->providertype)->type,
                    'total_bookings' => $totalBookings,
                    'total_earning' =>$totalEarning,
                    'admin_earning' => $adminEarning,
                    'handyman_paid_earning' => $handyman_paid_earning,
                    'handyman_paid_earning_formate' => $handyman_paid_earning,
                    'handyman_due_amount' => $handymanDueAmount,
                    'provider_total_amount' => $provider_total_earning,
                ];
            }
            return comman_custom_response($handymanearningData);
		}
    }


    public function show($id)
    {
        //
        $user = User::where('id',$id)->first();
        $auth_user = authSession();
        $assets = ['datatable'];

        if($user->user_type == 'provider'){
            $pageTitle = __('messages.list_form_title',['form' => __('messages.providerpayout_list')] );
            $providerdata = $user;
            return view('providerpayout.view', compact('pageTitle','auth_user','assets','id','providerdata'));
        }
        else if($user->user_type == 'handyman'){
            $pageTitle = __('messages.list_form_title',['form' => __('messages.handymanpayout')] );
            if($user->provider_id == auth()->user()->id){
            $handymandata = $user;
            return view('handymanpayout.view', compact('pageTitle','auth_user','assets','handymandata'));
            }
            return redirect(route('handyman.index'))->withErrors(trans('messages.demo_permission_denied'));
        }
    }
}
