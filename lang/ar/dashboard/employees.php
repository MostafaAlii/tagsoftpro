<?php

return [
    // Titles
    'employees' => 'الموظفين',
    'employee' => 'موظف',
    'create' => 'إضافة موظف جديد',
    'edit' => 'تعديل الموظف',
    'delete' => 'حذف الموظف',

    // Fields
    'name' => 'الاسم',
    'email' => 'البريد الإلكتروني',
    'phone' => 'الهاتف',
    'status' => 'الحالة',
    'type' => 'النوع',
    'password' => 'كلمة المرور',
    'date' => 'التاريخ',
    'company' => 'الشركة',
    'department' => 'الإدارة',
    'select_department' => 'اختر الإدارة...',

    // Status
    'status_active' => 'نشط',
    'status_inactive' => 'غير نشط',
    'status_on_leave' => 'في إجازة',
    'status_terminated' => 'منتهي الخدمة',

    // Types
    'type_full_time' => 'دوام كامل',
    'type_part_time' => 'دوام جزئي',
    'type_contractor' => 'متعاقد',
    'type_intern' => 'متدرب',
    'type_remote' => 'عن بعد',

    // Messages
    'created_successfully' => 'تم إنشاء الموظف بنجاح',
    'updated_successfully' => 'تم تعديل الموظف بنجاح',
    'deleted_successfully' => 'تم حذف الموظف بنجاح',
    'status_updated' => 'تم تحديث حالة الموظف بنجاح',
    'delete_confirm' => 'هل أنت متأكد من حذف الموظف ":name"؟',

    // Actions
    'add_new' => 'إضافة موظف',
    'edit_action' => 'تعديل',
    'delete_action' => 'حذف',

    // Validation
    'validation' => [
        'name_required' => 'اسم الموظف مطلوب',
        'email_required' => 'البريد الإلكتروني مطلوب',
        'email_unique' => 'البريد الإلكتروني مستخدم بالفعل',
        'status_required' => 'حالة الموظف مطلوبة',
        'status_in' => 'حالة الموظف غير صحيحة',
        'type_required' => 'نوع الموظف مطلوب',
        'type_in' => 'نوع الموظف غير صحيح',
        'password_required' => 'كلمة المرور مطلوبة',
        'password_min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
    ],
    'avatar' => 'الصوره الشخصيه',
    'no_avatar' => 'لا يوجد صوره لعرضها',
];