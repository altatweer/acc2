<li>
    {{ $account->name }}
    @if($account->children->count() > 0)
        <ul>
            @foreach($account->children as $child)
                @include('accounts.tree_node', ['account' => $child])
            @endforeach
        </ul>
    @endif
</li>
