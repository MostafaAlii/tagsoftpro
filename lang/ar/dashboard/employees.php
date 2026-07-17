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
    'leave_blank_to_keep' => 'اتركه فارغًا للاحتفاظ بكلمة المرور الحالية',

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
    'show_trashed' => 'عرض المهملات',
    'show_active' => 'عرض النشطين',

    'restore' => 'استعادة',
    'force_delete' => 'حذف نهائي',
    'restored_successfully' => 'تم استعادة الموظف بنجاح',
    'permanently_deleted' => 'تم حذف الموظف نهائياً',
    'restore_confirm' => 'هل أنت متأكد من استعادة هذا الموظف؟',
    'force_delete_confirm' => 'تحذير! هذا الإجراء لا يمكن التراجع عنه. هل أنت متأكد من الحذف النهائي؟',
    'bulk_actions' => 'اجرائات',
    'bulk_change_status' => 'تغيير الحالة',
    // في employees.php
    'selected_employees' => 'الموظفين المختارين',
    'bulk_activate' => 'تفعيل',
    'bulk_deactivate' => 'تعطيل',
    'bulk_on_leave' => 'إجازة',
    'bulk_terminate' => 'إنهاء الخدمة',
    'bulk_activate_confirm' => 'هل أنت متأكد من تفعيل الموظفين المختارين؟',
    'bulk_deactivate_confirm' => 'هل أنت متأكد من تعطيل الموظفين المختارين؟',
    'bulk_on_leave_confirm' => 'هل أنت متأكد من وضع الموظفين المختارين في إجازة؟',
    'bulk_terminate_confirm' => 'هل أنت متأكد من إنهاء خدمة الموظفين المختارين؟',

    'bulk_status_confirm' => 'اختر الحالة الجديدة للموظفين المختارين',
    'bulk_delete_confirm' => 'هل أنت متأكد من حذف الموظفين المختارين نهائياً؟',
    'bulk_select_at_least_one' => 'يرجى تحديد موظف واحد على الأقل',
    'bulk_status_updated' => 'تم تحديث حالة الموظفين بنجاح',
    'bulk_deleted' => 'تم حذف الموظفين المختارين بنجاح',
    'select_status' => 'اختر الحالة',
    'bulk_restore' => 'استعادة المحددين',
    'bulk_force_delete' => 'حذف نهائي للمحددين',
    'bulk_restore_confirm' => 'هل أنت متأكد من استعادة الموظفين المختارين؟',
    'bulk_force_delete_confirm' => 'هل أنت متأكد من الحذف النهائي للموظفين المختارين؟',
    'bulk_restored' => 'تم استعادة الموظفين المختارين بنجاح',
    'bulk_restored_successfully' => 'تم استعادة الموظفين المختارين بنجاح',
    'bulk_force_deleted' => 'تم حذف الموظفين المختارين نهائياً',
    'bulk_force_deleted_successfully' => 'تم حذف الموظفين المختارين نهائياً',
    'select_company' => 'اختار الشركه',
];