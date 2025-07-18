/**
 * تحسين عرض العملات في نماذج إضافة وتعديل الحسابات
 * Account Currency Display Enhancement
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // إضافة أيقونات العملة إلى القوائم المنسدلة
    function enhanceCurrencySelects() {
        const currencySelects = document.querySelectorAll('.currency-enhanced-select');
        
        currencySelects.forEach(select => {
            const options = select.querySelectorAll('option[data-currency]');
            
            options.forEach(option => {
                const currency = option.dataset.currency;
                let icon = '';
                
                // إضافة أيقونة حسب العملة
                switch(currency) {
                    case 'IQD':
                        icon = '🪙 ';
                        break;
                    case 'USD':
                        icon = '💵 ';
                        break;
                    case 'EUR':
                        icon = '💶 ';
                        break;
                    default:
                        icon = '💰 ';
                }
                
                // إضافة الأيقونة إذا لم تكن موجودة
                if (!option.textContent.includes(icon)) {
                    option.textContent = icon + option.textContent;
                }
            });
        });
    }
    
    // تجميع الخيارات حسب العملة
    function groupOptionsByCurrency() {
        const selects = document.querySelectorAll('.currency-enhanced-select');
        
        selects.forEach(select => {
            const options = Array.from(select.querySelectorAll('option[data-currency]'));
            const emptyOption = select.querySelector('option[value=""]');
            
            // تجميع الخيارات حسب العملة
            const groupedOptions = {};
            
            options.forEach(option => {
                const currency = option.dataset.currency || 'other';
                if (!groupedOptions[currency]) {
                    groupedOptions[currency] = [];
                }
                groupedOptions[currency].push(option);
            });
            
            // إعادة ترتيب الخيارات
            // مسح الخيارات الحالية (عدا الخيار الفارغ)
            options.forEach(option => option.remove());
            
            // إضافة الخيارات مجمعة حسب العملة
            Object.keys(groupedOptions).sort().forEach(currency => {
                // إضافة عنوان للمجموعة
                if (currency && currency !== 'other') {
                    const groupLabel = document.createElement('option');
                    groupLabel.disabled = true;
                    groupLabel.style.fontWeight = 'bold';
                    groupLabel.style.backgroundColor = '#f8f9fa';
                    groupLabel.style.color = '#6c757d';
                    groupLabel.textContent = `──── ${getCurrencyName(currency)} ────`;
                    select.appendChild(groupLabel);
                }
                
                // إضافة خيارات هذه المجموعة
                groupedOptions[currency].forEach(option => {
                    select.appendChild(option);
                });
            });
        });
    }
    
    // الحصول على اسم العملة بالعربية
    function getCurrencyName(code) {
        const names = {
            'IQD': 'الدينار العراقي',
            'USD': 'الدولار الأمريكي',
            'EUR': 'اليورو الأوروبي'
        };
        return names[code] || code;
    }
    
    // إضافة تأثيرات بصرية عند التفاعل
    function addInteractiveEffects() {
        const selects = document.querySelectorAll('.currency-enhanced-select');
        
        selects.forEach(select => {
            // تأثير عند الفتح
            select.addEventListener('focus', function() {
                this.style.transform = 'scale(1.02)';
                this.style.transition = 'transform 0.2s ease';
            });
            
            // إزالة التأثير عند الإغلاق
            select.addEventListener('blur', function() {
                this.style.transform = 'scale(1)';
            });
            
            // تحديث العملة المختارة
            select.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const currency = selectedOption.dataset.currency;
                
                if (currency) {
                    // إضافة كلاس للعملة المختارة
                    this.className = this.className.replace(/selected-currency-\w+/g, '');
                    this.classList.add(`selected-currency-${currency.toLowerCase()}`);
                    
                    // إظهار رسالة تأكيد
                    showCurrencyConfirmation(currency, selectedOption.textContent);
                }
            });
        });
    }
    
    // إظهار رسالة تأكيد اختيار العملة
    function showCurrencyConfirmation(currency, categoryName) {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #4ade80, #22c55e);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            font-size: 14px;
            font-weight: 500;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;
        
        const currencyNames = {
            'IQD': 'الدينار العراقي',
            'USD': 'الدولار الأمريكي',
            'EUR': 'اليورو الأوروبي'
        };
        
        notification.innerHTML = `
            <i class="fas fa-check-circle" style="margin-left: 8px;"></i>
            تم اختيار فئة ${currencyNames[currency] || currency}
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
    
    // إضافة مؤشر لعدد الحسابات لكل عملة
    function addAccountCountIndicator() {
        const selects = document.querySelectorAll('.currency-enhanced-select');
        
        selects.forEach(select => {
            const options = select.querySelectorAll('option[data-currency]');
            const counts = {};
            
            // حساب عدد الحسابات لكل عملة
            options.forEach(option => {
                const currency = option.dataset.currency;
                counts[currency] = (counts[currency] || 0) + 1;
            });
            
            // إضافة العدد إلى نص الخيار
            options.forEach(option => {
                const currency = option.dataset.currency;
                const count = counts[currency];
                
                if (count > 1 && !option.textContent.includes(`(${count})`)) {
                    const currentText = option.textContent;
                    const currencyBadge = option.querySelector('.currency-badge') || 
                                         currentText.match(/\b[A-Z]{3}\b/g)?.[0];
                    
                    if (currencyBadge) {
                        option.innerHTML = currentText.replace(
                            currencyBadge,
                            `${currencyBadge} <small style="opacity: 0.7;">(${count} فئات)</small>`
                        );
                    }
                }
            });
        });
    }
    
    // تشغيل جميع التحسينات
    enhanceCurrencySelects();
    addInteractiveEffects();
    
    // تطبيق التحسينات عند تحميل محتوى جديد (AJAX)
    if (typeof MutationObserver !== 'undefined') {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    enhanceCurrencySelects();
                    addInteractiveEffects();
                }
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    // إضافة دعم لـ Select2 إذا كان متوفراً
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.currency-enhanced-select').select2({
            templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }
                
                const currency = $(data.element).data('currency');
                let icon = '';
                
                switch(currency) {
                    case 'IQD':
                        icon = '<i class="currency-icon currency-icon-iqd">🪙</i>';
                        break;
                    case 'USD':
                        icon = '<i class="currency-icon currency-icon-usd">💵</i>';
                        break;
                    case 'EUR':
                        icon = '<i class="currency-icon currency-icon-eur">💶</i>';
                        break;
                }
                
                return $(`<span>${icon} ${data.text}</span>`);
            }
        });
    }
    
    console.log('✅ Account Currency Enhancement loaded successfully');
}); 