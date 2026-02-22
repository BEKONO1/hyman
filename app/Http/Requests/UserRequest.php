<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Documents;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request()->id;
        $rules = [
            'username'       => 'required|max:255|unique:users,username,' . $id,
            'email'          => 'required|email|max:255|unique:users,email,' . $id,
            'contact_number' => 'required', 'unique:users,contact_number,' . $id,
            'profile_image'  => 'nullable|mimetypes:image/jpeg,image/png,image/jpg,image/gif',
        ];

        // Only validate documents if provider is registering
        if ($this->input('user_type') === 'provider' && request()->is('api/*')) {
            $allDocIds = Documents::pluck('id')->toArray();
            $rules['document_id'] = ['nullable', 'array'];
            $rules['document_id.*'] = ['in:' . implode(',', $allDocIds)];
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('user_type') === 'provider' && request()->is('api/*')) {
                $submittedIds = (array) $this->input('document_id', []);
                $requiredDocs = Documents::where('is_required', 1)->where('status', 1)->pluck('id')->toArray();

                // 1. Check missing required document IDs
                $missingRequired = array_diff($requiredDocs, $submittedIds);
                if (!empty($missingRequired)) {
                    $docNames = Documents::whereIn('id', $missingRequired)->pluck('name')->toArray();
                    $validator->errors()->add('document_id', 'Missing required documents: ' . implode(', ', $docNames));
                }

                // 2. Check if file for required document ID is uploaded
                foreach ($submittedIds as $index => $docId) {
                    if (in_array($docId, $requiredDocs)) {
                        $fileKey = "provider_document_$index";
                        if (!$this->hasFile($fileKey)) {
                            $docName = Documents::where('id', $docId)->value('name') ?? "ID $docId";
                            $validator->errors()->add($fileKey, "Missing file for required document: $docName");
                        }
                    }
                }
            }
        });
    }

    public function messages()
    {
        return [
            'profile_image.*' => __('messages.image_png_gif')
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if (request()->is('api*')) {
            $data = [
                'status' => false,
                'message' => $validator->errors()->first(),
                'all_message' =>  $validator->errors()
            ];

            throw new HttpResponseException(response()->json($data, 406));
        }

        throw new HttpResponseException(redirect()->back()->withInput()->with('errors', $validator->errors()));
    }
}
