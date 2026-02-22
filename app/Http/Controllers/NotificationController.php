<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Yajra\DataTables\DataTables;

class NotificationController extends Controller
{
    public function index()
    {

        $pageTitle = __('messages.list_form_title',['form' => __('messages.notification')] );
        $assets = ['datatable'];

        return view('notification.index', compact('pageTitle','assets'));
    }
    public function index_data(DataTables $datatable)
    {
       $notifications = \Auth::user()->notifications()->latest()->get();
    
        // Step 2: Assign row numbers based on ascending age (oldest = 1)
        $total = $notifications->count();
        $notifications = $notifications->values()->map(function ($item, $index) use ($total) {
            $item->custom_row_index = $total - $index; // So latest is last, oldest is 1
            return $item;
        });
    
        return $datatable->collection($notifications)
            ->editColumn('DT_RowIndex', function ($row) {
                return $row->custom_row_index;
            })
        ->editColumn('type', function ($row) {
            $data = $row->data ?? [];

            $typeText = isset($data['type']) ? ucfirst(str_replace('_', ' ', $data['type'])) : 'N/A';
            $href = '#';

            if (!empty($data['check_booking_type']) && !empty($data['id'])) {
                $href = url('booking/' . $data['id']);
            } elseif (!empty($data['helpdesk_id'])) {
                $href = url('helpdesk/' . $data['helpdesk_id']);
            } elseif (!empty($data['notification-type'])) {
                $ntype = $data['notification-type'];
                if (in_array($ntype, ['wallet_refund_promotional_banner', 'wallet_refund', 'wallet_top_up', 'wallet_payout_transfer', 'cancellation_charges', 'paid_with_wallet']) && !empty($data['user_id'])) {
                    $href = url('wallet/' . $data['user_id']);
                } elseif (in_array($ntype, ['promotional_banner', 'promotional_banner_accepted', 'promotional_banner_rejected']) && !empty($data['id'])) {
                    $href = url('promotional-banner/' . $data['id']);
                } elseif (in_array($ntype, ['service_request', 'service_request_approved', 'service_request_reject']) && !empty($data['id'])) {
                    $href = url('service/create?id=' . $data['id']); 
                }
            }

            return '<a class="btn-link btn-link-hover notify-table-link" href="' . $href . '">' . $typeText . '</a>';
        })
        ->editColumn('message', function ($row) {
            return strip_tags($row->data['message']);
        })
        ->editColumn('created_at', function ($row) {
            return dateAgoFormate($row->created_at,true);
        })

        ->setRowClass(function ($user) {
            return $user->read_at == null ? 'iq-bg-primary' : '';
        })

        ->editColumn('updated_at', function ($row) {
            return dateAgoFormate($row->updated_at,true);
        })
        ->editColumn('action', function ($row) {
            if(isset($row->data['check_booking_type'])){
                return '<a href="'.route('booking.show',$row->data['id']) .'"><span class="iq-bg-info mr-2"><i class="far fa-eye text-secondary"></i></span></a>';
            }
            elseif (isset($row->data['helpdesk_id']) && $row->data['helpdesk_id']) {
                return '<a href="' . route('helpdesk.show', $row->data['helpdesk_id']) . '"><span class="iq-bg-info mr-2"><i class="far fa-eye text-secondary"></i></span></a>';
            } 
            else{
                return '<a href="#"><span class="iq-bg-info mr-2"><i class="far fa-eye text-secondary"></i></span></a>';
            }
        })
        ->rawColumns(['type','action','thread'])
        ->toJson();
    }
    public function notificationList(Request $request){
        $user = auth()->user();
        $user->last_notification_seen = now();
        $user->save();

        $type = isset($request->type) ? $request->type : null;
        if($type == "markas_read"){

            if(count($user->unreadNotifications) > 0 ) {
                $user->unreadNotifications->markAsRead();
            }
            $notifications = $user->notifications->take(5);
        } elseif($type == null) {
            $notifications = $user->notifications->take(5);
        } else {
            $notifications = $user->notifications->where('data.type',$type)->take(5);
        }
        $all_unread_count=isset($user->unreadNotifications) ? $user->unreadNotifications->count() : 0;

        $new_booking_count =  isset($user->unreadNotifications) ? $user->unreadNotifications->where('data.type','booking_added')->count() : 0;

        return response()->json([
            'status'     => true,
            'type'       => $type,
            'data'       => view('notification.list', compact('notifications','new_booking_count','all_unread_count','user'))->render()
        ]);
    }

    public function notificationCounts(Request $request)
    {

        $user = auth()->user();

        $unread_count = 0;
        $unread_total_count = 0;

        if(isset($user->unreadNotifications)){
            $unread_count =$user->unreadNotifications->where('created_at', '>', $user->last_notification_seen)->count() ;
            $unread_total_count = $user->unreadNotifications->count();
        }
        return response()->json([
            'status'     => true,
            'counts' => $unread_count,
            'unread_total_count' => $unread_total_count
        ]);
    }
}
