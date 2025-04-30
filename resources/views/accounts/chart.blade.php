@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">دليل الحسابات (عرض شجري)</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card mt-3">
                <div class="card-body">
                    <ul id="accountTree">
                        @foreach($accounts as $account)
                            <li>
                                <strong>{{ $account->name }}</strong>
                                @if($account->children->count() > 0)
                                    <ul>
                                        @foreach($account->children as $child)
                                            @include('accounts.tree_node', ['account' => $child])
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

</div>

<style>
#accountTree, #accountTree ul {
    list-style: none;
    padding-left: 20px;
}

#accountTree li {
    margin: 5px 0;
    font-size: 16px;
}

#accountTree li strong {
    color: #007bff;
}
</style>

@endsection
