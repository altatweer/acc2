/**
 * Professional Print System for Accounting System
 * Handles printing functionality for vouchers, invoices, and salary batches
 */

class PrintSystem {
    constructor() {
        this.init();
    }

    init() {
        this.setupPrintButtons();
        this.setupPrintStyles();
        this.handlePrintEvents();
    }

    setupPrintButtons() {
        // Add print functionality to existing print buttons
        document.querySelectorAll('.print-btn, [data-print]').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                this.handlePrint(button);
            });
        });
    }

    setupPrintStyles() {
        // Inject print-specific styles
        const printStyles = `
            <style id="print-system-styles">
                @media print {
                    /* Hide all non-essential elements */
                    .navbar, .sidebar, .footer, .no-print,
                    .btn, .alert, .breadcrumb, .pagination,
                    .modal, .dropdown-menu, .tooltip, .popover {
                        display: none !important;
                    }
                    
                    /* Optimize page layout */
                    body {
                        font-family: 'Cairo', 'Tajawal', Arial, sans-serif !important;
                        font-size: 12pt !important;
                        line-height: 1.4 !important;
                        color: #000 !important;
                        background: #fff !important;
                    }
                    
                    /* Table optimizations */
                    table {
                        border-collapse: collapse !important;
                        width: 100% !important;
                    }
                    
                    th, td {
                        border: 1px solid #000 !important;
                        padding: 8px !important;
                        font-size: 11pt !important;
                    }
                    
                    /* Page breaks */
                    .page-break-before { page-break-before: always; }
                    .page-break-after { page-break-after: always; }
                    .page-break-inside-avoid { page-break-inside: avoid; }
                    
                    /* Header and footer */
                    .print-header { margin-bottom: 20px; }
                    .print-footer { margin-top: 20px; }
                }
            </style>
        `;
        
        if (!document.getElementById('print-system-styles')) {
            document.head.insertAdjacentHTML('beforeend', printStyles);
        }
    }

    handlePrint(button) {
        const printUrl = button.getAttribute('href') || button.getAttribute('data-print-url');
        
        if (printUrl) {
            // Open print page in new window
            this.openPrintWindow(printUrl);
        } else {
            // Print current page
            this.printCurrentPage();
        }
    }

    openPrintWindow(url) {
        const printWindow = window.open(url, 'print-window', 
            'width=1024,height=768,scrollbars=yes,resizable=yes');
        
        if (printWindow) {
            printWindow.onload = () => {
                setTimeout(() => {
                    printWindow.print();
                }, 500);
            };
        }
    }

    printCurrentPage() {
        // Prepare page for printing
        this.preparePrintPage();
        
        // Print
        window.print();
        
        // Restore page after printing
        setTimeout(() => {
            this.restorePage();
        }, 1000);
    }

    preparePrintPage() {
        // Hide non-essential elements
        document.querySelectorAll('.no-print, .btn, .alert, .breadcrumb').forEach(el => {
            el.style.display = 'none';
        });
        
        // Add print-ready class
        document.body.classList.add('print-ready');
    }

    restorePage() {
        // Show hidden elements
        document.querySelectorAll('.no-print, .btn, .alert, .breadcrumb').forEach(el => {
            el.style.display = '';
        });
        
        // Remove print-ready class
        document.body.classList.remove('print-ready');
    }

    handlePrintEvents() {
        // Before print event
        window.addEventListener('beforeprint', () => {
            this.onBeforePrint();
        });
        
        // After print event
        window.addEventListener('afterprint', () => {
            this.onAfterPrint();
        });
    }

    onBeforePrint() {
        // Add any pre-print preparations
        document.body.classList.add('printing');
        
        // Optimize tables for printing
        this.optimizeTablesForPrint();
    }

    onAfterPrint() {
        // Clean up after printing
        document.body.classList.remove('printing');
    }

    optimizeTablesForPrint() {
        document.querySelectorAll('table').forEach(table => {
            // Ensure table headers repeat on each page
            const thead = table.querySelector('thead');
            if (thead) {
                thead.style.display = 'table-header-group';
            }
            
            // Prevent table rows from breaking across pages
            table.querySelectorAll('tr').forEach(row => {
                row.style.pageBreakInside = 'avoid';
            });
        });
    }

    // Utility methods for specific document types
    printVoucher(voucherId) {
        const url = `/vouchers/${voucherId}/print`;
        this.openPrintWindow(url);
    }

    printInvoice(invoiceId) {
        const url = `/invoices/${invoiceId}/print`;
        this.openPrintWindow(url);
    }

    printSalaryBatch(batchId) {
        const url = `/salary-batches/${batchId}/print`;
        this.openPrintWindow(url);
    }

    // Print preview functionality
    showPrintPreview(content) {
        const previewWindow = window.open('', 'print-preview', 
            'width=1024,height=768,scrollbars=yes,resizable=yes');
        
        const previewHTML = `
            <!DOCTYPE html>
            <html dir="rtl" lang="ar">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>معاينة الطباعة</title>
                <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
                <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
                <style>
                    body {
                        font-family: 'Cairo', Arial, sans-serif;
                        direction: rtl;
                        margin: 0;
                        padding: 20px;
                        background: #f5f5f5;
                    }
                    .preview-container {
                        background: white;
                        padding: 40px;
                        box-shadow: 0 0 20px rgba(0,0,0,0.1);
                        max-width: 800px;
                        margin: 0 auto;
                    }
                    .preview-actions {
                        text-align: center;
                        margin-bottom: 20px;
                    }
                    .btn {
                        padding: 10px 20px;
                        margin: 0 10px;
                        border: none;
                        border-radius: 5px;
                        cursor: pointer;
                        font-family: 'Cairo', Arial, sans-serif;
                    }
                    .btn-primary {
                        background: #007bff;
                        color: white;
                    }
                    .btn-secondary {
                        background: #6c757d;
                        color: white;
                    }
                    @media print {
                        .preview-actions { display: none; }
                        .preview-container { box-shadow: none; padding: 0; }
                        body { background: white; }
                    }
                </style>
            </head>
            <body>
                <div class="preview-actions">
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> طباعة
                    </button>
                    <button class="btn btn-secondary" onclick="window.close()">
                        <i class="fas fa-times"></i> إغلاق
                    </button>
                </div>
                <div class="preview-container">
                    ${content}
                </div>
            </body>
            </html>
        `;
        
        previewWindow.document.write(previewHTML);
        previewWindow.document.close();
    }
}

// Initialize print system when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.printSystem = new PrintSystem();
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PrintSystem;
} 