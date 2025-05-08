@extends('layouts.app')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">@lang('messages.create_invoice')</h1>
      </div>
      <div class="col-sm-6 text-left">
        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">@lang('messages.back_to_invoices')</a>
      </div>
    </div>
  </div>
</div>
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-10 offset-lg-1 col-md-12">
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">@lang('messages.invoice_data')</h3>
          </div>
          <form action="{{ route('invoices.store') }}" method="POST">
            @csrf
            <div class="card-body">
              @if($errors->any())
                <div class="alert alert-danger">
                  <ul class="mb-0">
                    @foreach($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>@lang('messages.invoice_number_optional')</label>
                  <input type="text" name="invoice_number" value="{{ old('invoice_number') }}" class="form-control">
                </div>
                <div class="form-group col-md-6">
                  <label>@lang('messages.customer')</label>
                  <select name="customer_id" class="form-control select2" required>
                    <option value="" disabled selected>-- @lang('messages.select_customer') --</option>
                    @foreach($customers as $cust)
                      <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>{{ $cust->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-4">
                  <label>@lang('messages.invoice_date')</label>
                  <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                </div>
                <div class="form-group col-md-4">
                  <label>@lang('messages.currency')</label>
                  <select name="currency" id="currency" class="form-control select2" required>
                    <option value="" disabled {{ old('currency') ? '' : 'selected' }}>-- @lang('messages.select_currency') --</option>
                    @foreach($currencies as $cur)
                      <option value="{{ $cur->code }}" {{ old('currency') == $cur->code ? 'selected' : '' }}>{{ $cur->code }} - {{ $cur->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group col-md-4">
                  <label>@lang('messages.exchange_rate')</label>
                  <input type="text" id="exchange_rate_display" class="form-control" disabled>
                  <input type="hidden" name="exchange_rate" id="exchange_rate">
                </div>
              </div>
              <hr>
              <!-- Invoice Total Summary -->
              <div class="row mb-3">
                <div class="col text-right">
                  <h4>@lang('messages.invoice_total'): <span id="invoice_total_display" class="badge badge-primary p-2" style="font-size:1.2rem;">0.00</span></h4>
                </div>
              </div>
              <input type="hidden" name="total" id="invoice_total" value="0.00">
              <h5 class="mb-3">@lang('messages.invoice_items')</h5>
              <div class="table-responsive">
                <table class="table table-bordered" id="invoice_items_table">
                  <thead>
                    <tr>
                      <th>@lang('messages.item')</th>
                      <th>@lang('messages.quantity')</th>
                      <th>@lang('messages.unit_price')</th>
                      <th>@lang('messages.line_total')</th>
                      <th width="100">@lang('messages.action')</th>
                    </tr>
                  </thead>
                  <tbody id="invoice_items_body">
                    <tr>
                      <td>
                        <select name="items[0][item_id]" class="form-control select2 items-select" required>
                          <option value="" disabled selected>-- @lang('messages.select_item') --</option>
                          @foreach($items as $itm)
                            <option value="{{ $itm->id }}" data-price="{{ $itm->unit_price }}">{{ $itm->name }} ({{ $itm->type }})</option>
                          @endforeach
                        </select>
                      </td>
                      <td><input type="number" name="items[0][quantity]" value="1" class="form-control item-quantity" min="1" step="1" required></td>
                      <td><input type="number" name="items[0][unit_price]" value="" class="form-control item-price" step="0.01" required></td>
                      <td><input type="number" name="items[0][line_total]" value="" class="form-control item-total" step="0.01" readonly></td>
                      <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-trash"></i></button></td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <button type="button" id="add_item" class="btn btn-secondary btn-sm mb-3"><i class="fas fa-plus"></i> @lang('messages.add_item')</button>
            </div>

            <div class="card-footer text-right">
              <button type="submit" class="btn btn-primary">@lang('messages.save_invoice')</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function(){
    // Initialize select2
    $('.select2').select2({ theme:'bootstrap4' });
    // Prepare exchange rate mapping
    const rates = @json($currencies->pluck('exchange_rate', 'code'));
    // Update exchange rate when currency changes
    $('select[name="currency"]').on('change', function(){
        const code = $(this).val();
        const rate = rates[code] || 1;
        $('#exchange_rate_display').val(rate.toFixed(6));
        $('#exchange_rate').val(rate);
    }).trigger('change');
    // Function to update invoice total
    function updateInvoiceTotal(){
        let total = 0;
        $('.item-total').each(function(){ total += parseFloat($(this).val()) || 0; });
        total = total.toFixed(2);
        let currencyCode = $('#currency').val() || '';
        $('#invoice_total_display').text(total + ' ' + currencyCode);
        $('#invoice_total').val(total);
    }
    // Dynamic invoice items
    let idx = $('#invoice_items_body tr').length;
    let $template = $('#invoice_items_body tr:first').clone();
    $template.find('select').val('');
    $template.find('input').val('');
    $('#add_item').click(function(){
      let $row = $template.clone();
      $row.find('[name]').each(function(){
        let name = $(this).attr('name');
        let newName = name.replace(/\[\d+\]/, '['+ idx +']');
        $(this).attr('name', newName);
      });
      $row.find('select.select2').select2({theme:'bootstrap4'});
      $('#invoice_items_body').append($row);
      idx++;
    });
    $(document).on('click', '.remove-item', function(){
      if($('#invoice_items_body tr').length > 1){
        $(this).closest('tr').remove();
      }
    });
    // Update line total
    $(document).on('change', '.items-select', function(){
      let price = parseFloat($(this).find('option:selected').data('price')) || 0;
      let $r = $(this).closest('tr');
      $r.find('.item-price').val(price.toFixed(2));
      updateTotal($r);
    });
    $(document).on('input', '.item-quantity, .item-price', function(){
      updateTotal($(this).closest('tr'));
    });
    function updateTotal($r){
      let q = parseFloat($r.find('.item-quantity').val()) || 0;
      let p = parseFloat($r.find('.item-price').val()) || 0;
      $r.find('.item-total').val((q*p).toFixed(2));
      updateInvoiceTotal();
    }
    // initialize first row
    $('#invoice_items_body tr:first .items-select').trigger('change');
    updateInvoiceTotal();
});
</script>
@endpush 