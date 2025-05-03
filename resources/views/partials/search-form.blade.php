<form method="GET" class="form-inline mb-3" id="searchForm">
    <div class="form-group mr-2">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="بحث...">
    </div>
    <div class="form-group mr-2">
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" placeholder="من تاريخ">
    </div>
    <div class="form-group mr-2">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" placeholder="إلى تاريخ">
    </div>
    @isset($currencies)
    <div class="form-group mr-2">
        <select name="currency" class="form-control">
            <option value="">كل العملات</option>
            @foreach($currencies as $currency)
                <option value="{{ $currency->code }}" {{ request('currency') == $currency->code ? 'selected' : '' }}>{{ $currency->code }}</option>
            @endforeach
        </select>
    </div>
    @endisset
    <button type="submit" class="btn btn-primary">بحث</button>
    <a href="?" class="btn btn-secondary ml-2">إعادة تعيين</a>
</form> 