@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">دليل الحسابات (شجرة تفاعلية)</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card mt-3">
                <div class="card-body">
                    <div id="accountTree"></div>
                    <pre>{{ json_encode($accounts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
        </div>
    </section>

</div>

<!-- jsTree CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css" />

<!-- jQuery & jsTree JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>

<script>
    // تحويل بيانات الحسابات إلى صيغة jsTree
    const accounts = @json($accounts);

    function buildTree(nodes) {
        return nodes.map(node => {
            let item = {
                id: node.id,
                text: node.name + (node.code ? ' <span style=\'color:#888;font-size:12px\'>['+node.code+']</span>' : ''),
                children: node.children_recursive && node.children_recursive.length > 0 ? buildTree(node.children_recursive) : [],
                icon: node.is_group ? 'fas fa-folder' : 'fas fa-file-alt',
                state: { opened: true }
            };
            return item;
        });
    }

    $(function() {
        // بناء الشجرة
        $('#accountTree').jstree({
            'core' : {
                'data' : buildTree(accounts),
                'themes': { 'variant': 'large' }
            },
            'plugins' : [ 'wholerow' ],
            'types' : {
                'default' : { 'icon' : 'fas fa-folder' },
                'file' : { 'icon' : 'fas fa-file-alt' }
            }
        });
    });
</script>

<style>
#accountTree {
    direction: rtl;
    font-size: 16px;
}
.jstree-default .jstree-anchor {
    font-weight: 500;
}
</style>

@endsection
