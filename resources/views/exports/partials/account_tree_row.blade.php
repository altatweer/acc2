{{-- عرض الحساب أو المجموعة الحالية --}}
<tr class="level-{{ $node['level'] }} {{ $node['account']->is_group ? 'account-group' : 'account-item' }}">
    <td style="text-align: center;">
        <strong>{{ $node['level'] }}</strong>
        @if($node['level'] > 1)
            @for($i = 1; $i < $node['level']; $i++)
                <span style="color: #ccc;">└</span>
            @endfor
        @endif
    </td>
    
    <td class="code">{{ $node['account']->code }}</td>
    
    <td style="padding-right: {{ ($node['level'] - 1) * 20 }}px;">
        @if($node['account']->is_group)
            <strong>📁 {{ $node['account']->name }}</strong>
        @else
            📄 {{ $node['account']->name }}
        @endif
    </td>
    
    <td style="text-align: center;">
        @if($node['account']->type)
            @php
                $typeNames = [
                    'asset' => 'أصول',
                    'liability' => 'خصوم', 
                    'equity' => 'حقوق ملكية',
                    'revenue' => 'إيرادات',
                    'expense' => 'مصروفات'
                ];
                $typeName = $typeNames[$node['account']->type] ?? $node['account']->type;
                $typeClass = 'type-' . $node['account']->type;
            @endphp
            <span class="type-badge {{ $typeClass }}">{{ $typeName }}</span>
        @else
            <span style="color: #999;">غير محدد</span>
        @endif
    </td>
    
    <td style="text-align: center;">
        @if($node['account']->is_group)
            <span style="color: #007bff; font-weight: bold;">مجموعة</span>
        @else
            <span style="color: #28a745;">حساب</span>
        @endif
    </td>
    
    <td style="text-align: center;">
        @if($node['account']->default_currency)
            <strong>{{ $node['account']->default_currency }}</strong>
        @else
            <span style="color: #999;">افتراضي</span>
        @endif
    </td>
</tr>

{{-- عرض الحسابات الفرعية --}}
@if(!empty($node['children']))
    @foreach($node['children'] as $child)
        @include('exports.partials.account_tree_row', ['node' => $child])
    @endforeach
@endif 