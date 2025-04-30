<div class="transaction-item border p-3 mb-2">
    <div class="form-row">
        <div class="form-group col-md-4">
            <label>الحساب الرئيسي (الصندوق)</label>
            <select name="transactions[{{ $index }}][account_id]" class="form-control" required>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-4">
            <label>الحساب المستهدف</label>
            <select name="transactions[{{ $index }}][target_account_id]" class="form-control" required>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-2">
            <label>المبلغ</label>
            <input type="number" step="0.01" name="transactions[{{ $index }}][amount]" class="form-control" required>
        </div>

        <div class="form-group col-md-1">
            <label>العملة</label>
            <select name="transactions[{{ $index }}][currency]" class="form-control" required>
                @foreach($currencies as $currency)
                    <option value="{{ $currency->code }}">{{ $currency->name }} ({{ $currency->code }})</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-1">
            <label>سعر الصرف</label>
            <input type="number" step="0.000001" name="transactions[{{ $index }}][exchange_rate]" value="1" class="form-control" required>
        </div>
    </div>

    <div class="form-group">
        <label>وصف الحركة</label>
        <textarea name="transactions[{{ $index }}][description]" class="form-control"></textarea>
    </div>

    @if($index > 0)
    <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()">حذف الحركة</button>
    @endif
</div>
