@extends('layouts.app')
@section('content')
<div class="container">
    <h2 class="mb-4">@lang('messages.upload_new_language',[],'en')</h2>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('languages.upload') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group mb-3">
            <label for="code">@lang('messages.language_code',[],'en')</label>
            <input type="text" name="code" id="code" class="form-control" maxlength="2" required placeholder="مثال: fr">
        </div>
        <div class="form-group mb-3">
            <label for="messages">@lang('messages.language_file',[],'en') (messages.php)</label>
            <input type="file" name="messages" id="messages" class="form-control" accept=".php" required>
        </div>
        <button type="submit" class="btn btn-primary">@lang('messages.upload',[],'en')</button>
        <a href="{{ route('languages.index') }}" class="btn btn-secondary ms-2">@lang('messages.back',[],'en')</a>
    </form>
</div>
@endsection 