<?php

namespace App\Http\Controllers;

use App\Models\MailTemplates;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use App\Models\Constant;
use App\Models\MailTemplateContentMapping;
use Illuminate\Support\Facades\DB;

class MailTemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                $branches = MailTemplates::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = 'Bulk MailTemplate Status Updated';
                break;

            case 'delete':
                MailTemplates::whereIn('id', $ids)->delete();
                $message = 'Bulk MailTemplate Deleted';
                break;

            default:
                return response()->json(['status' => false, 'message' => __('branch.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function index_list(Request $request)
    {
        $query_data = MailTemplates::with('defaultMailTemplateMap', 'constant')->get();

        $data = [];

        $mailKeyChannels = array_keys(config('mailtemplate.channels'));

        $arr = [];
        // For Channel Map Or Update Channel Value
        foreach ($mailKeyChannels as $key => $value) {
            $arr[$value] = 0;
        }

        foreach ($query_data as $key => $value) {
            $data[$key] = [
                'id' => $value->id,
                'type' => $value->type,
                'template' => $value->defaultMailTemplateMap->subject,
                'is_default' => false,
            ];

            if (isset($value->channels)) {
                $data[$key]['channels'] = $value->channels;
            } else {
                $data[$key]['channels'] = $arr;
            }
        }

        $mailtemplate = config('mailtemplate.channels');

        return response()->json(['data' => $data, 'channels' => $mailtemplate, 'status' => true, 'message' => __('messages.mail_temp_list')]);
    }

    public function update_status(Request $request, MailTemplates $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('branch.status_update')]);
    }

    public function index_data(Datatables $datatable, Request $request)
    {

        $query = MailTemplates::query()->with('defaultMailTemplateMap');
        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" data-type="mailtemplate" onclick="dataTableRowCheck(' . $row->id . ',this)">';
            })
            ->addColumn('action', function ($data) {
                return view('mailtemplates.action', compact('data'));
            })
            ->editColumn('label', function ($row) {
                return '<a href="' . route('mail-templates.edit', $row->id) . '">' . optional($row->defaultMailTemplateMap)->subject . '</a>';
            })
            ->editColumn('status', function ($query) {
                $disabled = $query->trashed() ? 'disabled' : '';
                return '<div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <div class="custom-switch-inner">
                        <input type="checkbox" class="custom-control-input  change_status" data-type="mailtemplate_status" ' . ($query->status ? "checked" : "") . '  ' . $disabled . ' value="' . $query->id . '" id="' . $query->id . '" data-id="' . $query->id . '">
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
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $module_action = 'Create';

        $assets = ['textarea'];

        return view('mailtemplates.create', compact('module_action', 'assets'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $map = $request->defaultMailTemplateMap;
        $request->merge(['type' => $request->type]);
        $page = 'mail-templates';
        $map['subject'] = $request->defaultMailTemplateMap['subject'];
        $map['user_type'] = $request->defaultMailTemplateMap['user_type'];
        $map['notification_message'] = $request->defaultMailTemplateMap['notification_message'];
        $map['notification_link'] = $request->defaultMailTemplateMap['notification_link'];

        $request['to'] = isset($request->to) ? json_encode($request->to) : null;
        $request['bcc'] = isset($request->bcc) ? json_encode($request->bcc) : null;
        $request['cc'] = isset($request->cc) ? json_encode($request->cc) : null;
        $data = MailTemplates::create($request->all());
        $data->defaultMailTemplateMap()->create($map);

        $message = (__('messages.msg_added', ['name' => __('messages.mailable')]));

        return redirect()->route('mail-templates.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MailTemplates  $mailTemplates
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $module_name = $this->module_name;

        $module_name_singular = Str::singular($module_name);

        $module_action = 'Show';

        $data = MailTemplates::findOrFail($id);

        return view('mailtemplates.show', compact('module_name_singular', 'module_action', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MailTemplates  $mailTemplates
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pageTitle = trans('messages.setting');
        $pageTitle1 = trans('messages.mail_templates');
        $page = 'mail-templates';
        $module_action = 'Edit';
        $data = MailTemplates::with('defaultMailTemplateMap', 'constant')->findOrFail($id);
        $buttonTypes = Constant::where('type', 'notification_param_button')
            ->where(function ($query) use ($data) {
                $query->where('sub_type', $data->type)->orWhere('sub_type', null);
            })->get();

        $assets = ['textarea'];

        return view('setting.edit', compact('module_action', 'data', 'assets', 'buttonTypes', 'pageTitle', 'pageTitle1', 'page'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MailTemplates  $mailTemplates
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $page = 'mail-templates';
        $userType = $request->defaultMailTemplateMap['user_type'];
        $type = $request->type;

        $ids = MailTemplates::where('type', $type)->pluck('id');

        $check = MailTemplateContentMapping::with('template')
            ->whereIn('template_id', $ids)
            ->where('user_type', $userType)
            ->first();

        if ($check !== null) {
            $data = MailTemplateContentMapping::find($check->id);
            if ($data !== null) {
                $map = $request->defaultMailTemplateMap;
                $data->update([
                    'subject' => $map['subject'],
                    'template_detail' => $map['template_detail'],
                    'notification_message' => $map['notification_message'],
                    'notification_link' => $map['notification_link'],
                ]);
                
                $data->template->update([
                    'to' => isset($request->to) ? json_encode($request->to) : null,
                    'bcc' => isset($request->bcc) ? json_encode($request->bcc) : null,
                    'cc' => isset($request->cc) ? json_encode($request->cc) : null,
                    'status' => $request->has('status') ? 1 : 0,
                ]);

                $message = __('messages.mail_template_updated');
            } else {
                $message = __('messages.mail_template_not_found');
            }
        } else {
            $data = MailTemplates::updateOrCreate(['type' => $type], [
                'name' => $type,
                'description' => $request->description,
                'to' => isset($request->to) ? json_encode($request->to) : null,
                'bcc' => isset($request->bcc) ? json_encode($request->bcc) : null,
                'cc' => isset($request->cc) ? json_encode($request->cc) : null,
                'status' => $request->has('status') ? 1 : 0,
            ]);

            $data->defaultMailTemplateMap()->create($request->defaultMailTemplateMap);

            $message = __('messages.mail_template_created');
        }

        return redirect()->route('setting.index', ['page' => $page])->withSuccess($message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MailTemplates  $mailTemplates
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = MailTemplates::findOrFail($id);
        $data->delete();

        $message = __('messages.mail_template_deleted');

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

        $data = MailTemplates::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate();

        return view('mailtemplates.trash', compact('data', 'module_name_singular', 'module_action'));
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
        $$module_name_singular = MailTemplates::withTrashed()->find($id);
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

    public function mailButton(Request $request)
    {
        $buttonTypes = Constant::where('type', 'notification_param_button')
            ->where(function ($query) use ($request) {
                $query->where('sub_type', $request->type)->orWhere('sub_type', null);
            })->get();

        return view('mailtemplates.perameters-buttons', compact('buttonTypes'));
    }

    public function mailTemplate(Request $request)
    {
        $detail = MailTemplateContentMapping::where(['template_id' => $request->template_id, 'mailable_id' => $request->mailable_id, 'language' => $request->language])->first();
        if (!isset($type)) {
            $detail = MailTemplates::find($request->template_id);
        }

        return response()->json(['data' => $detail, 'status' => true]);
    }


    public function updateChanels(Request $request)
    {
        $data = $request->input('template_channels');
        $page = $request->page;

        foreach ($data as $templateId => $channels) {
            $mailTemplate = MailTemplates::find($templateId);

            if ($mailTemplate) {

                $mailTemplate->channels = $channels;

                $mailTemplate->save();
            }
        }

        $message = __('messages.bulk_mail_setting_update');

        return redirect()->route('setting.index', ['page' => $page])->withSuccess($message);
    }

    public function fetchData(Request $request)
    {

        $userType = $request->input('user_type');
        $type = $request->input('type');

        $ids = MailTemplates::where('type', $type)->pluck('id');

        $data = MailTemplateContentMapping::with('template')->whereIn('template_id', $ids)
            ->where('user_type', $userType)
            ->first();

        if ($data) {
            return response()->json(['success' => true, 'data' => $data]);
        } else {
            return response()->json(['success' => false, 'message' => 'No data found for the selected user_type.']);
        }
    }
}
