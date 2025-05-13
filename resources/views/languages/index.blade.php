@extends('layouts.app')
@section('content')
<div class="container">
    <h2 class="mb-4">@lang('messages.languages_management',[],'en')</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>@lang('messages.language_code',[],'en')</th>
                <th>@lang('messages.has_messages_file',[],'en')</th>
                <th>@lang('messages.actions',[],'en')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($languages as $lang)
                <tr>
                    <td>{{ $lang['code'] }}</td>
                    <td>{{ $lang['has_messages'] ? '✔' : '✖' }}</td>
                    <td>
                        @if($lang['has_messages'])
                            <a href="{{ route('languages.download', $lang['code']) }}" class="btn btn-sm btn-primary">@lang('messages.download',[],'en')</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('languages.uploadForm') }}" class="btn btn-success mt-3">@lang('messages.upload_new_language',[],'en')</a>
</div>
@endsection 