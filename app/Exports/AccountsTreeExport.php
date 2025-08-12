<?php

namespace App\Exports;

use App\Models\Account;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AccountsTreeExport implements FromView, WithStyles, ShouldAutoSize
{
    protected $accounts;

    public function __construct()
    {
        $this->accounts = $this->buildAccountsTree();
    }

    private function buildAccountsTree()
    {
        // الحصول على جميع الحسابات
        $query = Account::with(['children.children.children', 'parent']);
        
        // لا نحتاج لفلترة is_active لأن العمود غير موجود
        // جميع الحسابات تُعتبر نشطة افتراضياً
        
        $allAccounts = $query->orderBy('code')->get();
        
        // بناء شجرة هرمية
        $tree = [];
        
        // الحسابات الرئيسية (المستوى الأول)
        $mainCategories = $allAccounts->where('parent_id', null)->where('is_group', 1);
        
        foreach ($mainCategories as $main) {
            $mainData = [
                'level' => 1,
                'account' => $main,
                'children' => []
            ];
            
            // المستوى الثاني
            $level2 = $allAccounts->where('parent_id', $main->id)->where('is_group', 1);
            foreach ($level2 as $l2) {
                $l2Data = [
                    'level' => 2,
                    'account' => $l2,
                    'children' => []
                ];
                
                // المستوى الثالث
                $level3 = $allAccounts->where('parent_id', $l2->id)->where('is_group', 1);
                foreach ($level3 as $l3) {
                    $l3Data = [
                        'level' => 3,
                        'account' => $l3,
                        'children' => []
                    ];
                    
                    // الحسابات الفعلية في المستوى الثالث
                    $accounts3 = $allAccounts->where('parent_id', $l3->id)->where('is_group', 0);
                    foreach ($accounts3 as $acc) {
                        $l3Data['children'][] = [
                            'level' => 4,
                            'account' => $acc,
                            'children' => []
                        ];
                    }
                    
                    $l2Data['children'][] = $l3Data;
                }
                
                // الحسابات الفعلية في المستوى الثاني
                $accounts2 = $allAccounts->where('parent_id', $l2->id)->where('is_group', 0);
                foreach ($accounts2 as $acc) {
                    $l2Data['children'][] = [
                        'level' => 3,
                        'account' => $acc,
                        'children' => []
                    ];
                }
                
                $mainData['children'][] = $l2Data;
            }
            
            // الحسابات الفعلية في المستوى الأول
            $accounts1 = $allAccounts->where('parent_id', $main->id)->where('is_group', 0);
            foreach ($accounts1 as $acc) {
                $mainData['children'][] = [
                    'level' => 2,
                    'account' => $acc,
                    'children' => []
                ];
            }
            
            $tree[] = $mainData;
        }
        
        return $tree;
    }

    public function view(): View
    {
        return view('exports.accounts_tree', [
            'accountsTree' => $this->accounts,
            'generatedAt' => now(),
            'title' => 'شجرة الحسابات - نظام المحاسبة'
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // تنسيق الرأس
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'color' => ['rgb' => '1f4e79']
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center'
                ]
            ],
            // تنسيق عناوين الأعمدة
            3 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'color' => ['rgb' => '4472c4']
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center'
                ]
            ]
        ];
    }
} 