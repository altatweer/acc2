@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title">@lang('messages.items_list')</h3>
                <div class="card-tools">
                    @php $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin(); @endphp
                    @if($isSuperAdmin || auth()->user()->can('إضافة عنصر'))
                    <a href="{{ route('items.create') }}" class="btn btn-sm btn-success">@lang('messages.new_item')</a>
                    @endif
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
                </div>
            </div>
            <div class="card-body p-3">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="@lang('messages.close')"><span aria-hidden="true">&times;</span></button>
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-center mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('messages.item_name')</th>
                                <th>@lang('messages.item_type')</th>
                                <th>@lang('messages.unit_price')</th>
                                <th>@lang('messages.item_description')</th>
                                <th>@lang('messages.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                            <tr>
                                <td>{{ $items->firstItem() + $loop->index }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->type == 'product' ? __('messages.product') : __('messages.service') }}</td>
                                <td>{{ number_format($item->unit_price,2) }}</td>
                                <td>{{ $item->description }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        @if($isSuperAdmin || auth()->user()->can('عرض العناصر'))
                                        <a href="{{ Route::localizedRoute('items.show', ['item' => $item, ]) }}" class="btn btn-outline-info" title="@lang('messages.view')"><i class="fas fa-eye"></i></a>
                                        @endif
                                        @if($isSuperAdmin || auth()->user()->can('تعديل عنصر'))
                                        <a href="{{ Route::localizedRoute('items.edit', ['item' => $item, ]) }}" class="btn btn-outline-primary" title="@lang('messages.edit')"><i class="fas fa-edit"></i></a>
                                        @endif
                                        @if($isSuperAdmin || auth()->user()->can('حذف عنصر'))
                                        <form action="{{ Route::localizedRoute('items.destroy', ['item' => $item, ]) }}" method="POST" onsubmit="return confirm('@lang('messages.delete_item_confirm')');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="@lang('messages.delete')"><i class="fas fa-trash"></i></button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix d-flex justify-content-between align-items-center">
                <div>@lang('messages.total_items') <strong>{{ $items->total() }}</strong></div>
                <div>{{ $items->appends(['lang' => app()->getLocale()])->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection 