<?php

return [
    // Titles
    'modules' => 'الوحدات',
    'module' => 'وحدة',
    'create' => 'إضافة وحدة جديدة',
    'edit' => 'تعديل الوحدة',
    'delete' => 'حذف الوحدة',

    // Fields
    'name' => 'اسم الوحدة',
    'description' => 'الوصف',
    'status' => 'الحالة',
    'company' => 'الشركة',

    'project_type' => 'نوع المشروع',
    'project_types' => 'نوع فئه المشروع',
    'select_project_type' => 'اختر نوع المشروع...',
    'select_company' =>     'اختر الشركه',
    'project_types_for' => 'أنواع المشاريع للوحدة',
    'no_project_types' => 'لا توجد أنواع مشاريع مرتبطة بهذه الوحدة',
    'close' => 'إغلاق',

    // Messages
    'created_successfully' => 'تم إنشاء الوحدة بنجاح',
    'updated_successfully' => 'تم تعديل الوحدة بنجاح',
    'deleted_successfully' => 'تم حذف الوحدة بنجاح',
    'status_updated' => 'تم تحديث حالة الوحدة بنجاح',
    'delete_confirm' => 'هل أنت متأكد من حذف الوحدة ":name"؟',

    // Actions
    'add_new' => 'إضافة وحدة',
    'edit_action' => 'تعديل',
    'delete_action' => 'حذف',

    // Validation
    'validation' => [
        'name_required' => 'اسم الوحدة باللغة :locale مطلوب',
        'name_string' => 'اسم الوحدة باللغة :locale يجب أن يكون نصاً',
        'name_max' => 'اسم الوحدة باللغة :locale يجب ألا يزيد عن :max حرف',
        'project_type_exists' => 'نوع المشروع المحدد غير موجود',
    ],
];