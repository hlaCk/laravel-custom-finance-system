<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages.
    |
    */
    'The given data was invalid.' => 'البيانات غير صالحة',
    'accepted' => 'يجب قبول :attribute',
    'active_url' => ':attribute لا يُمثّل رابطًا صحيحًا',
    'after' => 'يجب على :attribute أن يكون تاريخًا لاحقًا للتاريخ :date.',
    'after_or_equal' => ':attribute يجب أن يكون تاريخاً لاحقاً أو مطابقاً للتاريخ :date.',
    'alpha' => 'يجب أن لا يحتوي :attribute سوى على حروف',
    'alpha_dash' => 'يجب أن لا يحتوي :attribute على حروف، أرقام ومطّات.',
    'alpha_num' => 'يجب أن يحتوي :attribute على حروفٍ وأرقامٍ فقط',
    'array' => 'يجب أن يكون :attribute ًمصفوفة',
    'before' => 'يجب على :attribute أن يكون تاريخًا سابقًا للتاريخ :date.',
    'before_or_equal' => ':attribute يجب أن يكون تاريخا سابقا أو مطابقا للتاريخ :date',
    'between' => [
        'numeric' => 'يجب أن تكون قيمة :attribute بين :min و :max.',
        'file' => 'يجب أن يكون حجم الملف :attribute بين :min و :max كيلوبايت.',
        'string' => 'يجب أن يكون عدد حروف النّص :attribute بين :min و :max',
        'array' => 'يجب أن يحتوي :attribute على عدد من العناصر بين :min و :max',
    ],
    'boolean' => 'يجب أن تكون قيمة :attribute إما true أو false ',
    'confirmed' => 'حقل التأكيد غير مُطابق للحقل :attribute',
    'date' => ':attribute ليس تاريخًا صحيحًا',
    'date_format' => 'لا يتوافق :attribute مع الشكل :format.',
    'different' => 'يجب أن يكون الحقلان :attribute و :other مُختلفان',
    'digits' => 'يجب أن يحتوي :attribute على :digits رقمًا/أرقام',
    'digits_between' => 'يجب أن يحتوي :attribute بين :min و :max رقمًا/أرقام ',
    'dimensions' => 'الـ :attribute يحتوي على أبعاد صورة غير صالحة.',
    'distinct' => 'للحقل :attribute قيمة مُكرّرة.',
    'email' => 'يجب أن يكون :attribute عنوان بريد إلكتروني صحيح البُنية',
    'exists' => 'القيمة المحددة :attribute غير موجودة',
    'file' => 'الـ :attribute يجب أن يكون ملفا.',
    'filled' => ':attribute إجباري',
    'image' => 'يجب أن يكون :attribute صورةً',
    'in' => ':attribute يحتوي على قيمة غير صالحة',
    'in_array' => ':attribute غير موجود في :other.',
    'integer' => 'يجب أن يكون :attribute عددًا صحيحًا',
    'ip' => 'يجب أن يكون :attribute عنوان IP صحيحًا',
    'ipv4' => 'يجب أن يكون :attribute عنوان IPv4 صحيحًا.',
    'ipv6' => 'يجب أن يكون :attribute عنوان IPv6 صحيحًا.',
    'json' => 'يجب أن يكون :attribute نصآ من نوع JSON.',
    'max' => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أصغر لـ :max.',
        'file' => 'يجب أن لا يتجاوز حجم الملف :attribute :max كيلوبايت',
        'string' => 'يجب أن لا يتجاوز طول النّص :attribute :max حروفٍ/حرفًا',
        'array' => 'يجب أن لا يحتوي :attribute على أكثر من :max عناصر/عنصر.',
    ],
    'mimes' => 'يجب أن يكون ملفًا من نوع : :values.',
    'mimetypes' => 'يجب أن يكون ملفًا من نوع : :values.',
    'min' => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أكبر لـ :min.',
        'file' => 'يجب أن يكون حجم الملف :attribute على الأقل :min كيلوبايت',
        'string' => 'يجب أن يكون طول النص :attribute على الأقل :min حروفٍ/حرفًا',
        'array' => 'يجب أن يحتوي :attribute على الأقل على :min عُنصرًا/عناصر',
    ],
    'not_in' => ':attribute لاغٍ',
    'numeric' => 'يجب على :attribute أن يكون رقمًا',
    'present' => 'يجب تقديم :attribute',
    'regex' => 'صيغة :attribute .غير صحيحة',
    'required' => ':attribute مطلوب.',
    'required_if' => ':attribute مطلوب في حال ما إذا كان :other يساوي :value.',
    'required_unless' => ':attribute مطلوب في حال ما لم يكن :other يساوي :values.',
    'required_with' => ':attribute مطلوب إذا توفّر :values.',
    'required_with_all' => ':attribute مطلوب إذا توفّر :values.',
    'required_without' => ':attribute مطلوب إذا لم يتوفّر :values.',
    'required_without_all' => ':attribute مطلوب إذا لم يتوفّر :values.',
    'same' => 'يجب أن يتطابق :attribute مع :other',
    'size' => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية لـ :size',
        'file' => 'يجب أن يكون حجم الملف :attribute :size كيلوبايت',
        'string' => 'يجب أن يحتوي النص :attribute على :size حروفٍ/حرفًا بالظبط',
        'array' => 'يجب أن يحتوي :attribute على :size عنصرٍ/عناصر بالظبط',
    ],
    'starts_with' => ':attribute يجب أن يبدأ بـ: :values',
    'string' => 'يجب أن يكون :attribute نصآ.',
    'timezone' => 'يجب أن يكون :attribute نطاقًا زمنيًا صحيحًا',
    'unique' => 'قيمة :attribute مُستخدمة من قبل',
    'uploaded' => 'فشل في تحميل الـ :attribute',
    'url' => 'صيغة الرابط :attribute غير صحيحة',
    'iban' => ':attribute يجب أن يكون رقم حساب مصرفي دوليًا صالحًا (IBAN)',
    'not_starts_with' => ' :attribute يجب أن لا يبدأ بـ  ',
    'max_deposit_order_amount' => 'تم تجاوز الحد',

    'start_with' => 'يجب أن يبدأ :attribute بـ :val',

    'gt' => [
        'numeric' => ':attribute يجب ان يكون اكبر من :value',
    ],
    'round_error'=>'حقل الجولة مطلوب',
    'horse_error'=>'حقل الحصان مطلوب',
    'jocky_error'=>'مطلوب حقل جوكي',
    'trainer_error'=>'حقل المدرب مطلوب',
    'prize_error'=>'حقل الجائزة مطلوب',
    'file_renamed_successfully'=>'تمت إعادة تسمية الملف بنجاح',
    'file_not_found'=>'لم يتم العثور على الملف',

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

    'ajaxmimetypes' => 'يجب أن يكون ملفًا من نوع : :values.',
    'transaction_order_verify_amount' => 'الرصيد لا يسمح',

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],

        'team.*.id' => [
            'required' => 'هذا الحقل لايمكن أن يكون فارغاً.',
        ],
        'videos.*.file' => [
            'required' => 'هذا الحقل لايمكن أن يكون فارغاً.',
        ],
        'images.*.file' => [
            'required' => 'هذا الحقل لايمكن أن يكون فارغاً.',
        ],
        'pdfs.*.file' => [
            'required' => 'هذا الحقل لايمكن أن يكون فارغاً.',
        ],
        'name.*' => [
            'required' => 'حقل الاسم مطلوب.',
        ],
        'type.*' => [
            'required' => 'حقل النوع مطلوب.',
        ],
        'title.*' => [
            'required' => 'مطلوب حقل العنوان.',
        ],
        'content.*' => [
            'required' => 'حقل المحتوى مطلوب.',
        ],
        'value.*' => [
            'required' => 'حقل القيمة مطلوب.',
        ],
        'description.*' => [
            'required' => 'حقل الوصف مطلوب.',
        ],
        'program_title.*' => [
            'required' => 'حقل عنوان البرنامج مطلوب.',
        ],
         'show.*' => [
            'required' => 'حقل العرض مطلوب.',
        ],
        'color.*' => [
            'required' => 'حقل اللون مطلوب.',
        ],
        'hometown.*' => [
            'required' => 'حقل المدينة الأصلية مطلوب.',
        ],


    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'name' => 'الاسم',
        'username' => 'اسم المُستخدم',
        'email' => 'البريد الالكتروني',
        'first_name' => 'الاسم الأول',
        'last_name' => 'اسم العائلة',
        'password' => 'كلمة السر',
        'password_confirmation' => 'تأكيد كلمة السر',
        'city' => 'المحافظة',
        'country' => 'الدولة',
        'address' => 'عنوان السكن',
        'phone' => 'الهاتف',
        'mobile' => 'الجوال',
        'age' => 'العمر',
        'sex' => 'الجنس',
        'gender' => 'النوع',
        'day' => 'اليوم',
        'month' => 'الشهر',
        'year' => 'السنة',
        'hour' => 'ساعة',
        'minute' => 'دقيقة',
        'second' => 'ثانية',
        'title' => 'العنوان',
        'content' => 'المُحتوى',
        'description' => 'الوصف',
        'excerpt' => 'المُلخص',
        'date' => 'التاريخ',
        'time' => 'الوقت',
        'available' => 'مُتاح',
        'size' => 'الحجم',

        'school_id' => 'المدرسة',
        'school_code' => 'الرقم الوزاري',
        'level_type' => 'المرحلة الدراسية',
        'passwordConfirm' => 'تأكيد كلمة المرور',
        'identity_num' => 'رقم الهوية',
        'user_type' => 'نوع الحساب',
        'educational_administration_id' => 'الادارة التعليمية',
        'office_id' => 'مكتب التربية',
        'images' => 'الصور',
        'videos' => 'الفيديوهات',
        'pdfs' => 'PDFs',
        'team' => 'الفريق',
        'minutes_count' => 'مدة الفعالية ( بالدقائق )',
        'report_type_id' => 'نوع الفعالية',
        'place' => 'المكان',

        'start_date' => 'تاريخ البداية',
        'end_date' => 'تاريخ النهاية',
        'school_year_id' => 'السنة الدراسية',
        'olympiad_id' => 'المسابقة',
        'stage_id' => 'المرحلة',
        'user_id' => 'المستخدم',
        'status' => 'الحالة',
        'ambitable_type' => 'نوع المنطقة',
        'ambitable_id' => 'تحديد المنطقة',
        'logo' => 'الشعار',
        'g-recaptcha-response' => 'يجب التاكيد انك لست روبوت',
        'companies_limit' => 'عدد الشركات المسموح به للطالب',
        'products_limit' => 'عدد المنتجات المسموح به للطالب',
        'user_in_report_limit' => 'عدد الفعاليات المسموح به للطالب',
        'student_school_num' => 'رقم الطالب بالمدرسة',
        'national_id' => 'رقم الهوية',
        'guardian_id' => 'id الاب',
        'student_id' => 'id الطالب',
        'valid_balance' => 'المبلغ',
        'amount' => 'المبلغ',
        'price' => 'السعر',
        'account_number' => 'رقم الحساب',
        'code' => 'رمز التوثيق',
        'supplier-classifications' => 'تصنيفات التاجر',
        'delivery_code' => 'كود التسليم'
    ],
    'maximum_file_size_2m' => '<span class="file_size_help_text">(الحد الأقصى لحجم الملف: 2 ميجا)</span>',
    'maximum_file_size_10m' => '<span class="file_size_help_text">(الحد الأقصى لحجم الملف: 10 ميجا)</span>'
];
