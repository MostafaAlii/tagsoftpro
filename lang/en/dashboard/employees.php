<?php

return [
    // Titles
    'employees' => 'Employees',
    'employee' => 'Employee',
    'create' => 'Add New Employee',
    'edit' => 'Edit Employee',
    'delete' => 'Delete Employee',

    // Fields
    'name' => 'Name',
    'email' => 'Email',
    'phone' => 'Phone',
    'status' => 'Status',
    'type' => 'Type',
    'password' => 'Password',
    'date' => 'Date',
    'company' => 'Company',
    'department' => 'Department',
    'select_department' => 'Select Department...',
    'leave_blank_to_keep' => 'Leave blank to keep current password',

    // Status
    'status_active' => 'Active',
    'status_inactive' => 'Inactive',
    'status_on_leave' => 'On Leave',
    'status_terminated' => 'Terminated',

    // Types
    'type_full_time' => 'Full Time',
    'type_part_time' => 'Part Time',
    'type_contractor' => 'Contractor',
    'type_intern' => 'Intern',
    'type_remote' => 'Remote',

    // Messages
    'created_successfully' => 'Employee created successfully',
    'updated_successfully' => 'Employee updated successfully',
    'deleted_successfully' => 'Employee deleted successfully',
    'status_updated' => 'Employee status updated successfully',
    'delete_confirm' => 'Are you sure you want to delete employee ":name"?',

    // Actions
    'add_new' => 'Add Employee',
    'edit_action' => 'Edit',
    'delete_action' => 'Delete',

    // Validation
    'validation' => [
        'name_required' => 'Employee name is required',
        'email_required' => 'Email is required',
        'email_unique' => 'Email is already used',
        'status_required' => 'Employee status is required',
        'status_in' => 'Invalid employee status',
        'type_required' => 'Employee type is required',
        'type_in' => 'Invalid employee type',
        'password_required' => 'Password is required',
        'password_min' => 'Password must be at least 8 characters',
    ],
    'avatar' => 'Profile Picture',
    'no_avatar' => 'No image to display',
    'show_trashed' => 'Show Trashed',
    'show_active' => 'Show Active',

    'restore' => 'Restore',
    'force_delete' => 'Force Delete',
    'restored_successfully' => 'Employee restored successfully',
    'permanently_deleted' => 'Employee permanently deleted',
    'restore_confirm' => 'Are you sure you want to restore this employee?',
    'force_delete_confirm' => 'Warning! This action cannot be undone. Are you sure you want to permanently delete?',
    'bulk_actions' => 'Bulk Actions',
    'bulk_change_status' => 'Change Status',
    'selected_employees' => 'Selected Employees',
    'bulk_activate' => 'Activate',
    'bulk_deactivate' => 'Deactivate',
    'bulk_on_leave' => 'Set On Leave',
    'bulk_terminate' => 'Terminate',
    'bulk_activate_confirm' => 'Are you sure you want to activate selected employees?',
    'bulk_deactivate_confirm' => 'Are you sure you want to deactivate selected employees?',
    'bulk_on_leave_confirm' => 'Are you sure you want to put selected employees on leave?',
    'bulk_terminate_confirm' => 'Are you sure you want to terminate selected employees?',

    'bulk_status_confirm' => 'Select the new status for selected employees',
    'bulk_delete_confirm' => 'Are you sure you want to delete selected employees?',
    'bulk_select_at_least_one' => 'Please select at least one employee',
    'bulk_status_updated' => 'Employee statuses updated successfully',
    'bulk_deleted' => 'Selected employees deleted successfully',
    'select_status' => 'Select Status',
    'bulk_restore' => 'Restore Selected',
    'bulk_force_delete' => 'Force Delete Selected',
    'bulk_restore_confirm' => 'Are you sure you want to restore selected employees?',
    'bulk_force_delete_confirm' => 'Are you sure you want to permanently delete selected employees?',
    'bulk_restored' => 'Selected employees restored successfully',
    'bulk_restored_successfully' => 'Selected employees restored successfully',
    'bulk_force_deleted' => 'Selected employees permanently deleted',
    'bulk_force_deleted_successfully' => 'Selected employees permanently deleted',
    'select_company' => 'Select Company',
];