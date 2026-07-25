<?php

return [
    // Titles
    'menu_items' => 'عناصر القوائم',
    'menu_item' => 'عنصر قائمة',
    'create' => 'إضافة عنصر قائمة جديد',
    'edit' => 'تعديل عنصر القائمة',
    'delete' => 'حذف عنصر القائمة',

    // Fields
    'title' => 'العنوان',
    'description' => 'الوصف',
    'type' => 'النوع',
    'icon' => 'الأيقونة',
    'icon_helper' => 'مثال: ti ti-users (من مكتبة Tabler Icons)',
    'link_type' => 'نوع الرابط',
    'link' => 'الرابط',
    'route_name' => 'اسم المسار',
    'url' => 'الرابط',
    'target' => 'الهدف',
    'is_owner_only' => 'للمالك فقط',
    'permission_name' => 'اسم الصلاحية',
    'badge_text' => 'نص الشارة',
    'badge_color' => 'لون الشارة',
    'status' => 'الحالة',
    'visible_from' => 'مرئي من',
    'visible_until' => 'مرئي حتى',
    'company' => 'الشركة',

    // Types
    'type_link' => 'رابط',
    'type_dropdown' => 'قائمة منسدلة',
    'type_header' => 'رأس',
    'type_divider' => 'فاصل',

    // Link Types
    'link_type_route' => 'مسار (Route)',
    'link_type_url' => 'رابط (URL)',
    'link_type_none' => 'بدون رابط',

    // Target
    'target_self' => 'نفس النافذة',
    'target_blank' => 'نافذة جديدة',

    // Status
    'status_active' => 'نشط',
    'status_inactive' => 'غير نشط',

    // Messages
    'created_successfully' => 'تم إنشاء عنصر القائمة بنجاح',
    'updated_successfully' => 'تم تعديل عنصر القائمة بنجاح',
    'deleted_successfully' => 'تم حذف عنصر القائمة بنجاح',
    'status_updated' => 'تم تحديث حالة عنصر القائمة بنجاح',
    'delete_confirm' => 'هل أنت متأكد من حذف عنصر القائمة ":title"؟',

    // Actions
    'add_new' => 'إضافة عنصر قائمة',
    'edit_action' => 'تعديل',
    'delete_action' => 'حذف',

    // Validation
    'validation' => [
        'type_required' => 'نوع عنصر القائمة مطلوب',
        'type_in' => 'نوع عنصر القائمة غير صحيح',
        'title_required' => 'عنوان عنصر القائمة مطلوب',
        'title_max' => 'عنوان عنصر القائمة يجب ألا يزيد عن 255 حرف',
        'status_required' => 'حالة عنصر القائمة مطلوبة',
        'status_in' => 'حالة عنصر القائمة غير صحيحة',
        'visible_until_after' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء',
    ],

    'show_trashed' => 'عرض المهملات',
    'show_active' => 'عرض النشطين',
    'restore' => 'استعادة',
    'force_delete' => 'حذف نهائي',
    'restored_successfully' => 'تم استعادة عنصر القائمة بنجاح',
    'permanently_deleted' => 'تم حذف عنصر القائمة نهائياً',
    'restore_confirm' => 'هل أنت متأكد من استعادة هذا العنصر؟',
    'force_delete_confirm' => 'تحذير! هذا الإجراء لا يمكن التراجع عنه. هل أنت متأكد من الحذف النهائي؟',

    'bulk_actions' => 'إجراءات جماعية',
    'bulk_change_status' => 'تغيير الحالة',
    'bulk_status_confirm' => 'اختر الحالة الجديدة للعناصر المختارة',
    'bulk_delete_confirm' => 'هل أنت متأكد من حذف العناصر المختارة؟',
    'bulk_select_at_least_one' => 'يرجى تحديد عنصر واحد على الأقل',
    'bulk_status_updated' => 'تم تحديث حالة العناصر بنجاح',
    'bulk_deleted' => 'تم حذف العناصر المختارة بنجاح',
    'select_status' => 'اختر الحالة',
    'bulk_restore' => 'استعادة المحددين',
    'bulk_force_delete' => 'حذف نهائي للمحددين',
    'bulk_restore_confirm' => 'هل أنت متأكد من استعادة العناصر المختارة؟',
    'bulk_force_delete_confirm' => 'هل أنت متأكد من الحذف النهائي للعناصر المختارة؟',
    'bulk_restored_successfully' => 'تم استعادة العناصر المختارة بنجاح',
    'bulk_force_deleted_successfully' => 'تم حذف العناصر المختارة نهائياً',
];