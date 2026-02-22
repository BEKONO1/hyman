<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\Booking;
use App\Models\User;
use App\Models\ProviderDocument;
use App\Models\Coupon;
use App\Models\Documents;
use App\Models\Slider;
use App\Models\Blog;
use App\Http\Requests\CategoryRequest;
use App\Models\ProviderType;
use Yajra\DataTables\DataTables;
use League\CommonMark\Node\Block\Document as BlockDocument;
use App\Models\NotificationTemplate;
use App\Traits\TranslationTrait;
use App\Models\ServiceZone;
use App\Models\SeoSetting;
use Illuminate\Support\Str;

class CategoryController extends Controller
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
        $pageTitle = trans('messages.list_form_title', ['form' => trans('messages.category')]);
        $auth_user = authSession();
        $assets = ['datatable'];
        $globalSeoSetting = \App\Models\SeoSetting::first();
        return view('category.index', compact('pageTitle','auth_user','assets','filter', 'globalSeoSetting'));
    }


    public function index_data(DataTables $datatable, Request $request)
    {
        $query = Category::with('translations');
        $filter = $request->filter;
        $primary_locale = app()->getLocale() ?? 'en';
        if (!$request->order) {
            $query->orderBy('created_at', 'DESC');
        }
        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        if (auth()->user()->hasAnyRole(['admin'])) {
            $query->withTrashed();
        }

        return $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" data-type="category" onclick="dataTableRowCheck(' . $row->id . ',this)">';
            })

            ->editColumn('name', function ($query) use ($primary_locale) {
                $name = $this->getTranslation($query->translations, $primary_locale, 'name', $query->name) ?? $query->name;
                if (auth()->user()->can('category edit')) {
                    $link = '<a class="btn-link btn-link-hover" href=' . route('category.create', ['id' => $query->id]) . '>' . $name . '</a>';
                } else {
                    $link = $name;
                }
                return $link;
            })
            ->filterColumn('name', function ($query, $keyword) use ($primary_locale) {
                if ($primary_locale !== 'en') {
                    $query->where(function ($query) use ($keyword, $primary_locale) {
                        $query->whereHas('translations', function ($query) use ($keyword, $primary_locale) {
                            // Search in the translations table based on the primary_locale
                            $query->where('locale', $primary_locale)
                                ->where('value', 'LIKE', '%' . $keyword . '%');
                        })
                            ->orWhere('name', 'LIKE', '%' . $keyword . '%'); // Fallback to 'name' field if no translation is found
                    });
                } else {
                    $query->where('name', 'LIKE', '%' . $keyword . '%');
                }
            })
            ->addColumn('action', function ($data) {
                return view('category.action', compact('data'))->render();
            })
            ->editColumn('is_featured', function ($query) {
                $disabled = $query->trashed() ? 'disabled' : '';

                return '<div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <div class="custom-switch-inner">
                        <input type="checkbox" class="custom-control-input  change_status" data-type="category_featured" data-name="is_featured" ' . ($query->is_featured ? "checked" : "") . '  ' .  $disabled . ' value="' . $query->id . '" id="f' . $query->id . '" data-id="' . $query->id . '">
                        <label class="custom-control-label" for="f' . $query->id . '" data-on-label="' . __("messages.yes") . '" data-off-label="' . __("messages.no") . '"></label>
                    </div>
                </div>';
            })
            ->editColumn('status', function ($query) {
                $disabled = $query->trashed() ? 'disabled' : '';
                return '<div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <div class="custom-switch-inner">
                        <input type="checkbox" class="custom-control-input  change_status" data-type="category_status" ' . ($query->status ? "checked" : "") . '  ' . $disabled . ' value="' . $query->id . '" id="' . $query->id . '" data-id="' . $query->id . '">
                        <label class="custom-control-label" for="' . $query->id . '" data-on-label="" data-off-label=""></label>
                    </div>
                </div>';
            })
            ->editColumn('description', function ($query) use ($primary_locale) {
                $description = $this->getTranslation($query->translations, $primary_locale, 'description', $query->description) ?? $query->description;

                return $description ?? '-';
            })
            ->rawColumns(['action', 'status', 'check', 'is_featured', 'name', 'description'])
            ->toJson();
    }

    /* bulck action method */
    public function bulk_action(Request $request)
    {
        $request->validate([
            'rowIds' => 'required|string',
            'action_type' => 'required|string'
        ]);

        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;

        switch ($actionType) {
            case 'change-status':
                Category::whereIn('id', $ids)->update(['status' => $request->status]);
                return response()->json(['status' => true, 'message' => 'Status updated']);

            case 'change-featured':
                Category::whereIn('id', $ids)->update(['is_featured' => $request->is_featured]);
                return response()->json(['status' => true, 'message' => 'Featured updated']);

            case 'delete':
                if (!auth()->user()->can('category delete')) {
                    return response()->json(['status' => false, 'message' => trans('messages.permission_denied')]);
                }
                Category::whereIn('id', $ids)->delete();
                return response()->json(['status' => true, 'message' => 'Deleted']);

            case 'restore':
                if (!auth()->user()->can('category delete')) {
                    return response()->json(['status' => false, 'message' => trans('messages.permission_denied')]);
                }
                Category::whereIn('id', $ids)->restore();
                return response()->json(['status' => true, 'message' => 'Restored']);

            case 'permanently-delete':
                if (!auth()->user()->can('category delete')) {
                    return response()->json(['status' => false, 'message' => trans('messages.permission_denied')]);
                }
                Category::whereIn('id', $ids)->forceDelete();
                return response()->json(['status' => true, 'message' => 'Permanently Deleted']);

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
        if (!auth()->user()->can('category add')) {
            return redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $id = $request->id;
        $auth_user = authSession();
        $primary_locale = app()->getLocale() ?? 'en';
        $language_array = $this->languagesArray();
        $categorydata = Category::find($id);

        $pageTitle = trans('messages.update_form_title', ['form' => trans('messages.category')]);

        if ($categorydata == null) {
            $pageTitle = trans('messages.add_button_form', ['form' => trans('messages.category')]);
            $categorydata = new Category;
        }

        $globalSeoSetting = \App\Models\SeoSetting::first();

        return view('category.create', compact('pageTitle' ,'categorydata' ,'auth_user','language_array', 'globalSeoSetting'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        if (demoUserPermission()) {
            return redirect()->back()
                ->withErrors(trans('messages.demo_permission_denied'))
                ->withInput();
        }

        $data = $request->all();

        $data['is_featured'] = $request->has('is_featured') ? $request->is_featured : 0;
        $language_option = sitesetupSession('get')->language_option ?? ["ar", "nl", "en", "fr", "de", "hi", "it"];
        $data = $request->except('seo_image'); // Exclude seo_image from direct save

        $data['is_featured'] = $request->has('is_featured') ? $request->is_featured : 0;
        $data['seo_enabled'] = $request->has('seo_enabled') ? $request->seo_enabled : 0;
        if ($request->filled('meta_title')) {
            $data['slug'] = $request->has('meta_title') ? Str::slug($request->meta_title) : null;
        }
        
        
        $language_option = sitesetupSession('get')->language_option ?? ["ar","nl","en","fr","de","hi","it"];

        $primary_locale = app()->getLocale() ?? 'en';
        $translatableAttributes = ['name', 'description'];

        if (!$request->is('api/*') && is_null($request->id) && !$request->hasFile('category_image')) {
            return redirect()->route('category.create')
                ->withErrors(__('validation.required', ['attribute' => 'attachments']))
                ->withInput();
        }

        $result = Category::updateOrCreate(['id' => $data['id']], $data);
        if ($request->is('api/*')) {
            // Decode API JSON string
            $data['translations'] = json_decode($data['translations'] ?? '{}', true);
        } elseif (isset($data['translations']) && is_array($data['translations'])) {
            // Web request already provides translations as an array
            $data['translations'] = $data['translations'];
        }
        $result->saveTranslations($data, $translatableAttributes, $language_option, $primary_locale);
        if ($request->hasFile('category_image')) {
            storeMediaFile($result, $request->file('category_image'), 'category_image');
        } elseif (!getMediaFileExit($result, 'category_image')) {
            return redirect()->route('category.create', ['id' => $result->id])
                ->withErrors(['category_image' => 'The attachments field is required.'])
                ->withInput();
           
        }
        // Handle SEO image upload via media library only
        if ($request->hasFile('seo_image')) {
            storeMediaFile($result, $request->file('seo_image'), 'seo_image');
        }

        $message = $result->wasRecentlyCreated
            ? trans('messages.save_form', ['form' => trans('messages.category')])
            : trans('messages.update_form', ['form' => trans('messages.category')]);

        if ($request->is('api/*')) {
            return comman_message_response($message);
        }

        return redirect(route('category.index'))->withSuccess($message);
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
        $category = \App\Models\Category::findOrFail($id);
        $globalSeoSetting = \App\Models\SeoSetting::first();

        // Fallback logic: use category meta if set, else global
        $metaTitle = $category->translate('meta_title', $locale) ?? $category->meta_title ?? $globalSeoSetting->meta_title ?? $category->name;
        $metaDescription = $category->translate('meta_description', $locale) ?? $category->meta_description ?? $globalSeoSetting->meta_description ?? '';
        $metaKeywords = $category->translate('meta_keywords', $locale) ?? $category->meta_keywords ?? $globalSeoSetting->meta_keywords ?? '';
        $slug = $category->translate('slug', $locale) ?? $category->slug ?? $globalSeoSetting->slug ?? '';
        // SEO image: try category's localized image, else global
        $seoImage = $category->getFirstMediaUrl('seo_image_' . $locale);
        if (empty($seoImage) && $globalSeoSetting) {
            $seoImage = $globalSeoSetting->getFirstMediaUrl('seo_image');
        }
        $pageTitle = trans('messages.list_form_title',['form' => trans('messages.category')] );
        return view('category.index', compact('category', 'metaTitle', 'metaDescription', 'metaKeywords', 'slug', 'seoImage', 'pageTitle', 'globalSeoSetting'));
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
        if (!auth()->user()->can('category delete')) {
            return response()->json(['status' => false, 'message' => trans('messages.permission_denied')]);
        }

        if (demoUserPermission()) {
            return response()->json(['status' => false, 'message' => trans('messages.demo_permission_denied')]);
        }

        try {
            $category = Category::findOrFail($id);
            $category->delete();
            return response()->json(['status' => true, 'message' => trans('messages.category_deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => trans('messages.error_deleting', ['name' => trans('messages.category')])]);
        }
    }

    public function action(Request $request)
    {
        if (!auth()->user()->can('category delete')) {
            return response()->json(['status' => false, 'message' => trans('messages.permission_denied')]);
        }

        try {
            $id = $request->id;
            $category = Category::withTrashed()->findOrFail($id);
            $type = $request->type;

            switch ($type) {
                case 'restore':
                    $category->restore();
                    $message = trans('messages.restored_successfully');
                    break;

                case 'forcedelete':
                    $category->forceDelete();
                    $message = trans('messages.Categories_permanently_deleted');
                    break;

                default:
                    return response()->json(['status' => false, 'message' => trans('messages.invalid_action')]);
            }

            return response()->json(['status' => true, 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => trans('messages.something_wrong')]);
        }
    }

    public function check_in_trash(Request $request)
    {

        $ids = $request->ids;
        $type = $request->datatype;

        switch ($type) {
            case 'category':
                $InTrash = Category::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'subcategory':
                $InTrash = SubCategory::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'service':
                $InTrash = Service::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'servicepackage':
                $InTrash = ServicePackage::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'booking':
                $InTrash = Booking::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'user':
                $InTrash = User::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'providertype':
                $InTrash = ProviderType::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'providerdocument':
                $InTrash = ProviderDocument::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'coupon':
                $InTrash = Coupon::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'slider':
                $InTrash = Slider::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'document':
                $InTrash = Documents::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'blog':
                $InTrash = Blog::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'notificationtemplate':
                $InTrash = NotificationTemplate::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;
            case 'servicezone':
                $InTrash = ServiceZone::withTrashed()->whereIn('id', $ids)->whereNotNull('deleted_at')->get();
                break;

            default:
                break;
        }

        if (count($InTrash) === count($ids)) {
            return response()->json(['all_in_trash' => true]);
        }

        return response()->json(['all_in_trash' => false]);
    }
}
