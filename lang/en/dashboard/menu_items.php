<?php

return [
    // Titles
    'menu_items' => 'Menu Items',
    'menu_item' => 'Menu Item',
    'create' => 'Add New Menu Item',
    'edit' => 'Edit Menu Item',
    'delete' => 'Delete Menu Item',

    // Fields
    'title' => 'Title',
    'description' => 'Description',
    'type' => 'Type',
    'icon' => 'Icon',
    'icon_helper' => 'Example: ti ti-users (from Tabler Icons library)',
    'link_type' => 'Link Type',
    'link' => 'Link',
    'route_name' => 'Route Name',
    'url' => 'URL',
    'target' => 'Target',
    'is_owner_only' => 'Owner Only',
    'permission_name' => 'Permission Name',
    'badge_text' => 'Badge Text',
    'badge_color' => 'Badge Color',
    'status' => 'Status',
    'visible_from' => 'Visible From',
    'visible_until' => 'Visible Until',
    'company' => 'Company',

    // Types
    'type_link' => 'Link',
    'type_dropdown' => 'Dropdown',
    'type_header' => 'Header',
    'type_divider' => 'Divider',

    // Link Types
    'link_type_route' => 'Route',
    'link_type_url' => 'URL',
    'link_type_none' => 'None',

    // Target
    'target_self' => 'Same Window',
    'target_blank' => 'New Window',

    // Status
    'status_active' => 'Active',
    'status_inactive' => 'Inactive',

    // Messages
    'created_successfully' => 'Menu item created successfully',
    'updated_successfully' => 'Menu item updated successfully',
    'deleted_successfully' => 'Menu item deleted successfully',
    'status_updated' => 'Menu item status updated successfully',
    'delete_confirm' => 'Are you sure you want to delete menu item ":title"?',

    // Actions
    'add_new' => 'Add Menu Item',
    'edit_action' => 'Edit',
    'delete_action' => 'Delete',

    // Validation
    'validation' => [
        'type_required' => 'Menu item type is required',
        'type_in' => 'Invalid menu item type',
        'title_required' => 'Menu item title is required',
        'title_max' => 'Menu item title must not exceed 255 characters',
        'status_required' => 'Menu item status is required',
        'status_in' => 'Invalid menu item status',
        'visible_until_after' => 'End date must be after start date',
    ],

    'show_trashed' => 'Show Trashed',
    'show_active' => 'Show Active',
    'restore' => 'Restore',
    'force_delete' => 'Force Delete',
    'restored_successfully' => 'Menu item restored successfully',
    'permanently_deleted' => 'Menu item permanently deleted',
    'restore_confirm' => 'Are you sure you want to restore this item?',
    'force_delete_confirm' => 'Warning! This action cannot be undone. Are you sure you want to permanently delete?',

    'bulk_actions' => 'Bulk Actions',
    'bulk_change_status' => 'Change Status',
    'bulk_status_confirm' => 'Select the new status for selected items',
    'bulk_delete_confirm' => 'Are you sure you want to delete selected items?',
    'bulk_select_at_least_one' => 'Please select at least one item',
    'bulk_status_updated' => 'Item statuses updated successfully',
    'bulk_deleted' => 'Selected items deleted successfully',
    'select_status' => 'Select Status',
    'bulk_restore' => 'Restore Selected',
    'bulk_force_delete' => 'Force Delete Selected',
    'bulk_restore_confirm' => 'Are you sure you want to restore selected items?',
    'bulk_force_delete_confirm' => 'Are you sure you want to permanently delete selected items?',
    'bulk_restored_successfully' => 'Selected items restored successfully',
    'bulk_force_deleted_successfully' => 'Selected items permanently deleted',
];