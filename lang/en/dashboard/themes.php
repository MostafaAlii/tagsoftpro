<?php

return [
    // Titles
    'themes' => 'Themes',
    'theme' => 'Theme',
    'create' => 'Add New Theme',
    'edit' => 'Edit Theme',
    'delete' => 'Delete Theme',

    // Fields
    'name' => 'Theme Name',
    'code' => 'Code',
    'description' => 'Description',
    'is_active' => 'Status',
    'is_default' => 'Default',
    'paid_type' => 'Type',
    'paid_type_free' => 'Free',
    'paid_type_paid' => 'Paid',
    'price' => 'Price',
    'company' => 'Company',
    'default' => 'Default',
    'normal' => 'Normal',
    'free' => 'Free',

    // Messages
    'created_successfully' => 'Theme created successfully',
    'updated_successfully' => 'Theme updated successfully',
    'deleted_successfully' => 'Theme deleted successfully',
    'status_updated' => 'Theme status updated successfully',
    'cannot_deactivate_default' => 'Cannot deactivate the default theme',
    'cannot_delete_default' => 'Cannot delete the default theme',
    'delete_confirm' => 'Are you sure you want to delete the theme ":name"?',

    // Actions
    'add_new' => 'Add Theme',
    'edit_action' => 'Edit',
    'delete_action' => 'Delete',

    'already_default' => 'This theme is already the default',
    'default_updated' => 'Default theme updated successfully',
    'project_types_config' => 'Project Types Settings',
    'select_company' => 'Select Company',
    'active' => 'Active',
    'default_status' => 'Default Status',
    'active_status' => 'Active Status',
    'default_status_for' => 'Default Status for',
    'active_status_for' => 'Active Status for',

    // Validation
    'validation' => [
        'name_required' => 'Theme name is required',
        'code_required' => 'Theme code is required',
        'code_unique' => 'Theme code is already in use',
        'paid_type_required' => 'Theme type is required',
        'paid_type_in' => 'Invalid theme type',
        'price_required_if_paid' => 'Price is required when selecting paid type',
        'price_numeric' => 'Price must be a number',
        'price_min' => 'Price must be greater than or equal to 0',
    ],
];