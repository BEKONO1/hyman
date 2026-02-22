<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use Yajra\DataTables\DataTables;
use Hash;
use App\Models\Setting;
use App\Models\Booking;
use App\Models\BookingHandymanMapping;
use App\Models\HandymanPayout;
use App\Models\Wallet;

class HandymanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];
        $pageTitle = __('messages.list_form_title', ['form' => __('messages.handyman')]);
        if ($request->status == 'pending') {
            $pageTitle = __('messages.pending_list_form_title', ['form' => __('messages.handyman')]);
        }
        if ($request->status == 'unassigned') {
            $pageTitle = __('messages.unassigned_list_form_title', ['form' => __('messages.handyman')]);
        }
        if ($request->status == 'request') {
            $pageTitle = __('messages.pending_list_form_title', ['form' => __('messages.handyman')]);
        }
        $auth_user = authSession();
        $assets = ['datatable'];
        $list_status = $request->status;
        return view('handyman.index', compact('list_status', 'pageTitle', 'auth_user', 'assets', 'filter'));
    }

    public function index_data(DataTables $datatable, Request $request)
    {
        $query = User::where('user_type', 'handyman');

        $filter = $request->filter;

        // Apply filters
        if (!empty($filter['column_status'])) {
            $query->where('status', $filter['column_status']);
        }

        // Include trashed users based on roles
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('provider')) {
            $query->withTrashed();
        }

        // Apply provider-specific filters
        if (auth()->user()->hasRole('provider')) {
            $query->where('provider_id', auth()->user()->id);
        }

        // Apply list status filters
        switch ($request->list_status) {
            case 'pending':
                $query->where('status', 0);
                break;
            case 'unassigned':
                $query->where('status', 1)->whereNull('provider_id');
                break;
            case 'request':
                $query->where('status', 0)->where(function ($query) {
                    $query->whereNull('provider_id')->orWhereNotNull('provider_id');
                });
                break;
            default:
                $query->where('status', 1)->whereNotNull('provider_id');
        }

        return $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row" id="datatable-row-' . $row->id . '" name="datatable_ids[]" value="' . $row->id . '" data-type="user" onclick="dataTableRowCheck(' . $row->id . ',this)">';
            })
            ->editColumn('display_name', fn($query) => view('handyman.user', compact('query')))
            ->editColumn('address', fn($query) => $query->address ?? '-')
            ->editColumn('created_at', function ($query) {
                $sitesetup = Setting::where('type', 'site-setup')->where('key', 'site-setup')->first();
                $datetime = $sitesetup ? json_decode($sitesetup->value) : null;

                return $datetime && $datetime->date_format && $datetime->time_format
                    ? date($datetime->date_format, strtotime($query->created_at)) .'  '. date($datetime->time_format, strtotime($query->created_at))
                    : $query->created_at;
            })
            ->editColumn('status', function ($query) {
                return $query->status == 0
                    ? '<a class="btn-sm text-white btn btn-success" href=' . route('handyman.approve', $query->id) . '>Accept</a>'
                    : '<span class="badge badge-active badge badge-active text-success bg-success-subtle">' . __('messages.active') . '</span>';
            })
            ->editColumn('provider_id', fn($query) => view('handyman.provider', compact('query')))
            ->filterColumn('provider_id', function ($qry, $keyword) {
                $qry->whereHas('providers', function ($q) use ($keyword) {
                    $q->where('display_name', 'like', '%' . $keyword . '%');
                });
            })
            ->orderColumn('provider_id', function ($query, $order) {
                $query->selectRaw('users.*, (SELECT display_name FROM users AS providers WHERE providers.id = users.provider_id) AS provider_display_name')
                      ->orderBy('provider_display_name', $order);
            })
            ->editColumn('wallet', function ($query){
                return view('handyman.wallet', compact('query'));
            })
            ->addColumn('action', fn($handyman) => view('handyman.action', compact('handyman'))->render())
            ->addIndexColumn()
            ->rawColumns(['check', 'display_name', 'action', 'status','wallet'])
            ->toJson();
    }


    /* bulck action method */
    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = 'Bulk Action Updated';

        switch ($actionType) {
            case 'change-status':
                $branches = User::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = 'Bulk Handyman Status Updated';
                break;

            case 'delete':
                if (!auth()->user()->can('handyman delete') && !auth()->user()->hasRole('demo_admin')) {
                    return response()->json(['status' => false, 'message' => trans('messages.permission_denied')]);
                }
                User::whereIn('id', $ids)->delete();
                $message = 'Bulk Handyman Deleted';
                break;

            case 'restore':
                if (!auth()->user()->can('handyman delete') && !auth()->user()->hasRole('demo_admin')) {
                    return response()->json(['status' => false, 'message' => trans('messages.permission_denied')]);
                }
                User::whereIn('id', $ids)->restore();
                $message = 'Bulk Handyman Restored';
                break;

            case 'permanently-delete':
                if (!auth()->user()->can('handyman delete') && !auth()->user()->hasRole('demo_admin')) {
                    return response()->json(['status' => false, 'message' => trans('messages.permission_denied')]);
                }
                User::whereIn('id', $ids)->forceDelete();
                $message = 'Bulk Handyman Permanently Deleted';
                break;

            case 'restore':
                User::whereIn('id', $ids)->restore();
                $message = 'Bulk Provider Restored';
                break;

            case 'permanently-delete':
                User::whereIn('id', $ids)->forceDelete();
                $message = 'Bulk Provider Permanently Deleted';
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
    public function create(Request $request)
    {
        if (!auth()->user()->can('handyman add')) {
            return redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $id = $request->id;
        $auth_user = authSession();

        $handymandata = User::find($id);
        $pageTitle = __('messages.update_form_title', ['form' => __('messages.handyman')]);

        if ($handymandata == null) {
            $pageTitle = __('messages.add_button_form', ['form' => __('messages.handyman')]);
            $handymandata = new User;
        }else{
            if ($handymandata->provider_id !== auth()->user()->id && !auth()->user()->hasRole(['admin', 'demo_admin'])) {
                return redirect(route('handyman.index'))->withErrors(trans('messages.demo_permission_denied'));
            }
        }

        return view('handyman.create', compact('pageTitle', 'handymandata', 'auth_user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        if (demoUserPermission()) {
            return  redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $data = $request->all();
        if (auth()->user()->hasAnyRole(['provider'])) {
            $auth_user = authSession();
            $user_id = $auth_user->id;
            $data['provider_id'] = $user_id;
        }
        if ($request->id == null && default_earning_type() === 'subscription') {
            $exceed =  get_provider_plan_limit($data['provider_id'], 'handyman');
            if (!empty($exceed)) {
                if ($exceed == 1) {
                    $message = __('messages.limit_exceed', ['name' => __('messages.handyman')]);
                } else {
                    $message = __('messages.not_in_plan', ['name' => __('messages.handyman')]);
                }
                if ($request->is('api/*')) {
                    return comman_message_response($message);
                } else {
                    return  redirect()->back()->withErrors($message);
                }
            }
        }
        $id = $data['id'];

        $data['user_type'] = $data['user_type'] ?? 'handyman';
        $data['is_featured'] = 0;

        if ($request->has('is_featured')) {
            $data['is_featured'] = 1;
        }

        $data['display_name'] = $data['first_name'] . " " . $data['last_name'];
        // Save User data...
        if ($id == null) {
            $data['password'] = bcrypt($data['password']);
            $user = User::create($data);
            $wallet = array(
                'title' => $user->display_name,
                'user_id' => $user->id,
                'amount' => 0
            );
            $result = Wallet::create($wallet);
        } else {
            $user = User::findOrFail($id);
            // User data...
            // $user->removeRole($user->user_type);
            $user->fill($data)->update();
        }
        if ($data['status'] == 1 && auth()->user()->hasAnyRole(['admin'])) {
            try {
            \Mail::send(
                'verification.verification_email',
                array(),
                function ($message) use ($user) {
                    $message->from(env('MAIL_FROM_ADDRESS'));
                    $message->to($user->email);
                }
            );
        } catch (\Throwable $th) {

        }

        }
        $user->assignRole($data['user_type']);
        storeMediaFile($user, $request->profile_image, 'profile_image');
        $message = __('messages.update_form', ['form' => __('messages.handyman')]);
        if ($user->wasRecentlyCreated) {
            $message = __('messages.save_form', ['form' => __('messages.handyman')]);
        }

        if ($request->is('api/*')) {
            return comman_message_response($message);
        }

        return redirect(route('handyman.index'))->withSuccess($message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $auth_user = authSession();
        if ($id != auth()->user()->id && !auth()->user()->hasRole(['admin', 'demo_admin'])) {
            return redirect(route('home'))->withErrors(trans('messages.demo_permission_denied'));
        }
        $providerdata = User::with('providerHandyman')->where('user_type', 'provider')->where('id', $id)->first();
        if (empty($providerdata)) {
            $msg = __('messages.not_found_entry', ['name' => __('messages.provider')]);
            return redirect(route('provider.index'))->withError($msg);
        }
        $pageTitle = __('messages.view_form_title', ['form' => __('messages.provider')]);
        return view('handyman.view', compact('pageTitle', 'providerdata', 'auth_user'));
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
        if (demoUserPermission()) {
            if (request()->is('api/*')) {
                return comman_message_response(__('messages.demo_permission_denied'));
            }
            return  redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $handyman = User::find($id);
        $msg = __('messages.msg_fail_to_delete', ['item' => __('messages.handyman')]);

        if ($handyman != '') {
            $handyman->delete();
            $msg = __('messages.msg_deleted', ['name' => __('messages.handyman')]);
        }
        if (request()->is('api/*')) {
            return comman_message_response($msg);
        }
        return comman_custom_response(['message' => $msg, 'status' => true]);
    }
    public function action(Request $request)
    {
        $id = $request->id;

        $user  = User::withTrashed()->where('id', $id)->first();
        $msg = __('messages.not_found_entry', ['name' => __('messages.handyman')]);
        if ($request->type == 'restore') {
            $user->restore();
            $msg = __('messages.msg_restored', ['name' => __('messages.handyman')]);
        }
        if ($request->type === 'forcedelete') {
            $user->forceDelete();
            $msg = __('messages.msg_forcedelete', ['name' => __('messages.handyman')]);
        }
        if (request()->is('api/*')) {
            return comman_message_response($msg);
        }
        return comman_custom_response(['message' => $msg, 'status' => true]);
    }

    public function approve($id)
    {
        $provider = User::find($id);
        $provider->status = 1;
        $provider->save();
        $msg = __('messages.approve_successfully');
        return redirect()->back()->withSuccess($msg);
    }

    public function updateProvider(Request $request)
    {
        $id = $request->id;
        $handyman = User::with('handyman')->findOrFail($id);
        $provider_id = $request->provider_id;

        $handyman->update(['provider_id' => $provider_id]);

        return response()->json(['message' => 'Provider Assign Successfully', 'status' => true]);
    }



    public function getChangePassword(Request $request)
    {
        $id = $request->id;
        $auth_user = authSession();

        $handymandata = User::find($id);
        $pageTitle = __('messages.change_password', ['form' => __('messages.change_password')]);
        if ($handymandata == null) {
            $pageTitle = __('messages.add_button_form', ['form' => __('messages.handyman')]);
            $handymandata = new User;
        }
        return view('handyman.changepassword', compact('pageTitle', 'handymandata', 'auth_user'));
    }

    public function changePassword(Request $request)
    {
        if (demoUserPermission()) {
            return  redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $user = User::where('id', $request->id)->first();
        if ($user == "") {
            $message = __('messages.user_not_found');
            return comman_message_response($message, 400);
        }

        $validator = \Validator::make($request->all(), [
            'old' => 'required|min:8|max:255',
            'password' => 'required|min:8|confirmed|max:255',
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('password')) {
                $message = __('messages.confirmed', ['name' => __('messages.password')]);
                return redirect()->route('handyman.changepassword', ['id' => $user->id])->with('error', $message);
            }
            return redirect()->route('handyman.changepassword', ['id' => $user->id])->with('errors', $validator->errors());
        }

        $hashedPassword = $user->password;

        $match = Hash::check($request->old, $hashedPassword);

        $same_exits = Hash::check($request->password, $hashedPassword);
        if ($match) {
            if ($same_exits) {
                $message = __('messages.old_new_pass_same');
                return redirect()->route('handyman.changepassword', ['id' => $user->id])->with('error', $message);
            }

            $user->fill([
                'password' => Hash::make($request->password)
            ])->save();
            $message = __('messages.password_change');
            return redirect()->route('handyman.index')->withSuccess($message);
        } else {
            $message = __('messages.valid_password');
            return redirect()->route('handyman.changepassword', ['id' => $user->id])->with('error', $message);
        }
    }
    public function handyman_detail($id)
    {
        $auth_user = authSession();
        $handymandata = User::with(['providerHandyman', 'commission_earning', 'wallet'])
                        ->where('user_type', 'handyman')
                        ->find($id);               
        // Fetch handyman data along with related data in a single query
        
        
        if (is_null($handymandata)) {
            $msg = __('messages.not_found_entry', ['name' => __('messages.handyman')]);
            return redirect(route('handyman.index'))->withError($msg);
        }

        if (
            ((auth()->user()->hasRole('handyman') && $handymandata->id !== auth()->user()->id) ||
            (auth()->user()->hasRole('provider')  && $handymandata->provider_id !== auth()->user()->id ) ) &&
            (!auth()->user()->hasRole(['admin', 'demo_admin']))
        ) {
            return redirect(route('handyman.index'))->withErrors(trans('messages.demo_permission_denied'));
        }
        // Count handyman's booking statuses in a single query
        $data = Booking::whereHas('handymanAdded', function ($query) use ($id) {
            $query->where('handyman_id', $id);
        })->selectRaw(
            'COUNT(CASE WHEN status = "pending" THEN "pending" END) AS PendingStatusCount,
             COUNT(CASE WHEN status = "in_progress"  THEN "InProgress" END) AS InProgressstatuscount,
             COUNT(CASE WHEN status = "completed"  THEN "Completed" END) AS Completedstatuscount,
             COUNT(CASE WHEN status = "accept"  THEN "Accepted" END) AS Acceptedstatuscount,
             COUNT(CASE WHEN status = "on_going"  THEN "Ongoing" END) AS Ongoingstatuscount'
        )->first()->toArray();

        $totalbooking = Booking::whereHas('handymanAdded', function ($query) use ($id) {
            $query->where('handyman_id', $id);
        })->count();
        // Get total handyman payout and unpaid commission earnings
        $totalWithdrawn = HandymanPayout::where('handyman_id', $id)->sum('amount') ?? 0;
        $pendingCommission = $handymandata->commission_earning()
            ->whereHas('getbooking', function ($query) {
                $query->where('status', 'completed');
            })
            ->where('commission_status', 'unpaid')
            ->sum('commission_amount');
        $earning =    $pendingCommission ? $pendingCommission : 0;
        // Calculate total earnings
        $walletAmount = optional($handymandata->wallet)->amount ?? 0;
        $totalEarnings = $totalWithdrawn + $earning;
        
        $handymanData = [
            'wallet' => $walletAmount,
            'handymanAlreadyWithdrawAmt' => $totalWithdrawn,
            'pendWithdrwan' => $earning,
            'totalEarning' => $totalEarnings,
            'totalbooking' => $totalbooking,
        ];
        

        $pageTitle = __('messages.view_form_title', ['form' => __('messages.provider')]);
        return view('handyman.detail', compact('pageTitle', 'handymandata', 'handymanData', 'auth_user', 'data'));
    }
}
