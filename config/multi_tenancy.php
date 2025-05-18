<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tenant Model
    |--------------------------------------------------------------------------
    |
    | This is the model class that will be used to represent a tenant.
    | When the multi-tenancy system is fully implemented, this model
    | will store information about each tenant.
    |
    */
    'tenant_model' => 'App\Models\Tenant',

    /*
    |--------------------------------------------------------------------------
    | Default Tenant ID
    |--------------------------------------------------------------------------
    |
    | This is the default tenant ID used in single-tenant mode.
    | It's set to 1 for simplicity.
    |
    */
    'default_tenant_id' => 1,

    /*
    |--------------------------------------------------------------------------
    | Domain Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how tenants are identified via domain.
    | In SaaS mode, each tenant can have their own subdomain.
    |
    */
    'domain' => [
        'central' => env('APP_URL', 'http://aursuite.local'),
        'suffix' => env('TENANT_DOMAIN_SUFFIX', '.aursuite.local'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Tables
    |--------------------------------------------------------------------------
    |
    | List of tables that should be tenant-aware (have tenant_id column).
    | This can be used for automatic verification and data management.
    |
    */
    'tables' => [
        // الجداول الأساسية
        'users', 
        'roles',
        'permissions',
        'model_has_roles',
        'role_has_permissions',
        'accounts',
        'account_balances',
        'account_user',
        'branches',
        'currencies',
        'vouchers',
        'transactions',
        'invoices',
        'invoice_items',
        'items',
        'customers',
        'employees',
        'salaries',
        'salary_batches',
        'salary_payments',
        'journal_entries',
        'journal_entry_lines',
        'accounting_settings',
        'settings',
        
        // جداول إضافية
        'reports',
        'report_templates',
        'financial_periods',
        'fiscal_years',
        'invoice_payments',
        'languages',
        'tax_rates',
        'expense_categories',
        'expenses',
        'user_preferences',
        'notifications',
        'templates',
        'documents',
        'document_types',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Keys Prefix
    |--------------------------------------------------------------------------
    |
    | To avoid cache collisions between tenants, we prefix cache keys
    | with the tenant ID.
    |
    */
    'cache_prefix' => 'tenant_',
]; 