@extends('layouts.app')

@section('content')
<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>@lang('messages.account_groups_management_title')</h1>
            </div>
            <div class="col-sm-6 text-left">
                <a href="{{ route('accounts.createGroup') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-plus-circle"></i> @lang('messages.add_new_category')
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">@lang('messages.categories_list')</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="@lang('messages.close')">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-striped table-hover text-center mb-0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width:60px;">#</th>
                                        <th>@lang('messages.category_code')</th>
                                        <th>@lang('messages.category_name')</th>
                                        <th>@lang('messages.account_type')</th>
                                        <th>@lang('messages.parent_category')</th>
                                        <th style="width:140px;">@lang('messages.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($categories as $index => $category)
                                        <tr>
                                            <td>{{ $categories->firstItem() + $index }}</td>
                                            <td>{{ $category->code }}</td>
                                            <td class="text-left">{{ $category->name }}</td>
                                            <td>
                                                @if($category->type)
                                                    <span class="badge badge-info">@lang('messages.type_'.$category->type)</span>
                                                @else
                                                    <span class="badge badge-secondary">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $category->parent->name ?? '-' }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('accounts.edit', $category) }}" class="btn btn-outline-primary" title="@lang('messages.edit')">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('accounts.destroy', $category) }}" method="POST" onsubmit="return confirm('@lang('messages.delete_confirmation_account')');" style="display:inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" title="@lang('messages.delete')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="py-4">@lang('messages.no_categories_to_display')</td>
                                        </tr>
                                        <tr style="display:none;"><td colspan="6"></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer clearfix d-flex justify-content-between align-items-center">
                        <div>@lang('messages.total_categories') <strong>{{ $categories->total() }}</strong></div>
                        <div>{{ $categories->appends(['locale' => app()->getLocale()])->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
