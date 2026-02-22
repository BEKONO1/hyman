<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Constant;
use App\Models\NotificationTemplate;
use Yajra\DataTables\DataTables;
use App\Models\NotificationTemplateContentMapping;
use App\Models\MailTemplates;
use App\Models\MailTemplateContentMapping;

class NotificationTemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {

        $filter = [
            'status' => $request->status,
        ];
        $pageTitle = __('messages.notification_templates') ;
        $auth_user = authSession();
        $assets = ['datatable'];
        return view('notificationtemplates.index', compact('pageTitle','auth_user','assets','filter'));
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                $branches = NotificationTemplate::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = 'Bulk NotificationTemplate Status Updated';
                break;

            case 'delete':
                NotificationTemplate::whereIn('id', $ids)->delete();
                $message = 'Bulk NotificationTemplate Deleted';
                break;

            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function index_list(Request $request)
    {
        $query_data = NotificationTemplate::with('defaultNotificationTemplateMap', 'constant')->get();

        $data = [];

        $notificationKeyChannels = array_keys(config('notificationtemplate.channels'));

        $arr = [];
        // For Channel Map Or Update Channel Value
        foreach ($notificationKeyChannels as $key => $value) {
            $arr[$value] = 0;
        }

        foreach ($query_data as $key => $value) {
            $data[$key] = [
                'id' => $value->id,
                'type' => $value->type,
                'template' => $value->defaultNotificationTemplateMap->subject,
                'is_default' => false,
            ];

            if (isset($value->channels)) {
                $data[$key]['channels'] = $value->channels;
            } else {
                $data[$key]['channels'] = $arr;
            }
        }

        $notificationChannels = config('notificationtemplate.channels');

        return response()->json(['data' => $data, 'channels' => $notificationChannels, 'status' => true, 'message' => __('messages.notification_temp_list')]);
    }

    public function update_status(Request $request, NotificationTemplate $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('branch.status_update')]);
    }

    public function index_data(Datatables $datatable, Request $request)
    {

        $query = NotificationTemplate::query()->with('defaultNotificationTemplateMap');
        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" data-type="notificationtemplate" onclick="dataTableRowCheck(' . $row->id . ',this)">';
            })
            ->addColumn('action', function ($data) {
               return view('notificationtemplates.action', compact('data'));
            })
            ->editColumn('label', function ($row) {
                return '<a class="btn-link btn-link-hover" href="' . route('notification-templates.edit', $row->id) . '">' . optional($row->defaultNotificationTemplateMap)->subject . '</a>';
            })
            ->orderColumn('label', function ($query, $order) {
                $query->select('notification_templates.*')
                      ->join('notification_template_content_mapping', 'notification_template_content_mapping.template_id', '=', 'notification_templates.id')
                      ->groupBy('notification_templates.id')
                      ->orderBy('notification_template_content_mapping.subject', $order);
            })
            ->editColumn('status', function ($query) {
                $disabled = $query->trashed() ? 'disabled' : '';
                return '<div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <div class="custom-switch-inner">
                        <input type="checkbox" class="custom-control-input  change_status" data-type="notificationtemplate_status" ' . ($query->status ? "checked" : "") . '  ' . $disabled . ' value="' . $query->id . '" id="' . $query->id . '" data-id="' . $query->id . '">
                        <label class="custom-control-label" for="' . $query->id . '" data-on-label="" data-off-label=""></label>
                    </div>
                </div>';
            })
            ->editColumn('updated_at', function ($data) {

                $diff = Carbon::now()->diffInHours($data->updated_at);

                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })
            ->orderColumns(['id'], '-:column $1');


        return $datatable->rawColumns(array_merge(['label', 'action', 'status', 'check']))
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $module_action = 'Create';

        $assets = ['textarea'];

        return view('notificationtemplates.create', compact('module_action', 'assets'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {

        $map = $request->defaultNotificationTemplateMap;
        $request->merge(['type' => $request->type]);
        $page = 'notification-templates';
        $map['subject'] = $request->defaultNotificationTemplateMap['subject'];
        $map['notification_message'] = $request->defaultNotificationTemplateMap['notification_message'];
        $map['notification_link'] = $request->defaultNotificationTemplateMap['notification_link'];


        $request['to'] = isset($request->to) ? json_encode($request->to) : null;
        $request['bcc'] = isset($request->bcc) ? json_encode($request->bcc) : null;
        $request['cc'] = isset($request->cc) ? json_encode($request->cc) : null;
        $data = NotificationTemplate::create($request->all());
        $data->defaultNotificationTemplateMap()->create($map);

        $message = (__('messages.msg_added', ['name' => __('messages.mailable')]));

        return redirect()->route('notification-templates.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $module_name = $this->module_name;

        $module_name_singular = Str::singular($module_name);

        $module_action = 'Show';

        $data = NotificationTemplate::findOrFail($id);

        return view('notificationtemplates.show', compact('module_name_singular', 'module_action', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {

        $pageTitle = trans('messages.update_notification_template');
        $pageTitle1 = trans('messages.notification_templates');
        $page = 'notification-templates';
        $module_action = 'Edit';
        $data = NotificationTemplate::where('id',$id)->with('defaultNotificationTemplateMap', 'constant')->first();

        $notification_template_data = NotificationTemplate::where('type', $data['type'])->with('defaultNotificationTemplateMap', 'constant')->first();

        $buttonTypes = Constant::where('type', 'notification_param_button')
            ->where(function ($query) use ($data) {
                $query->where('sub_type', $data->type)->orWhere('sub_type', null);
            })->get();

        $assets = ['textarea'];

        return view('notificationtemplates.form', compact('module_action', 'data', 'assets', 'buttonTypes', 'pageTitle', 'pageTitle1', 'page','notification_template_data'));
        // return view('setting.edit', compact('module_action', 'data', 'assets', 'buttonTypes', 'pageTitle', 'pageTitle1', 'page'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */

    public function update(Request $request, $id)
    {
        $page = 'templates';
        $type = $request->type;

        // Handle Notification Template
        if ($request->has('defaultNotificationTemplateMap')) {
            $userType = $request->defaultNotificationTemplateMap['user_type'];
            $ids = NotificationTemplate::where('type', $type)->pluck('id');

            $check = NotificationTemplateContentMapping::with('template')
                ->whereIn('template_id', $ids)
                ->where('user_type', $userType)
                ->first();

            if ($check !== null) {
                $data = NotificationTemplateContentMapping::find($check->id);
                if ($data !== null) {
                    $map = $request->defaultNotificationTemplateMap;
                    $data->update([
                        'subject' => $map['subject'],
                        'template_detail' => $map['template_detail'],
                        'mail_subject' => $map['mail_subject'],
                        'mail_template_detail' => $map['mail_template_detail'],
                        'sms_subject' => $map['sms_subject'],
                        'sms_template_detail' => $map['sms_template_detail'],
                        'whatsapp_subject' => $map['whatsapp_subject'],
                        'whatsapp_template_detail' => $map['whatsapp_template_detail'],

                    ]);
                    $data->template->update([
                        'to' => isset($request->to) ? json_encode($request->to) : null,
                        'status' => $request->has('status') ? 1 : 0,
                    ]);

                    $notificationMessage = __('messages.notification_template_updated');
                } else {
                    $notificationMessage = __('messages.notification_template_not_found');
                }
            } else {
                $data = NotificationTemplate::updateOrCreate(['type' => $type], [
                    'name' => $type,
                    'description' => $request->description,
                    'to' => isset($request->to) ? json_encode($request->to) : null,
                    'status' => $request->has('status') ? 1 : 0,
                ]);

                $data->defaultNotificationTemplateMap()->create($request->defaultNotificationTemplateMap);

                $notificationMessage = __('messages.notification_template_created');
            }
        }

        // if ($request->has('defaultMailTemplateMap')) {
        //     $userType = $request->defaultNotificationTemplateMap['user_type'];
        //     $ids = MailTemplates::where('type', $type)->pluck('id');

        //     $check = MailTemplateContentMapping::with('template')
        //         ->whereIn('template_id', $ids)
        //         ->where('user_type', $userType)
        //         ->first();

        //     if ($check !== null) {
        //         $data = MailTemplateContentMapping::find($check->id);

        //         if ($data !== null) {
        //             $map = $request->defaultMailTemplateMap;

        //             $data->update([
        //                 'subject' => $map['subject'],
        //                 'template_detail' => $map['template_detail'],
        //             ]);

        //             $data->template->update([
        //                 'to' => isset($request->to) ? json_encode($request->to) : null,
        //                 'status' => $request->has('status') ? 1 : 0,
        //             ]);
        //         }
        //      } else {
        //         $data = MailTemplates::updateOrCreate(['type' => $type], [
        //             'name' => $type,
        //             'description' => $request->description,
        //             'to' => isset($request->to) ? json_encode($request->to) : null,
        //             'status' => $request->has('status') ? 1 : 0,
        //         ]);

        //         $data->defaultMailTemplateMap()->create($request->defaultMailTemplateMap);

        //     }
        // }

        $message = ($notificationMessage ?? '') ;

        return redirect()->back()->with('success', $message);

        // return redirect()->route('notification-templates.index')->with('success', $message);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $data = NotificationTemplate::findOrFail($id);
        $data->delete();

        $message = __('messages.notification_template_deleted');

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * List of trashed ertries
     * works if the softdelete is enabled.
     *
     * @return Response
     */
    public function trashed()
    {
        $module_name_singular = Str::singular($this->module_name);

        $module_action = 'Trash List';

        $data = NotificationTemplate::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate();

        return view('notificationtemplates.trash', compact('data', 'module_name_singular', 'module_action'));
    }

    /**
     * Restore a soft deleted entry.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function restore($id)
    {
        $module_name = $this->module_name;
        $module_name_singular = Str::singular($module_name);
        $$module_name_singular = NotificationTemplate::withTrashed()->find($id);
        $$module_name_singular->restore();

        flash('<i class="fas fa-check"></i> ' . label_case($module_name_singular) . ' Data Restoreded Successfully!')->success()->important();

        return redirect("app/$module_name");
    }

    public function getAjaxList(Request $request)
    {
        $items = [];
        $value = $request->q;
        switch ($request->type) {
            case 'constants':
                $items = Constant::select(\DB::raw('id,name text'))
                    ->where(function ($query) use ($value) {
                        $query->where(\DB::raw('value', 'LIKE', '%' . $value . '%'));
                        $query->orWhere('value', 'LIKE', '%' . $value . '%');
                    })
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->orderBy('sequence', 'ASC')
                    ->where('type', $request->data_type);
                $items = $items->get();
                break;
            case 'constants_key':
                $items = DB::table('constants')->select(DB::raw('value id, name text'))
                    ->where(function ($query) use ($value) {
                        $query->where(DB::raw('value', 'LIKE', '%' . $value . '%'));
                        $query->orWhere('value', 'LIKE', '%' . $value . '%');
                    })
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->orderBy('sequence', 'ASC')
                    ->where('type', $request->data_type);
                $items = $items->get();
                break;
                break;
            default:
                break;
        }
        return response()->json(['status' => 'true', 'results' => $items]);
    }

    public function notificationButton(Request $request)
    {
        $buttonTypes = Constant::where('type', 'notification_param_button')
            ->where(function ($query) use ($request) {
                $query->where('sub_type', $request->type)->orWhere('sub_type', null);
            })->get();

        return view('notificationtemplates.perameters-buttons', compact('buttonTypes'));
    }

    public function notificationTemplate(Request $request)
    {
        $detail = NotificationTemplateContentMapping::where(['template_id' => $request->template_id, 'mailable_id' => $request->mailable_id, 'language' => $request->language])->first();
        if (!isset($type)) {
            $detail = NotificationTemplate::find($request->template_id);
        }

        return response()->json(['data' => $detail, 'status' => true]);
    }


    public function updateChanels(Request $request)
    {
        $data = $request->input('template_channels');
        $page = $request->page;

        foreach ($data as $templateId => $channels) {
            $notificationTemplate = NotificationTemplate::find($templateId);

            if ($notificationTemplate) {

                $notificationTemplate->channels = $channels;

                $notificationTemplate->save();
            }
        }

        $message = __('messages.bulk_notification_setting_update');

        return redirect()->route('setting.index', ['page' => $page])->withSuccess($message);
    }

    public function parseTemplate($template, $data)
{
    foreach ($data as $key => $value) {
        $template = str_replace('[[' . $key . ']]', $value, $template);
    }
    return $template;
}

   public function fetchNotificationData(Request $request)
{
    $userType = $request->input('user_type');
    $type = $request->input('type');

    $ids = NotificationTemplate::where('type', $type)->pluck('id');

    $data = NotificationTemplateContentMapping::with('template')->whereIn('template_id', $ids)
        ->where('user_type', $userType)
        ->first();

    $notification_template_data = NotificationTemplateContentMapping::with('template')->whereIn('template_id', $ids)
        ->where('user_type', $userType)
        ->first();

    // 🔁 Dummy dynamic values – Replace with actual booking data
    $replacements = [
        'booking_id' => 'BK1234',
        'booking_services_name' => 'Car Wash',
        // Add more if needed
    ];

    if ($data) {
        // ✅ Parse placeholders
        $data->template_detail = $this->parseTemplate($data->template_detail, $replacements);
        $data->mail_template_detail = $this->parseTemplate($data->mail_template_detail, $replacements);
        $data->sms_template_detail = $this->parseTemplate($data->sms_template_detail, $replacements);
        $data->whatsapp_template_detail = $this->parseTemplate($data->whatsapp_template_detail, $replacements);

        return response()->json([
            'success' => true,
            'data' => $data,
            'notification_template_data' => $notification_template_data
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'No data found for the selected user_type.'
        ]);
    }
}

}
