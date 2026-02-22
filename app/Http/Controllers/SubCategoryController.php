<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubCategory;
use App\Http\Requests\SubCategoryRequest;
use Facade\Ignition\QueryRecorder\Query;
use Yajra\DataTables\DataTables;
use App\Traits\TranslationTrait;
use Illuminate\Support\Str;
class SubCategoryController extends Controller
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
        $pageTitle = trans('messages.list_form_title',['form' => trans('messages.subcategory')] );
        $auth_user = authSession();
        $assets = ['datatable'];
        $globalSeoSetting = \App\Models\SeoSetting::first();
        return view('subcategory.index',compact('pageTitle','auth_user','assets','filter', 'globalSeoSetting'));
    }

    public function index_data(DataTables $datatable,Request $request)
    {
        $query = SubCategory::query()->with(['translations', 'category.translations']);
        if (!$request->order) { 
            $query->orderBy('created_at', 'DESC');
        } 
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

                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" data-type="subcategory" onclick="dataTableRowCheck('.$row->id.', this)">';
            })


            ->editColumn('name', function($row) use ($primary_locale){                
                $name = $this->getTranslation($row->translations, $primary_locale, 'name', $row->name) ?? $row->name;  

                if (auth()->user()->can('subcategory edit')) {
                    return '<a class="btn-link btn-link-hover" href="' . route('subcategory.create', ['id' => $row->id]) . '">' . $name . '</a>';
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
            ->editColumn('category_id' , function ($query) use ($primary_locale){
                $catname = $this->getTranslation($query->category->translations, $primary_locale, 'name', $query->category->name) ?? $query->category->name;
                return $catname ?? '-';
                //return ($query->category_id != null && isset($query->category)) ? $query->category->name : '-';
            })
            ->filterColumn('category_id',function($query,$keyword) use($primary_locale){
                $query->whereHas('category', function ($q) use ($keyword, $primary_locale) {
                    // Check if the locale is not 'en'
                    if ($primary_locale !== 'en') {
                        $q->where(function ($q) use ($keyword, $primary_locale) {
                            // Search in the translations table for the given locale
                            $q->whereHas('translations', function ($q) use ($keyword, $primary_locale) {
                                $q->where('locale', $primary_locale)
                                  ->where('value', 'LIKE', '%' . $keyword . '%');
                            })
                            // Fallback to checking 'name' field if no translation is found
                            ->orWhere('name', 'LIKE', '%' . $keyword . '%');
                        });
                    } else {
                        // If locale is 'en', search directly in the 'name' field
                        $q->where('name', 'LIKE', '%' . $keyword . '%');
                    }
                });
            })
            ->orderColumn('category_id', function ($query, $order) {
                $query->join('categories', 'categories.id', '=', 'sub_categories.category_id')
                      ->orderBy('categories.name', $order);
            })
            ->editColumn('is_featured' , function ($query){
                $disabled = $query->trashed() ? 'disabled': '';

                return '<div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <div class="custom-switch-inner">
                        <input type="checkbox" class="custom-control-input change_status" data-type="subcategory_featured" data-name="is_featured" '.($query->is_featured ? "checked" : "").'  '.  $disabled.' value="'.$query->id.'" id="f'.$query->id.'" data-id="'.$query->id.'">
                        <label class="custom-control-label" for="f'.$query->id.'" data-on-label="'.__("messages.yes").'" data-off-label="'.__("messages.no").'"></label>
                    </div>
                </div>';
            })
            ->addColumn('action', function ($data) {
                return view('subcategory.action', compact('data'));
            })
            ->editColumn('status' , function ($query){
                $disabled = $query->trashed() ? 'disabled': '';
                return '<div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <div class="custom-switch-inner">
                        <input type="checkbox" class="custom-control-input  change_status" data-type="subcategory_status" '.($query->status ? "checked" : "").'  '.$disabled.' value="'.$query->id.'" id="'.$query->id.'" data-id="'.$query->id.'">
                        <label class="custom-control-label" for="'.$query->id.'" data-on-label="" data-off-label=""></label>
                    </div>
                </div>';
            })

            ->rawColumns(['action', 'status', 'check','is_featured','name'])
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
                $branches = SubCategory::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = 'Bulk Sub Category Status Updated';
                break;
            
            case 'change-featured':
                $branches = SubCategory::whereIn('id', $ids)->update(['is_featured' => $request->is_featured]);
                $message = 'Bulk Sub Category Featured Updated';
                break;

            case 'delete':
                SubCategory::whereIn('id', $ids)->delete();
                $message = 'Bulk Sub Category Deleted';
                break;
                
            case 'restore':
                SubCategory::whereIn('id', $ids)->restore();
                $message = 'Bulk Sub Category Restored';
                break;
                
            case 'permanently-delete':
                SubCategory::whereIn('id', $ids)->forceDelete();
                $message = 'Bulk Sub Category Permanently Deleted';
                break;

                default:
                return response()->json(['status' => false,'is_featured' => false, 'message' => 'Action Invalid']);
                break;
        }

        return response()->json(['status' => true, 'is_featured' => true, 'message' => $message]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (!auth()->user()->can('subcategory add')) {
            return redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $id = $request->id;
        $auth_user = authSession();
        $language_array = $this->languagesArray();
        $subcategory = SubCategory::find($id);
        $pageTitle = trans('messages.update_form_title',['form'=>trans('messages.subcategory')]);
        
        if($subcategory == null){
            $pageTitle = trans('messages.add_button_form',['form' => trans('messages.subcategory')]);
            $subcategory = new SubCategory;
        }
        
        $globalSeoSetting = \App\Models\SeoSetting::first();
        return view('subcategory.create', compact('pageTitle' ,'subcategory' ,'auth_user','language_array', 'globalSeoSetting'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubCategoryRequest $request)
    {
        if(demoUserPermission()){
            return  redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $data = $request->all();
        $data['is_featured'] = $request->has('is_featured') ? $request->is_featured : 0;
        $data['seo_enabled'] = $request->has('seo_enabled') ? $request->seo_enabled : 0;
        if ($request->filled('meta_title')) {
            $data['slug'] = $request->has('meta_title') ? Str::slug($request->meta_title) : null;
        }
        $language_option = sitesetupSession('get')->language_option ?? ["ar","nl","en","fr","de","hi","it"];

        $primary_locale = app()->getLocale() ?? 'en';
        $translatableAttributes = ['name', 'description'];
        if(!$request->is('api/*')) {
            if($request->id == null ){
                if(!isset($data['subcategory_image'])){
                    return  redirect()->back()->withErrors(__('validation.required',['attribute' =>'attachments']));
                }
            }
        }

        
        $result = SubCategory::updateOrCreate(['id' => $data['id'] ],$data);
        if ($request->is('api/*')) {
            // Decode API JSON string
            $data['translations'] = json_decode($data['translations'] ?? '{}', true);
        } elseif (isset($data['translations']) && is_array($data['translations'])) {
            // Web request already provides translations as an array
            $data['translations'] = $data['translations'];
        }
        $result->saveTranslations($data, $translatableAttributes, $language_option, $primary_locale);
         if ($request->hasFile('subcategory_image')) {
             storeMediaFile($result,$request->subcategory_image, 'subcategory_image');
         } elseif (!getMediaFileExit($result, 'subcategory_image')) {
             return redirect()->route('subcategory.create', ['id' => $result->id])
             ->withErrors(['subcategory_image' => 'The attachments field is required.'])
             ->withInput();
         }

        // Handle SEO image upload
        if ($request->hasFile('seo_image')) {
            storeMediaFile($result, $request->file('seo_image'), 'seo_image');
         }

        $message = trans('messages.update_form',['form' => trans('messages.subcategory')]);
        if($result->wasRecentlyCreated){
            $message = trans('messages.save_form',['form' => trans('messages.subcategory')]);
        }
        if($request->is('api/*')) {
            return comman_message_response($message);
		}
        return redirect(route('subcategory.index'))->withSuccess($message);    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $locale = app()->getLocale();
        $subcategory = \App\Models\SubCategory::findOrFail($id);
        $globalSeoSetting = \App\Models\SeoSetting::first();

        // Fallback logic: use subcategory meta if set, else global
        $metaTitle = $subcategory->translate('meta_title', $locale) ?? $subcategory->meta_title ?? $globalSeoSetting->meta_title ?? $subcategory->name;
        $metaDescription = $subcategory->translate('meta_description', $locale) ?? $subcategory->meta_description ?? $globalSeoSetting->meta_description ?? '';
        $metaKeywords = $subcategory->translate('meta_keywords', $locale) ?? $subcategory->meta_keywords ?? $globalSeoSetting->meta_keywords ?? '';
        $slug = $subcategory->translate('slug', $locale) ?? $subcategory->slug ?? $globalSeoSetting->slug ?? '';
        // SEO image: try subcategory's localized image, else global
        $seoImage = $subcategory->getFirstMediaUrl('seo_image_' . $locale);
        if (empty($seoImage) && $globalSeoSetting) {
            $seoImage = $globalSeoSetting->getFirstMediaUrl('seo_image');
        }
        $auth_user = authSession();
        $filter = ['status' => null];
        $pageTitle = trans('messages.list_form_title',['form' => trans('messages.subcategory')] );
        return view('subcategory.index', compact('subcategory', 'metaTitle', 'metaDescription', 'metaKeywords', 'slug', 'seoImage', 'auth_user', 'filter', 'pageTitle', 'globalSeoSetting'));
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
        $subcategory = SubCategory::find($id);
        $msg= __('messages.msg_fail_to_delete',['name' => __('messages.subcategory')] );
        
        if($subcategory!='') { 
            $subcategory->delete();
            $msg= __('messages.msg_deleted',['name' => __('messages.subcategory')] );
        }
        if(request()->is('api/*')) {
            return comman_message_response($msg);
		}
        return comman_custom_response(['message'=> $msg , 'status' => true]);
    }
    public function action(Request $request){
        $id = $request->id;

        $subcategory  = SubCategory::withTrashed()->where('id',$id)->first();
        $msg = __('messages.not_found_entry',['name' => __('messages.subcategory')] );
        if($request->type == 'restore') {
            $subcategory->restore();
            $msg = __('messages.msg_restored',['name' => __('messages.subcategory')] );
        }
        if($request->type === 'forcedelete'){
            $subcategory->forceDelete();
            $msg = __('messages.msg_forcedelete',['name' => __('messages.subcategory')] );
        }
        if(request()->is('api/*')){
            return comman_message_response($msg);
		}
        return comman_custom_response(['message'=> $msg , 'status' => true]);
    }
}
