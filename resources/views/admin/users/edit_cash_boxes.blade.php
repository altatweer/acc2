@extends('layouts.app')
@section('content')
<div class="container">
    <h3>تحديد الصناديق النقدية للموظف: {{ $user->name }}</h3>
    <form method="POST" action="{{ route('admin.users.cash_boxes.update', $user->id) }}">
        @csrf
        <div class="form-group">
            @foreach($cashBoxes as $box)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="cash_boxes[]" value="{{ $box->id }}"
                        {{ in_array($box->id, $userCashBoxes) ? 'checked' : '' }}>
                    <label class="form-check-label">{{ $box->name }} ({{ $box->currency }})</label>
                </div>
            @endforeach
        </div>
        <button type="submit" class="btn btn-primary">حفظ</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">رجوع</a>
    </form>
</div>
@endsection 