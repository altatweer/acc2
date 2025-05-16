@if($isSuperAdmin || auth()->user()->can('view_transactions'))
<li class="nav-header">@lang('sidebar.transactions')</li>
<li class="nav-item">
    <a href="{{ route('transactions.index') }}" class="nav-link {{ Request::routeIs('transactions.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-exchange-alt"></i>
        <p>@lang('sidebar.transactions_management')</p>
    </a>
</li>
@endif

@if($isSuperAdmin || auth()->user()->can('view_currencies'))
<li class="nav-header">@lang('sidebar.currencies')</li>
<li class="nav-item">
    <a href="{{ route('currencies.index') }}" class="nav-link {{ Request::routeIs('currencies.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-coins"></i>
        <p>@lang('sidebar.currencies_management')</p>
    </a>
</li>
@endif

@if($isSuperAdmin || auth()->user()->can('view_invoices'))
<li class="nav-header">@lang('sidebar.invoices')</li>
<li class="nav-item has-treeview {{ Request::routeIs('invoices.*') || Request::routeIs('invoice-payments.*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ Request::routeIs('invoices.*') || Request::routeIs('invoice-payments.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-receipt"></i>
        <p>
            @lang('sidebar.invoices_management')
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('invoices.index') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>@lang('sidebar.invoices_list')</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('invoices.create') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>@lang('sidebar.new_invoice')</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('invoice-payments.create') }}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>@lang('sidebar.pay_invoice')</p>
            </a>
        </li>
    </ul>
</li>
@endif

@if($isSuperAdmin || auth()->user()->can('view_customers'))
<li class="nav-header">@lang('sidebar.customers')</li>
<li class="nav-item has-treeview {{ Request::routeIs('customers.*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ Request::routeIs('customers.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-users"></i>
        <p>@lang('sidebar.customers_management')<i class="right fas fa-angle-left"></i></p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('customers.index') }}" class="nav-link {{ Request::routeIs('customers.index') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>@lang('sidebar.customers_list')</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('customers.create') }}" class="nav-link {{ Request::routeIs('customers.create') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>@lang('sidebar.new_customer')</p>
            </a>
        </li>
    </ul>
</li>
@endif

@if($isSuperAdmin || auth()->user()->can('view_items'))
<li class="nav-header">@lang('sidebar.items')</li>
<li class="nav-item has-treeview {{ Request::routeIs('items.*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ Request::routeIs('items.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-box-open"></i>
        <p>@lang('sidebar.items_management')<i class="right fas fa-angle-left"></i></p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('items.index') }}" class="nav-link {{ Request::routeIs('items.index') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>@lang('sidebar.items_list')</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('items.create') }}" class="nav-link {{ Request::routeIs('items.create') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>@lang('sidebar.new_item')</p>
            </a>
        </li>
    </ul>
</li>
@endif

@if($isSuperAdmin || auth()->user()->can('view_journal_entries'))
<li class="nav-header">@lang('sidebar.accounting')</li>
<li class="nav-item">
    <a class="nav-link {{ Request::routeIs('journal-entries.*') ? 'active' : '' }}" href="{{ route('journal-entries.index') }}">
        <i class="nav-icon fas fa-book"></i>
        <p>@lang('sidebar.accounting_entries')</p>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ Request::routeIs('ledger.*') ? 'active' : '' }}" href="{{ route('ledger.index') }}">
        <i class="nav-icon fas fa-book-open"></i>
        <p>@lang('sidebar.ledger')</p>
    </a>
</li>
@endif

@if($isSuperAdmin || auth()->user()->can('view_employees') || auth()->user()->can('view_salaries') || auth()->user()->can('view_salary_payments') || auth()->user()->can('view_salary_batches'))
<li class="nav-header">@lang('sidebar.hr')</li>

@if($isSuperAdmin || auth()->user()->can('view_employees'))
<li class="nav-item">
    <a href="{{ route('employees.index') }}" class="nav-link {{ Request::routeIs('employees.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-user-tie"></i>
        <p>@lang('sidebar.employees')</p>
    </a>
</li>
@endif

@if($isSuperAdmin || auth()->user()->can('view_salaries'))
<li class="nav-item">
    <a href="{{ route('salaries.index') }}" class="nav-link {{ Request::routeIs('salaries.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-money-bill-wave"></i>
        <p>@lang('sidebar.salaries')</p>
    </a>
</li>
@endif

@if($isSuperAdmin || auth()->user()->can('view_salary_payments'))
<li class="nav-item">
    <a href="{{ route('salary-payments.index') }}" class="nav-link {{ Request::routeIs('salary-payments.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-money-check-alt"></i>
        <p>@lang('sidebar.salary_payments')</p>
    </a>
</li>
@endif

@if($isSuperAdmin || auth()->user()->can('view_salary_batches'))
<li class="nav-item">
    <a href="{{ route('salary-batches.index') }}" class="nav-link {{ Request::routeIs('salary-batches.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-file-invoice-dollar"></i>
        <p>@lang('sidebar.salary_sheets')</p>
    </a>
</li>
@endif
@endif

@if($isSuperAdmin || auth()->user()->can('view_roles') || auth()->user()->can('view_permissions') || auth()->user()->can('view_user_roles') || auth()->user()->can('view_users') || auth()->user()->can('manage_system_settings'))
<li class="nav-header">@lang('sidebar.system_settings')</li>

@if($isSuperAdmin || auth()->user()->can('view_roles'))
<li class="nav-item">
    <a href="{{ route('admin.roles.index') }}" class="nav-link {{ Request::routeIs('admin.roles.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-user-shield"></i>
        <p>@lang('sidebar.roles')</p>
    </a>
</li>
@endif

@if($isSuperAdmin || auth()->user()->can('view_permissions'))
<li class="nav-item">
    <a href="{{ route('admin.permissions.index') }}" class="nav-link {{ Request::routeIs('admin.permissions.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-key"></i>
        <p>@lang('sidebar.permissions')</p>
    </a>
</li>
@endif

@if($isSuperAdmin || auth()->user()->can('view_user_roles'))
<li class="nav-item">
    <a href="{{ route('admin.user-roles.index') }}" class="nav-link {{ Request::routeIs('admin.user-roles.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-users-cog"></i>
        <p>@lang('sidebar.user_roles')</p>
    </a>
</li>
@endif

@if($isSuperAdmin || auth()->user()->can('view_users'))
<li class="nav-item">
    <a href="{{ route('admin.users.index') }}" class="nav-link {{ Request::routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-users"></i>
        <p>@lang('sidebar.users')</p>
    </a>
</li>
@endif

@if($isSuperAdmin || auth()->user()->can('manage_system_settings'))
<li class="nav-item">
    <a href="{{ route('accounting-settings.edit') }}" class="nav-link {{ Request::routeIs('accounting-settings.edit') ? 'active' : '' }}">
        <i class="nav-icon fas fa-cogs"></i>
        <p>@lang('sidebar.accounting_settings')</p>
    </a>
</li>
@endif

@if($isSuperAdmin || auth()->user()->can('عرض الصلاحيات'))
<li class="nav-item">
    <a href="{{ route('settings.system.edit') }}" class="nav-link {{ Request::routeIs('settings.system.edit') ? 'active' : '' }}">
        <i class="nav-icon fas fa-cog"></i>
        <p>@lang('sidebar.system_settings_page')</p>
    </a>
</li>
@endif

@if($isSuperAdmin)
<li class="nav-item">
    <a href="{{ route('languages.index') }}" class="nav-link {{ Request::routeIs('languages.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-language"></i>
        <p>@lang('sidebar.languages_management')</p>
    </a>
</li>
@endif
@endif

@if($isSuperAdmin || auth()->user()->can('view_reports'))
<li class="nav-header">@lang('sidebar.reports')</li>
<li class="nav-item">
    <a class="nav-link {{ Request::routeIs('reports.trial-balance') ? 'active' : '' }}" href="{{ route('reports.trial-balance') }}">
        <i class="nav-icon fas fa-balance-scale"></i>
        <p>@lang('sidebar.trial_balance')</p>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ Request::routeIs('reports.balance-sheet') ? 'active' : '' }}" href="{{ route('reports.balance-sheet') }}">
        <i class="nav-icon fas fa-file-invoice-dollar"></i>
        <p>@lang('sidebar.balance_sheet')</p>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ Request::routeIs('reports.income-statement') ? 'active' : '' }}" href="{{ route('reports.income-statement') }}">
        <i class="nav-icon fas fa-chart-line"></i>
        <p>@lang('sidebar.income_statement')</p>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ Request::routeIs('reports.payroll') ? 'active' : '' }}" href="{{ route('reports.payroll') }}">
        <i class="nav-icon fas fa-money-check-alt"></i>
        <p>@lang('sidebar.payroll_report')</p>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ Request::routeIs('reports.expenses-revenues') ? 'active' : '' }}" href="{{ route('reports.expenses-revenues') }}">
        <i class="nav-icon fas fa-receipt"></i>
        <p>@lang('sidebar.expenses_revenues')</p>
    </a>
</li>
@endif 