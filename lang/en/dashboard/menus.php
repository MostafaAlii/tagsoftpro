<?php

return [
    // Titles
    'menus' => 'Menus',
    'menu' => 'Menu',
    'create' => 'Add New Menu',
    'edit' => 'Edit Menu',
    'delete' => 'Delete Menu',

    // Fields
    'key' => 'Key',
    'name' => 'Name',
    'description' => 'Description',
    'route_prefix' => 'Route Prefix',
    'icon' => 'Icon',
    'icon_helper' => 'Example: ti ti-users (from Tabler Icons library)',
    'status' => 'Status',
    'company' => 'Company',

    // Status
    'status_active' => 'Active',
    'status_inactive' => 'Inactive',

    // Messages
    'created_successfully' => 'Menu created successfully',
    'updated_successfully' => 'Menu updated successfully',
    'deleted_successfully' => 'Menu deleted successfully',
    'status_updated' => 'Menu status updated successfully',
    'delete_confirm' => 'Are you sure you want to delete menu ":name"?',

    // Actions
    'add_new' => 'Add Menu',
    'edit_action' => 'Edit',
    'delete_action' => 'Delete',

    // Validation
    'validation' => [
        'key_required' => 'Menu key is required',
        'key_unique' => 'Menu key is already used',
        'key_regex' => 'Menu key must contain only letters, numbers and underscores',
        'name_required' => 'Menu name is required',
        'name_max' => 'Menu name must not exceed 255 characters',
        'status_required' => 'Menu status is required',
        'status_in' => 'Invalid menu status',
    ],

    'show_trashed' => 'Show Trashed',
    'show_active' => 'Show Active',
    'restore' => 'Restore',
    'force_delete' => 'Force Delete',
    'restored_successfully' => 'Menu restored successfully',
    'permanently_deleted' => 'Menu permanently deleted',
    'restore_confirm' => 'Are you sure you want to restore this menu?',
    'force_delete_confirm' => 'Warning! This action cannot be undone. Are you sure you want to permanently delete?',

    'bulk_actions' => 'Bulk Actions',
    'bulk_change_status' => 'Change Status',
    'bulk_status_confirm' => 'Select the new status for selected menus',
    'bulk_delete_confirm' => 'Are you sure you want to delete selected menus?',
    'bulk_select_at_least_one' => 'Please select at least one menu',
    'bulk_status_updated' => 'Menu statuses updated successfully',
    'bulk_deleted' => 'Selected menus deleted successfully',
    'select_status' => 'Select Status',
    'bulk_restore' => 'Restore Selected',
    'bulk_force_delete' => 'Force Delete Selected',
    'bulk_restore_confirm' => 'Are you sure you want to restore selected menus?',
    'bulk_force_delete_confirm' => 'Are you sure you want to permanently delete selected menus?',
    'bulk_restored_successfully' => 'Selected menus restored successfully',
    'bulk_force_deleted_successfully' => 'Selected menus permanently deleted',
];