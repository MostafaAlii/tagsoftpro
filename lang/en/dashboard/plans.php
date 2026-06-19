<?php
return [
    // Titles
    'plans'                          => 'Plans',
    'plan'                           => 'Plan',
    'create'                         => 'Add New Plan',
    'edit'                           => 'Edit Plan',
    'delete'                         => 'Delete Plan',
    // Fields
    'name'                           => 'Plan Name',
    'description'                    => 'Description',
    'price'                          => 'Price',
    'billing_cycle'                  => 'Billing Cycle',
    'billing_monthly'                => 'Monthly',
    'billing_yearly'                 => 'Yearly',
    'status'                         => 'Status',
    'company'                        => 'Company',
    'date'                           => 'Date',
    // Messages
    'created_successfully'           => 'Plan created successfully',
    'updated_successfully'           => 'Plan updated successfully',
    'deleted_successfully'           => 'Plan deleted successfully',
    'status_updated'                 => 'Plan status updated successfully',
    'billing_cycle_updated'          => 'Billing cycle updated successfully',
    'delete_confirm'                 => 'Are you sure you want to delete the plan ":name"?',
    // Actions
    'add_new'                        => 'Add Plan',
    'edit_action'                    => 'Edit',
    'delete_action'                  => 'Delete',
    // Validation
    'validation' => [
        'name_required'              => 'Plan name in :locale is required',
        'price_required'             => 'Price is required',
        'price_numeric'              => 'Price must be a number',
        'price_min'                  => 'Price must be greater than or equal to 0',
        'billing_cycle_required'     => 'Billing cycle is required',
        'billing_cycle_in'           => 'Invalid billing cycle',
    ],
    'manage_features'                => 'Manage Features',
    'plan_features'                  => 'Plan Features',
    'select_features_for_plan'       => 'Select the features you want to add to the plan and set the maximum limit for each',
    'limit'                          => 'Limit',
    'features_updated_successfully'  => 'Plan features updated successfully',
    'select_all'                     => 'Select All',
    'no_features'                    => 'No features available',
];
