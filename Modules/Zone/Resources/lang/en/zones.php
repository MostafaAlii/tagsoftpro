<?php

return [
    // Titles
    'zones' => 'Zones',
    'zone' => 'Zone',
    'create' => 'Add Zone',
    'edit' => 'Edit Zone',
    'delete' => 'Delete Zone',
    'delete_confirm' => 'Are you sure you want to delete zone ":name"?',

    // Messages
    'created_successfully' => 'Zone created successfully',
    'updated_successfully' => 'Zone updated successfully',
    'deleted_successfully' => 'Zone deleted successfully',
    'restored_successfully' => 'Zone restored successfully',
    'permanently_deleted' => 'Zone permanently deleted',
    'status_updated' => 'Status updated successfully',
    'error_occurred' => 'An error occurred',

    // Fields
    'name' => 'Name',
    'key' => 'Key',
    'key_placeholder' => 'Zone key (optional)',
    'key_helper' => 'Will be generated automatically if left empty',
    'description' => 'Description',
    'status' => 'Status',
    'status_active' => 'Active',
    'status_inactive' => 'Inactive',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',
    'actions' => 'Actions',

    // Bulk Actions
    'bulk_actions' => 'Bulk Actions',
    'bulk_change_status' => 'Change Status',
    'bulk_delete' => 'Delete',
    'bulk_restore' => 'Restore',
    'bulk_force_delete' => 'Force Delete',
    'bulk_select_at_least_one' => 'Please select at least one zone',
    'bulk_status_confirm' => 'Are you sure you want to change status of selected zones?',
    'bulk_delete_confirm' => 'Are you sure you want to delete selected zones?',
    'bulk_restore_confirm' => 'Are you sure you want to restore selected zones?',
    'bulk_force_delete_confirm' => 'Warning! This action cannot be undone. Are you sure?',
    'bulk_deleted_successfully' => 'Selected zones deleted successfully',
    'bulk_restored_successfully' => 'Selected zones restored successfully',
    'bulk_force_deleted_successfully' => 'Selected zones permanently deleted',
    'bulk_status_updated_successfully' => 'Status of selected zones updated successfully',
    'select_status' => 'Select Status',
    'delete_selected' => 'Delete Selected',
    'invalid_action' => 'Invalid action',

    // Confirm
    'confirm' => 'Confirm',
    'cancel' => 'Cancel',
    'save' => 'Save',
    'loading' => 'Loading...',
    'all' => 'All',

    // Trashed
    'show_trashed' => 'Show Trashed',
    'show_active' => 'Show Active',
    'restore' => 'Restore',
    'force_delete' => 'Force Delete',
    'restore_confirm' => 'Are you sure you want to restore this zone?',
    'force_delete_confirm' => 'Warning! This action cannot be undone. Are you sure?',

    // Validation
    'validation' => [
        'key_unique' => 'The key has already been taken',
        'status_required' => 'Status is required',
        'locales_required' => 'Translation is required',
        'name_required' => 'Name is required',
    ],

    // Dashboard
    'dashboard' => 'Dashboard',
];