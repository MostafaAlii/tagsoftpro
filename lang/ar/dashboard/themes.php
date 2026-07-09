<?php

return [
    // Titles
    'themes' => 'القوالب',
    'theme' => 'قالب',
    'create' => 'إضافة قالب جديد',
    'edit' => 'تعديل القالب',
    'delete' => 'حذف القالب',

    // Fields
    'name' => 'اسم القالب',
    'code' => 'الكود',
    'description' => 'الوصف',
    'is_active' => 'الحالة',
    'is_default' => 'افتراضي',
    'paid_type' => 'النوع',
    'paid_type_free' => 'مجاني',
    'paid_type_paid' => 'مدفوع',
    'price' => 'السعر',
    'company' => 'الشركة',
    'default' => 'افتراضي',
    'normal' => 'عادي',
    'free' => 'مجاني',

    // Messages
    'created_successfully' => 'تم إنشاء القالب بنجاح',
    'updated_successfully' => 'تم تعديل القالب بنجاح',
    'deleted_successfully' => 'تم حذف القالب بنجاح',
    'status_updated' => 'تم تحديث حالة القالب بنجاح',
    'cannot_deactivate_default' => 'لا يمكن تعطيل القالب الافتراضي',
    'cannot_delete_default' => 'لا يمكن حذف القالب الافتراضي',
    'delete_confirm' => 'هل أنت متأكد من حذف القالب ":name"؟',

    // Actions
    'add_new' => 'إضافة قالب',
    'edit_action' => 'تعديل',
    'delete_action' => 'حذف',

    'already_default' => 'هذا القالب هو الافتراضي بالفعل',
    'default_updated' => 'تم تحديث القالب الافتراضي بنجاح',
    'project_types_config' => 'اعدادات انواع المشاريع',
    'select_company' => 'اختار الشركه',
    'active'                            =>                      'مفعل',
    'default_status' => 'الافتراضى',
    'active_status' => 'التفعيل',
    'default_status_for' => 'الحالة الافتراضية لـ',
    'active_status_for' => 'حالة التفعيل لـ',

    // Validation
    'validation' => [
        'name_required' => 'اسم القالب مطلوب',
        'code_required' => 'كود القالب مطلوب',
        'code_unique' => 'كود القالب مستخدم بالفعل',
        'paid_type_required' => 'نوع القالب مطلوب',
        'paid_type_in' => 'نوع القالب غير صحيح',
        'price_required_if_paid' => 'السعر مطلوب عند اختيار نوع مدفوع',
        'price_numeric' => 'السعر يجب أن يكون رقماً',
        'price_min' => 'السعر يجب أن يكون أكبر من أو يساوي 0',
    ],
];