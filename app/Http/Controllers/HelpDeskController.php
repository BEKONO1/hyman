<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HelpDesk;
use Yajra\DataTables\DataTables;
use App\Http\Requests\HelpDeskRequest;
use App\Models\Setting;
use App\Traits\NotificationTrait;
use App\Models\HelpDeskActivityMapping;

class HelpDeskController extends Controller
{
    use NotificationTrait;
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
        $pageTitle = trans('messages.helpdesk');
        $auth_user = authSession();
        $assets = ['datatable'];
        return view('helpdesk.index', compact('pageTitle','auth_user','assets','filter'));
    }


    public function index_data(DataTables $datatable,Request $request)
    {
        $query = HelpDesk::query();
        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        if (auth()->user()->hasAnyRole(['admin'])) {
            $query->newquery()->withTrashed();
        }else{
            $query->where('employee_id',auth()->user()->id);
        }
        
        return $datatable->eloquent($query)
        ->addColumn('check', function ($row) {
            return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" data-type="helpdesk" onclick="dataTableRowCheck('.$row->id.',this)">';
        })
        ->editColumn('id' , function ($query){
            return '#'. $query->id;
        })
        ->editColumn('name' , function ($query){
            return view('helpdesk.user', compact('query'));
        })
        ->filterColumn('name',function($query,$keyword){
            $query->whereHas('users',function ($q) use($keyword){
                $q->where('display_name','like','%'.$keyword.'%');
            });
        })
        ->orderColumn('name', function ($query, $order) {
            $query->select('help_desk.*')
                  ->join('users as employees', 'employees.id', '=', 'help_desk.employee_id')
                  ->orderBy('employees.display_name', $order);   
        })
        ->editColumn('subject', function($query){
            // if (auth()->user()->can('helpdesk edit')) {
            //     $link =  '<a class="btn-link btn-link-hover" href='.route('helpdesk.create', ['id' => $query->id]).'>'.$query->subject.'</a>';
            // } else {
            //     $link = $query->subject;
            // }
            return $query->subject;
        })
        ->editColumn('datetime' , function ($query){
            $sitesetup = Setting::where('type','site-setup')->where('key', 'site-setup')->first();
            $datetime = json_decode($sitesetup->value);
            $date = date("$datetime->date_format $datetime->time_format", strtotime($query->updated_at->setTimezone(new \DateTimeZone($datetime->time_zone ?? 'UTC'))));
            return $date;
        })
        ->editColumn('mode' , function ($query){
            return ucfirst($query->mode) ?? '-';
        })
        ->editColumn('role' , function ($query){
            return ucfirst(optional($query->users)->user_type) ?? '-';
        })
        ->editColumn('status' , function ($query){
            $status = $query->status;
            if($status == 0){
                $status = '<span class="badge text-success bg-success-subtle">'.'open'.'</span>';
            }else{
                $status = '<span class="badge text-danger bg-danger-subtle">'.'closed'.'</span>';
            }
            return $status;
        })
        ->addColumn('action', function($row){
            return view('helpdesk.action',compact('row'))->render();
        })
        ->addIndexColumn()
        ->rawColumns(['check','subject','action','status'])
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
                $HelpDesk = HelpDesk::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = 'Bulk Help Desk Status Updated';
                break;

            case 'delete':
                HelpDesk::whereIn('id', $ids)->delete();
                $message = 'Bulk Help Desk Deleted';
                break;
            
            case 'restore':
                HelpDesk::whereIn('id', $ids)->restore();
                $message = 'Bulk Help Desk Restored';
                break;

            case 'permanently-delete':
                HelpDesk::whereIn('id', $ids)->forceDelete();
                $message = 'Bulk Help Desk Permanently Deleted';
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
        if (!auth()->user()->can('helpdesk add')) {
            return redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }        
        $pageTitle1 = trans('messages.setting');
        $page = 'taxes';
        $id = $request->id;
        $auth_user = authSession();

        $helpdesk = HelpDesk::find($id);
        $pageTitle = trans('messages.update_form_title',['form'=>'']);
        
        if($helpdesk == null){
            $pageTitle = trans('messages.add_button_form',['form' => '']);
            $helpdesk = new HelpDesk;
        }
        
        return view('helpdesk.create', compact('pageTitle' ,'helpdesk' ,'auth_user','pageTitle1','page' ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HelpDeskRequest $request)
    {
        if(demoUserPermission()){
            return  redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }

        $helpdesk = $request->all();

        $helpdesk['employee_id'] = !empty($request->employee_id) ? $request->employee_id : auth()->user()->id; 
        // $helpdesk['status'] = 'open';
        $result = HelpDesk::updateOrCreate(['id' => $request->id], $helpdesk);  
       
        $activity_data = [
            'activity_type' => 'add_helpdesk',
            'helpdesk_id' => $result->id,
            'sender_id' => $result->employee_id,
            'receiver_id' => admin_id(),
            'helpdesk' => $result,
        ];
        $this->sendNotification($activity_data);

        storeAttachments($request, 'helpdesk_attachment', $result);
        storeAttachments($request, 'helpdesk_activity_attachment', $activity_data);
    
        $message = __('messages.update_form',[ 'form' => __('messages.helpdesk') ] );
		if($result->wasRecentlyCreated){
			$message = __('messages.save_form',[ 'form' => __('messages.helpdesk') ] );
		}
        if($request->is('api/*')) {
            $response = [
                'message'=>$message
            ];
            return comman_custom_response($response);
		}
		return redirect(route('helpdesk.index'))->withSuccess($message);
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
        $helpdeskdata = HelpDesk::with('helpdeskactivity')->where('id',$id)->first();
        if(empty($helpdeskdata))
        {
            $msg = __('messages.not_found_entry',['name' => __('messages.helpdesk')] );
            return redirect(route('helpdesk.index'))->withError($msg);
        }
        if ($helpdeskdata->employee_id != auth()->user()->id && !auth()->user()->hasRole(['admin', 'demo_admin'])) {
            return redirect(route('home'))->withErrors(trans('messages.demo_permission_denied'));
        }
       
        $sitesetup = Setting::where('type','site-setup')->where('key', 'site-setup')->first();
        $datetime = $sitesetup ? json_decode($sitesetup->value) : null;
        $pageTitle = trans('messages.query' ) .' '. trans('messages.detail' );
        $assets = ['datatable'];
        
        return view('helpdesk.view', compact('pageTitle','auth_user','assets','helpdeskdata','datetime'));

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
            if(request()->is('api/*')){
                return comman_message_response( __('messages.demo_permission_denied') );
            }
            return  redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $helpdesk = HelpDesk::find($id);
        $msg= __('messages.msg_fail_to_delete',['item' => __('messages.helpdesk')] );

        if($helpdesk!='') {
            $helpdesk->delete();
            $msg= __('messages.msg_deleted',['name' => __('messages.helpdesk')] );
        }
        if(request()->is('api/*')){
            return comman_custom_response(['message'=> $msg , 'status' => true]);
        }
        return comman_custom_response(['message'=> $msg, 'status' => true]);
    }
    public function action(Request $request){
        $id = $request->id;
        $helpdesk = HelpDesk::withTrashed()->where('id',$id)->first();
        $msg = __('messages.not_found_entry',['name' => __('messages.helpdesk')] );
        if($request->type === 'restore'){
            $helpdesk->restore();
            $msg = __('messages.msg_restored',['name' => __('messages.helpdesk')] );
        }

        if($request->type === 'forcedelete'){
            $helpdesk->forceDelete();
            $msg = __('messages.msg_forcedelete',['name' => __('messages.helpdesk')] );
        }

        return comman_custom_response(['message'=> $msg , 'status' => true]);
    }
    public function closed(Request $request, $id){
        
        $helpdesk = HelpDesk::where('id', $id)->first();

        if ($helpdesk && $helpdesk->status == 0) {
            $helpdesk->update(['status' => 1]);
            if(auth()->user()->hasRole(['admin', 'demo_Admin'])) {
                $receiver_id = $helpdesk->employee_id;
            } else {
                $receiver_id = admin_id();
            }
            $activity_data = [
                'activity_type' => 'closed_helpdesk',
                'helpdesk_id' => $helpdesk->id,
                'sender_id' => auth()->user()->id,
                'receiver_id' => $receiver_id,
                'helpdesk' => $helpdesk
            ];
            $this->sendNotification($activity_data);
            $message = __('messages.closed_successfully', [ 'id' => $helpdesk->id ]);
            if(request()->is('api/*')){
                return comman_custom_response(['message'=> $message , 'status' => true]);
            }
            return redirect()->route('helpdesk.index')->withSuccess($message);
        }elseif ($helpdesk && $helpdesk->status == 1){
            $message = __('messages.already_closed_successfully', [ 'id' => $helpdesk->id ]);
            if(request()->is('api/*')){
                return comman_custom_response(['message'=> $message , 'status' => true]);
            }
            return redirect()->route('helpdesk.index')->withSuccess($message);
        }
        if(request()->is('api/*')){
            $message = __('messages.record_not_found');
            return comman_custom_response(['message'=> $message , 'status' => true]);
        }
        return redirect()->route('helpdesk.index')->withError(__('messages.record_not_found'));

    }
    public function activity(Request $request,$id){

        $helpdesk = HelpDesk::where('id', $id)->first();

        if ($helpdesk && $helpdesk->status == 0) {
            $helpdeskactivity['helpdesk_id'] = $helpdesk->id;
            $helpdeskactivity['sender_id'] = auth()->user()->id;
            if(auth()->user()->hasRole(['admin', 'demo_Admin'])) {
                $helpdeskactivity['receiver_id'] = $helpdesk->employee_id;
            } else {
                $helpdeskactivity['receiver_id'] = admin_id();
            }
            $helpdeskactivity['messages'] = $request->description ?? null;
            $activity = \App\Models\HelpDeskActivityMapping::updateOrCreate(
                [
                    'helpdesk_id' => $helpdeskactivity['helpdesk_id'],
                    'sender_id' => $helpdeskactivity['sender_id'],
                    'receiver_id' => $helpdeskactivity['receiver_id'],
                    'messages' => $helpdeskactivity['messages'],
                ],
                $helpdeskactivity 
            );
            $activity_data = [
                'activity_type' => 'reply_helpdesk',
                'helpdesk_id' => $helpdeskactivity['helpdesk_id'],
                'sender_id' => $helpdeskactivity['sender_id'],
                'receiver_id' => $helpdeskactivity['receiver_id'],
                'messages' => $helpdeskactivity['messages'],
                'helpdesk' => $helpdesk
            ];
            $this->sendNotification($activity_data);
            storeAttachments($request, 'helpdesk_activity_attachment', $activity);
            
            $message = __('messages.message_successfully_send' );
            if(request()->is('api/*')){
                return comman_custom_response(['message'=> $message , 'status' => true]);
            }
            return redirect()->route('helpdesk.show', $helpdesk->id)->withSuccess($message);
        }
        if(request()->is('api/*')){
            $message = __('messages.record_not_found');
            return comman_custom_response(['message'=> $message , 'status' => true]);
        }
        return redirect()->route('helpdesk.index')->withError(__('messages.record_not_found'));

    }
    
    
}
