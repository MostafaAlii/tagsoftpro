<?php

return [
    // Titles
    'permission_groups' => 'Permission Groups',
    'permission_group' => 'Permission Group',
    'create' => 'Add New Permission Group',
    'edit' => 'Edit Permission Group',
    'delete' => 'Delete Permission Group',

    // Fields
    'name' => 'Name',
    'description' => 'Description',
    'icon' => 'Icon',
    'icon_helper' => 'Example: ti ti-users (from Tabler Icons library)',
    'status' => 'Status',
    'company' => 'Company',

    // Status
    'status_active' => 'Active',
    'status_inactive' => 'Inactive',

    // Messages
    'created_successfully' => 'Permission group created successfully',
    'updated_successfully' => 'Permission group updated successfully',
    'deleted_successfully' => 'Permission group deleted successfully',
    'status_updated' => 'Permission group status updated successfully',
    'delete_confirm' => 'Are you sure you want to delete permission group ":name"?',

    // Actions
    'add_new' => 'Add Permission Group',
    'edit_action' => 'Edit',
    'delete_action' => 'Delete',

    // Validation
    'validation' => [
        'name_required' => 'Permission group name is required',
        'name_max' => 'Permission group name must not exceed 255 characters',
        'status_required' => 'Permission group status is required',
        'status_in' => 'Invalid permission group status',
    ],

    'show_trashed' => 'Show Trashed',
    'show_active' => 'Show Active',
    'restore' => 'Restore',
    'force_delete' => 'Force Delete',
    'restored_successfully' => 'Permission group restored successfully',
    'permanently_deleted' => 'Permission group permanently deleted',
    'restore_confirm' => 'Are you sure you want to restore this group?',
    'force_delete_confirm' => 'Warning! This action cannot be undone. Are you sure you want to permanently delete?',

    'bulk_actions' => 'Bulk Actions',
    'bulk_change_status' => 'Change Status',
    'bulk_status_confirm' => 'Select the new status for selected groups',
    'bulk_delete_confirm' => 'Are you sure you want to delete selected groups?',
    'bulk_select_at_least_one' => 'Please select at least one group',
    'bulk_status_updated' => 'Group statuses updated successfully',
    'bulk_deleted' => 'Selected groups deleted successfully',
    'select_status' => 'Select Status',
    'bulk_restore' => 'Restore Selected',
    'bulk_force_delete' => 'Force Delete Selected',
    'bulk_restore_confirm' => 'Are you sure you want to restore selected groups?',
    'bulk_force_delete_confirm' => 'Are you sure you want to permanently delete selected groups?',
    'bulk_restored_successfully' => 'Selected groups restored successfully',
    'bulk_force_deleted_successfully' => 'Selected groups permanently deleted',
];