import './bootstrap';

// حماية النماذج من إعادة الإرسال
document.addEventListener('DOMContentLoaded', function() {
    // ابحث عن جميع النماذج وأضف حماية ضد التكرار
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            // تحقق مما إذا كان النموذج قد تم تقديمه بالفعل
            if (this.hasAttribute('data-submitted')) {
                e.preventDefault();
                return;
            }
            
            // تعطيل جميع أزرار التقديم
            this.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(button => {
                button.disabled = true;
                
                // إضافة نص "جارٍ الحفظ..." للأزرار
                if (button.tagName === 'BUTTON') {
                    button.setAttribute('data-original-text', button.innerHTML);
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جارٍ الحفظ...';
                }
            });
            
            // وضع علامة على النموذج بأنه تم تقديمه
            this.setAttribute('data-submitted', 'true');
        });
    });
});
