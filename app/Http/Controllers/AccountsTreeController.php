<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Exports\AccountsTreeExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AccountsTreeController extends Controller
{
    /**
     * عرض صفحة شجرة الحسابات مع خيارات التصدير
     */
    public function index()
    {
        // إحصائيات سريعة
        $stats = [
            'total_accounts' => Account::where('is_group', 0)->count(),
            'total_groups' => Account::where('is_group', 1)->count(),
            'active_accounts' => Account::where('is_group', 0)->count(), // جميع الحسابات نشطة
            'inactive_accounts' => 0, // لا توجد حسابات غير نشطة
            'by_type' => Account::selectRaw('type, COUNT(*) as count')
                            ->groupBy('type')
                            ->pluck('count', 'type')
                            ->toArray()
        ];

        return view('accounts.tree_export', compact('stats'));
    }

    /**
     * تصدير شجرة الحسابات إلى Excel
     */
    public function exportToExcel(Request $request)
    {
        // لا نحتاج لمعامل includeInactive لأن جميع الحسابات نشطة
        
        $filename = 'شجرة_الحسابات_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(
            new AccountsTreeExport(), 
            $filename,
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    /**
     * معاينة شجرة الحسابات في المتصفح
     */
    public function preview(Request $request)
    {
        // استخدام نفس منطق العرض من Export
        $export = new AccountsTreeExport();
        
        return $export->view();
    }

    /**
     * الحصول على شجرة الحسابات كـ JSON
     */
    public function getTreeData(Request $request)
    {
        $query = Account::with(['children', 'parent']);
        
        // جميع الحسابات نشطة - لا نحتاج لفلترة
        
        $accounts = $query->orderBy('code')->get();
        
        // بناء الشجرة الهرمية
        $tree = $this->buildTreeStructure($accounts);
        
        return response()->json([
            'success' => true,
            'data' => $tree,
            'stats' => [
                'total_items' => $accounts->count(),
                'groups' => $accounts->where('is_group', 1)->count(),
                'accounts' => $accounts->where('is_group', 0)->count(),
            ]
        ]);
    }

    /**
     * بناء هيكل الشجرة
     */
    private function buildTreeStructure($accounts)
    {
        $tree = [];
        $lookup = [];
        
        // إنشاء lookup array
        foreach ($accounts as $account) {
            $lookup[$account->id] = [
                'id' => $account->id,
                'code' => $account->code,
                'name' => $account->name,
                'type' => $account->type,
                'is_group' => $account->is_group,
                'currency' => $account->default_currency,
                'parent_id' => $account->parent_id,
                'children' => []
            ];
        }
        
        // بناء الشجرة
        foreach ($lookup as $id => $account) {
            if ($account['parent_id'] === null) {
                $tree[] = &$lookup[$id];
            } else {
                if (isset($lookup[$account['parent_id']])) {
                    $lookup[$account['parent_id']]['children'][] = &$lookup[$id];
                }
            }
        }
        
        return $tree;
    }

    /**
     * طباعة شجرة الحسابات
     */
    public function printTree(Request $request)
    {
        // استخدام نفس منطق العرض من Export
        $export = new AccountsTreeExport();
        $viewData = $export->view()->getData();
        
        return view('accounts.tree_print', $viewData);
    }
} 