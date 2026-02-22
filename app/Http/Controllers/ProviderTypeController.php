<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProviderType;
use App\Models\Service;
use App\Http\Requests\ProviderTypeRequest;
use Yajra\DataTables\DataTables;
use App\Traits\TranslationTrait;

class ProviderTypeController extends Controller
{
    use TranslationTrait;
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
        $pageTitle = trans('messages.list_form_title',['form' => trans('messages.providertype')] );
        $auth_user = authSession();
        $assets = ['datatable'];
        return view('providertype.index', compact('pageTitle','auth_user','assets','filter'));
    }



    public function index_data(DataTables $datatable,Request $request)
    {
        $query = ProviderType::query()->with(['translations']);
        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        if (auth()->user()->hasAnyRole(['admin'])) {
            $query->withTrashed();
        }
        $primary_locale = app()->getLocale() ?? 'en';
        return $datatable->eloquent($query)
        ->addColumn('check', function ($row) {
            return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" data-type="providertype" onclick="dataTableRowCheck('.$row->id.',this)">';
        })
         

            ->editColumn('name', function($query) use($primary_locale){    
                $name = $this->getTranslation($query->translations, $primary_locale, 'name', $query->name) ?? $query->name;

                if (auth()->user()->can('providertype edit')) {
                    return '<a class="btn-link btn-link-hover" href="' . route('providertype.create', ['id' => $query->id]) . '">' . $name . '</a>';
                }

                return $name ?? '-';
            })
            ->filterColumn('name',function($query,$keyword) use($primary_locale){
                if ($primary_locale !== 'en') {
                    $query->where(function ($query) use ($keyword, $primary_locale) {
                        $query->whereHas('translations', function($query) use ($keyword, $primary_locale) {
                                // Search in the translations table based on the primary_locale
                                $query->where('locale', $primary_locale)
                                      ->where('value', 'LIKE', '%'.$keyword.'%');
                            })
                            ->orWhere('name', 'LIKE', '%'.$keyword.'%'); // Fallback to 'name' field if no translation is found
                    });
                } else {
                    $query->where('name', 'LIKE', '%'.$keyword.'%');
                }
               
            })
            ->editColumn('commission' , function ($query){
                $commission = getPriceFormat($query->commission);
                if($query->type === 'percent'){
                    $commission = $query->commission. '%';
                }
                return $commission;
            })
            ->editColumn('status' , function ($query){
                $disabled = $query->trashed() ? 'disabled': '';
                return '<div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <div class="custom-switch-inner">
                        <input type="checkbox" class="custom-control-input  change_status" data-type="providertype_status" '.($query->status ? "checked" : "").'  '.$disabled.' value="'.$query->id.'" id="'.$query->id.'" data-id="'.$query->id.'">
                        <label class="custom-control-label" for="'.$query->id.'" data-on-label="" data-off-label=""></label>
                    </div>
                </div>';
            })
            ->addColumn('action', function($providertype){
                return view('providertype.action',compact('providertype'))->render();
            })
            ->addIndexColumn()
            ->rawColumns(['check','name','action','status'])
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
                $branches = ProviderType::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = 'Bulk Provider Type List Status Updated';
                break;

            case 'delete':
                ProviderType::whereIn('id', $ids)->delete();
                $message = 'Bulk Provider Type List Deleted';
                break;

            case 'restore':
                ProviderType::whereIn('id', $ids)->restore();
                $message = 'Bulk Provider Type Restored';
                break;
                
            case 'permanently-delete':
                ProviderType::whereIn('id', $ids)->forceDelete();
                $message = 'Bulk Provider Type Permanently Deleted';
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
        if (!auth()->user()->can('providertype add')) {
            return redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $id = $request->id;
        $auth_user = authSession();
        $primary_locale = app()->getLocale() ?? 'en';
        $language_array = $this->languagesArray();
        $providertypedata = ProviderType::find($id);
        $pageTitle = trans('messages.update_form_title',['form'=>trans('messages.providertype')]);
        
        if($providertypedata == null){
            $pageTitle = trans('messages.add_button_form',['form' => trans('messages.providertype')]);
            $providertypedata = new ProviderType;
        }
        
        return view('providertype.create', compact('pageTitle' ,'providertypedata' ,'auth_user','language_array' ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProviderTypeRequest $request)
    {
        if(demoUserPermission()){
            return  redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $data = $request->all();
       
        if($request->has('type') && $request->type == 'percentage'){
            $data['type'] = 'percent';
        }
        $language_option = sitesetupSession('get')->language_option ?? ["ar","nl","en","fr","de","hi","it"];

        $primary_locale = app()->getLocale() ?? 'en';
        $translatableAttributes = ['name', 'description'];
        $result = ProviderType::updateOrCreate(['id' => $data['id'] ],$data);
        if ($request->is('api/*')) {
            // Decode API JSON string
            $data['translations'] = json_decode($data['translations'] ?? '{}', true);
        } elseif (isset($data['translations']) && is_array($data['translations'])) {
            // Web request already provides translations as an array
            $data['translations'] = $data['translations'];
        }
        $result->saveTranslations($data, $translatableAttributes, $language_option, $primary_locale);
        $message = trans('messages.update_form',['form' => trans('messages.providertype')]);
        if($result->wasRecentlyCreated){
            $message = trans('messages.save_form',['form' => trans('messages.providertype')]);
        }
        if($request->is('api/*')) {
            return comman_message_response($message);
		}
        return redirect(route('providertype.index'))->withSuccess($message);        
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
        $providertype = ProviderType::find($id);
        $msg= __('messages.msg_fail_to_delete',['item' => __('messages.providertype')] );
        
        if($providertype != '') { 
            $providertype->delete();
            $msg= __('messages.msg_deleted',['name' => __('messages.providertype')] );
        }
        if(request()->is('api/*')) {
            return comman_message_response($msg);
		}
        return comman_custom_response(['message'=> $msg, 'status' => true]);
    }
    public function action(Request $request){
        if(demoUserPermission()){
            return  redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $id = $request->id;

        $provider_type  = ProviderType::withTrashed()->where('id',$id)->first();
        $msg = __('messages.not_found_entry',['name' => __('messages.providertype')] );
        if($request->type == 'restore') {
            $provider_type->restore();
            $msg = __('messages.msg_restored',['name' => __('messages.providertype')] );
        }
        if($request->type === 'forcedelete'){
            $provider_type->forceDelete();
            $msg = __('messages.msg_forcedelete',['name' => __('messages.providertype')] );
        }
        if($request->is('api/*')) {
            return comman_message_response($msg);
		}

        return comman_custom_response(['message'=> $msg , 'status' => true]);
    }
}
