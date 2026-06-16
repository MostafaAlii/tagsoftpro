<?php

return [
    // Titles
    'plans' => 'الخطط',
    'plan' => 'خطة',
    'create' => 'إضافة خطة جديدة',
    'edit' => 'تعديل الخطة',
    'delete' => 'حذف الخطة',

    // Fields
    'name' => 'اسم الخطة',
    'description' => 'الوصف',
    'price' => 'السعر',
    'billing_cycle' => 'دورة الفوترة',
    'billing_monthly' => 'شهري',
    'billing_yearly' => 'سنوي',
    'status' => 'الحالة',
    'company' => 'الشركة',
    'date' => 'التاريخ',

    // Messages
    'created_successfully' => 'تم إنشاء الخطة بنجاح',
    'updated_successfully' => 'تم تعديل الخطة بنجاح',
    'deleted_successfully' => 'تم حذف الخطة بنجاح',
    'status_updated' => 'تم تحديث حالة الخطة بنجاح',
    'billing_cycle_updated' => 'تم تحديث دورة الفوترة بنجاح',
    'delete_confirm' => 'هل أنت متأكد من حذف الخطة ":name"؟',

    // Actions
    'add_new' => 'إضافة خطة',
    'edit_action' => 'تعديل',
    'delete_action' => 'حذف',

    // Validation
    'validation' => [
        'name_required' => 'اسم الخطة باللغة :locale مطلوب',
        'price_required' => 'السعر مطلوب',
        'price_numeric' => 'السعر يجب أن يكون رقماً',
        'price_min' => 'السعر يجب أن يكون أكبر من أو يساوي 0',
        'billing_cycle_required' => 'دورة الفوترة مطلوبة',
        'billing_cycle_in' => 'دورة الفوترة غير صحيحة',
    ],

    'manage_features' => 'إدارة المميزات',
    'plan_features' => 'مميزات الباقة',
    'select_features_for_plan' => 'اختر المميزات التي تريد إضافتها للباقة مع تحديد الحد الأقصى لكل ميزة',
    'limit' => 'الحد الأقصى',
    'features_updated_successfully' => 'تم تحديث مميزات الباقة بنجاح',
    'select_all' => 'تحديد الكل',
    'no_features' => 'لا توجد مميزات متاحة',
];