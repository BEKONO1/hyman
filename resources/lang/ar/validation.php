<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'يجب قبول السمة :attribute.',
    'active_url' => 'السمة :attribute ليست عنوان URL صالح.',
    'after' => 'يجب أن تكون السمة :attribute تاريخاً بعد :date.',
    'after_or_equal' => 'يجب أن تكون السمة :attribute تاريخاً بعد أو مساوياً ل :date.',
    'alpha' => 'يجب أن تحتوي السمة :attribute فقط على حروف.',
    'alpha_dash' => 'يجب أن تحتوي السمة :attribute فقط على حروف وأرقام وشرطات وتحديدات.',
    'alpha_num' => 'يجب أن تحتوي السمة :attribute فقط على حروف وأرقام.',
    'array' => 'يجب أن تكون السمة :attribute مصفوفة.',
    'before' => 'يجب أن تكون السمة :attribute تاريخاً قبل :date.',
    'before_or_equal' => 'يجب أن تكون السمة :attribute تاريخاً قبل أو مساوياً ل :date.',
    'between' => [
        'numeric' => 'يجب أن تكون السمة :attribute بين :min و :max.',
        'file' => 'يجب أن تكون السمة :attribute بين :min و :max كيلوبايت.',
        'tring' => 'يجب أن تكون السمة :attribute بين :min و :max حرفاً.',
        'array' => 'يجب أن تحتوي السمة :attribute بين :min و :max عناصر.',
    ],
    'boolean' => 'يجب أن يكون حقل السمة :attribute صحيحاً أو خاطئاً.',
    'confirmed' => 'تأكيد السمة :attribute لا يطابق.',
    'date' => 'السمة :attribute ليست تاريخاً صالحاً.',
    'date_equals' => 'يجب أن تكون السمة :attribute تاريخاً مساوياً ل :date.',
    'date_format' => 'السمة :attribute لا تطابق التنسيق :format.',
    'different' => 'يجب أن تكون السمة :attribute و :other مختلفين.',
    'digits' => 'يجب أن تكون السمة :attribute :digits أرقاماً.',
    'digits_between' => 'يجب أن تكون السمة :attribute بين :min و :max أرقاماً.',
    'dimensions' => 'السمة :attribute لها أبعاد صورة غير صالحة.',
    'distinct' => 'حقل السمة :attribute لديه قيمة مكررة.',
    'email' => 'يجب أن تكون السمة :attribute عنوان بريد إلكتروني صالح.',
    'ends_with' => 'يجب أن تنتهي السمة :attribute بواحد من القيم التالية: :values.',
    'exists' => 'السمة :attribute المختارة غير صالحة.',
    'file' => 'يجب أن تكون السمة :attribute ملفاً.',
    'filled' => 'يجب أن يكون حقل السمة :attribute لديه قيمة.',
    'gt' => [
        'numeric' => 'يجب أن تكون السمة :attribute أكبر من :value.',
        'file' => 'يجب أن تكون السمة :attribute أكبر من :value كيلوبايت.',
        'tring' => 'يجب أن تكون السمة :attribute أكبر من :value حرفاً.',
        'array' => 'يجب أن تحتوي السمة :attribute على أكثر من :value عناصر.',
    ],
    'gte' => [
        'numeric' => 'يجب أن تكون السمة :attribute أكبر من أو مساوية ل :value.',
        'file' => 'يجب أن تكون السمة :attribute أكبر من أو مساوية ل :value كيلوبايت.',
        'tring' => 'يجب أن تكون السمة :attribute أكبر من أو مساوية ل :value حرفاً.',
        'array' => 'يجب أن تحتوي السمة :attribute على :value عناصر أو أكثر.',
    ],
    'image' => 'يجب أن تكون السمة :attribute صورة.',
    'in' => 'السمة :attribute المختارة غير صالحة.',
    'in_array' => 'حقل السمة :attribute لا يوجد في :other.',
    'integer' => 'يجب أن تكون السمة :attribute عدداً صحيحاً.',
    'ip' => 'يجب أن تكون السمة :attribute عنوان IP صالح.',
    'ipv4' => 'يجب أن تكون السمة :attribute عنوان IPv4 صالح.',
    'ipv6' => 'يجب أن تكون السمة :attribute عنوان IPv6 صالح.',
    'json' => 'يجب أن تكون السمة :attribute سلسلة JSON صالحة.',
    'lt' => [
        'numeric' => 'يجب أن تكون السمة :attribute أصغر من :value.',
        'file' => 'يجب أن تكون السمة :attribute أصغر من :value كيلوبايت.',
        'tring' => 'يجب أن تكون السمة :attribute أصغر من :value حرفاً.',
        'array' => 'يجب أن تحتوي السمة :attribute على أقل من :value عناصر.',
    ],
    'lte' => [
        'numeric' => 'يجب أن تكون السمة :attribute أصغر من أو مساوية ل :value.',
        'file' => 'يجب أن تكون السمة :attribute أصغر من أو مساوية ل :value كيلوبايت.',
        'tring' => 'يجب أن تكون السمة :attribute أصغر من أو مساوية ل :value حرفاً.',
        'array' => 'يجب أن لا تحتوي السمة :attribute على أكثر من :value عناصر.',
    ],
    'max' => [
        'numeric' => 'لا يجب أن تكون السمة :attribute أكبر من :max.',
        'file' => 'لا يجب أن تكون السمة :attribute أكبر من :max كيلوبايت.',
        'tring' => 'لا يجب أن تكون السمة :attribute أكبر من :max حرفاً.',
        'array' => 'لا يجب أن تحتوي السمة :attribute على أكثر من :max عناصر.',
    ],
    'mimes' => 'يجب أن تكون السمة :attribute ملفاً من نوع: :values.',
    'mimetypes' => 'يجب أن تكون السمة :attribute ملفاً من نوع: :values.',
    'min' => [
        'numeric' => 'يجب أن تكون السمة :attribute على الأقل :min.',
        'file' => 'يجب أن تكون السمة :attribute على الأقل :min كيلوبايت.',
        'tring' => 'يجب أن تكون السمة :attribute على الأقل :min حرفاً.',
        'array' => 'يجب أن تحتوي السمة :attribute على على الأقل :min عناصر.',
    ],
    'multiple_of' => 'يجب أن تكون السمة :attribute عدداً من العديد من :value.',
    'not_in' => 'السمة :attribute المختارة غير صالحة.',
    'not_regex' => 'صيغة السمة :attribute غير صالحة.',
    'numeric' => 'يجب أن تكون السمة :attribute رقماً.',
    'password' => 'كلمة المرور غير صحيحة.',
    'present' => 'يجب أن يكون حقل السمة :attribute موجوداً.',
    'regex' => 'صيغة السمة :attribute غير صالحة.',
    'required' => 'يجب إدخال حقل السمة :attribute.',
    'required_if' => 'يجب إدخال حقل السمة :attribute عندما يكون :other بقيمة :value.',
    'required_unless' => ':حقل اللازم إلا عندما يكون :other في :values.',
    'required_with' => ':حقل اللازم عندما يكون :values موجودا.',
    'required_with_all' => ':حقل اللازم عندما يكون كل من :values موجودا.',
    'required_without' => ':حقل اللازم عندما لا يكون :values موجودا.',
    'required_without_all' => ':حقل اللازم عندما لا يكون أي من :values موجودا.',
    'prohibited' => ':حقل اللازم ممنوع.',
    'prohibited_if' => ':حقل اللازم ممنوع عندما يكون :other هو :value.',
    'prohibited_unless' => ':حقل اللازم ممنوع إلا عندما يكون :other في :values.',
    'same' => ':حقل اللازم و :other يجب أن يتطابقا.',
    'size' => [
        'numeric' => ':حقل اللازم يجب أن يكون :size.',
        'file' => ':حقل اللازم يجب أن يكون :size كيلوبايت.',
        'tring' => ':حقل اللازم يجب أن يكون :size حرفا.',
        'array' => ':حقل اللازم يجب أن يحتوي على :size عناصر.',
    ],
    'starts_with' => ':حقل اللازم يجب أن يبدأ بأحد ما يلي: :values.',
    'string' => ':حقل اللازم يجب أن يكون نصا.',
    'timezone' => ':حقل اللازم يجب أن يكون منطقة صالحة.',
    'unique' => ':حقل اللازم تم أخذه بالفعل.',
    'uploaded' => ':حقل اللازم فشل في التحميل.',
    'url' => ':حقل اللازم تنسيق غير صالح.',
    'uuid' => ':حقل اللازم يجب أن يكون UUID صالحا.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
